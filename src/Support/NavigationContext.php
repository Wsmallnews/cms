<?php

namespace Wsmallnews\Cms\Support;

use Illuminate\Support\Collection;
use Wsmallnews\Cms\Enums\NavigationType;
use Wsmallnews\Cms\Models\Navigation;
use Wsmallnews\Support\Features\Site\SiteContext;

/**
 * 导航上下文解析器：当前请求匹配的导航节点（含祖先链），驱动页面级装饰
 * （分区 banner 通栏、二级分组的兄弟列表）。
 *
 * 匹配规则（对 scope 内 normal 且可寻址的节点）：
 * - 精确匹配优先（path 相等；首页节点 path = / 仅精确匹配，避免吃掉全站前缀）
 * - 其次最长前缀匹配（当前 path 以「节点 path + /」开头，段边界对齐，防 /posts-x 误吃 /posts）
 * - 站外链接（Url 类型且 host 不属于本站）不参与匹配
 *
 * 同一 URL 被多个节点引用（如多个节点指向同一 Page）时取树序第一个。
 *
 * 生命周期：容器 scoped 实例（CmsServiceProvider 注册），每个请求一份，请求结束由
 * 框架释放——常驻进程（Octane / Swoole）下不会跨请求串站点或租户。缓存按「站点 +
 * 租户 + scope」分区，同一请求内切换上下文（SiteContext::run）也不会互相污染。
 * 只在初始页面请求里调用：Livewire update 的 path 是 /livewire/update。
 */
class NavigationContext
{
    /**
     * @var array<string, array{node: ?Navigation, ancestors: Collection<int, Navigation>}>
     */
    protected array $cache = [];

    /**
     * 解析当前请求匹配的导航节点（未命中返回 null）
     */
    public function current(string $scopeType, int | string $scopeId = 0): ?Navigation
    {
        return $this->resolve($scopeType, $scopeId)['node'];
    }

    /**
     * 当前请求的分区 banner 图 URL：匹配节点自身无图时向上继承最近的祖先
     */
    public function bannerUrl(string $scopeType, int | string $scopeId = 0): ?string
    {
        $resolved = $this->resolve($scopeType, $scopeId);

        $node = $resolved['node'];
        if (! $node) {
            return null;
        }

        // 自身 → 祖先（由近及远）中第一个设置了 banner 的节点
        return collect([$node, ...$resolved['ancestors']])
            ->map(fn (Navigation $candidate) => $candidate->getFirstMediaUrl('navigation_banner') ?: null)
            ->first(fn (?string $url) => filled($url));
    }

    /**
     * 当前请求是否应展示兄弟导航（骨架分栏判据）：匹配节点为二级或更深。
     * Brothers 组件 mount 与骨架共用本判断（逻辑单点，深度语义一致）
     */
    public function hasBrothers(string $scopeType, int | string $scopeId = 0): bool
    {
        $node = $this->resolve($scopeType, $scopeId)['node'];

        return filled($node) && (int) $node->depth >= 1;
    }

    /**
     * 清空本实例缓存（请求内更换站点后重解析、测试隔离用）
     */
    public function flush(): void
    {
        $this->cache = [];
    }

    /**
     * 解析并缓存：匹配节点 + 其 normal 祖先链（由近及远）
     *
     * @return array{node: ?Navigation, ancestors: Collection<int, Navigation>}
     */
    protected function resolve(string $scopeType, int | string $scopeId = 0): array
    {
        $key = $this->cacheKey($scopeType, $scopeId);

        if (! array_key_exists($key, $this->cache)) {
            $node = $this->matchNode($scopeType, $scopeId);

            $this->cache[$key] = [
                'node' => $node,
                'ancestors' => $node
                    ? $node->ancestors()->normal()->defaultOrder()->get()->reverse()->values()
                    : collect(),
            ];
        }

        return $this->cache[$key];
    }

    /**
     * 缓存分区：站点 + 租户 + scope。
     *
     * 非聚合站点的 scope 在注册期已保证全局唯一，站点 id 主要区分聚合站点与其
     * source（两者继承同一个 scopeable）；租户决定同一 scope 下的导航树，必须进 key。
     */
    protected function cacheKey(string $scopeType, int | string $scopeId): string
    {
        return implode('|', [
            app(SiteContext::class)->site()?->id() ?? '-',
            current_tenant()?->getKey() ?? '-',
            $scopeType,
            (string) $scopeId,
        ]);
    }

    /**
     * 在 scope 内的 normal 节点中寻找当前请求的最佳匹配
     *
     * 仅遍历主导航类型（scope 内第一个类型，与页头 Navigation 组件的默认解析一致），
     * 避免其他导航类型（页脚/测试树等）的节点参与匹配或触发其 URL 求值
     */
    protected function matchNode(string $scopeType, int | string $scopeId): ?Navigation
    {
        $type = Utils::getNavigationTypeModel()::query()->forScopeable(['scope_type' => $scopeType, 'scope_id' => $scopeId])
            ->orderBy('id')
            ->first();

        if (! $type) {
            return null;
        }

        $currentPath = '/' . trim(request()->getPathInfo(), '/');

        // 必须用 kalnoy 的 scoped()（而非 snScope）：withDepth 的深度子查询依赖 scoped 查询上下文，
        // 普通 where 过滤下深度恒为 -1，兄弟分组推导会失效
        $scoped = [
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
            'type_id' => $type->id,
        ];
        $scoped['team_id'] = current_tenant()?->getKey();

        $candidates = Utils::getNavigationModel()::scoped($scoped)
            ->normal()
            ->defaultOrder()
            ->withDepth()
            ->get();

        $best = null;
        $bestScore = 0;      // 2 = 精确，1 = 前缀
        $bestLength = 0;     // 前缀匹配取最长

        foreach ($candidates as $candidate) {
            $path = $this->nodePath($candidate);

            if ($path === null || $path === '') {
                continue;
            }

            if ($path === $currentPath) {
                // 精确匹配胜出；同 URL 命中父子多个节点时取更深者（用户点击的是具体菜单项，
                // 更深节点才能推导出兄弟分组）
                if ($bestScore < 2 || (int) $candidate->depth > (int) $best?->depth) {
                    $best = $candidate;
                    $bestScore = 2;
                }

                continue;
            }

            // 首页节点（path = /）只参与精确匹配，避免成为全站前缀
            if ($path === '/' || ! str_starts_with($currentPath, $path . '/')) {
                continue;
            }

            $length = strlen($path);
            if ($bestScore < 2 && ($best === null || $length > $bestLength)) {
                $best = $candidate;
                $bestScore = 1;
                $bestLength = $length;
            }
        }

        return $best;
    }

    /**
     * 节点可寻址的 path（相对站点的 URL path）；外域与不可寻址（null / '#'）返回 null
     */
    protected function nodePath(Navigation $node): ?string
    {
        $url = $node->url_info['url'] ?? null;

        if (blank($url) || $url === '#') {
            return null;
        }

        // 只有 Url 类型可能指向站外；Route / Page / Child 的 URL 由当前站点实时生成，
        // 恒为本站，host 不参与判断（换域名、别名域名下 path 仍然有效）
        if ($node->type === NavigationType::Url) {
            $host = parse_url($url, PHP_URL_HOST);

            if ($host !== null && $host !== request()->getHost()) {
                return null;
            }
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        return '/' . trim($path, '/');
    }
}
