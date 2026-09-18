<?php

namespace Wsmallnews\Cms\Support;

use Illuminate\Support\Collection;
use Wsmallnews\Cms\Models\Navigation;

/**
 * 导航上下文解析器：当前请求匹配的导航节点（含祖先链），驱动页面级装饰
 * （分区 banner 通栏、二级分组的兄弟列表）。
 *
 * 匹配规则（对 scope 内 normal 且可寻址的节点）：
 * - 精确匹配优先（path 相等；首页节点 path = / 仅精确匹配，避免吃掉全站前缀）
 * - 其次最长前缀匹配（当前 path 以「节点 path + /」开头，段边界对齐，防 /posts-x 误吃 /posts）
 * - 外域 URL（导航 Url 类型的站外链接）不参与匹配
 *
 * 同一 URL 被多个节点引用（如多个节点指向同一 Page）时取树序第一个。
 * 一次请求内静态缓存（scope 为 key），页头导航与骨架装饰共享同一次解析结果。
 */
class NavigationContext
{
    /**
     * @var array<string, array{node: ?Navigation, ancestors: Collection<int, Navigation>}>
     */
    protected static array $cache = [];

    /**
     * 解析当前请求匹配的导航节点（未命中返回 null）
     */
    public static function current(string $scopeType, int | string $scopeId = 0): ?Navigation
    {
        return self::resolve($scopeType, $scopeId)['node'];
    }

    /**
     * 当前请求的分区 banner 图 URL：匹配节点自身无图时向上继承最近的祖先
     */
    public static function bannerUrl(string $scopeType, int | string $scopeId = 0): ?string
    {
        $resolved = self::resolve($scopeType, $scopeId);

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
    public static function hasBrothers(string $scopeType, int | string $scopeId = 0): bool
    {
        $node = self::resolve($scopeType, $scopeId)['node'];

        return filled($node) && (int) $node->depth >= 1;
    }

    /**
     * 解析并缓存：匹配节点 + 其 normal 祖先链（由近及远）
     *
     * @return array{node: ?Navigation, ancestors: Collection<int, Navigation>}
     */
    protected static function resolve(string $scopeType, int | string $scopeId = 0): array
    {
        $key = "{$scopeType}:{$scopeId}";

        if (! array_key_exists($key, static::$cache)) {
            $node = static::matchNode($scopeType, $scopeId);

            static::$cache[$key] = [
                'node' => $node,
                'ancestors' => $node
                    ? $node->ancestors()->normal()->defaultOrder()->get()->reverse()->values()
                    : collect(),
            ];
        }

        return static::$cache[$key];
    }

    /**
     * 在 scope 内的 normal 节点中寻找当前请求的最佳匹配
     *
     * 仅遍历主导航类型（scope 内第一个类型，与页头 Navigation 组件的默认解析一致），
     * 避免其他导航类型（页脚/测试树等）的节点参与匹配或触发其 URL 求值
     */
    protected static function matchNode(string $scopeType, int | string $scopeId): ?Navigation
    {
        $type = Utils::getNavigationTypeModel()::scopeable($scopeType, $scopeId)
            ->orderBy('id')
            ->first();

        if (! $type) {
            return null;
        }

        $currentPath = '/'.trim(request()->getPathInfo(), '/');

        // 必须用 kalnoy 的 scoped()（而非 snScope）：withDepth 的深度子查询依赖 scoped 查询上下文，
        // 普通 where 过滤下深度恒为 -1，兄弟分组推导会失效
        $scoped = [
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
            'type_id' => $type->id,
        ];
        has_tenancy() && $scoped['team_id'] = current_tenant()?->id;

        $candidates = Utils::getNavigationModel()::scoped($scoped)
            ->normal()
            ->defaultOrder()
            ->withDepth()
            ->get();

        $best = null;
        $bestScore = 0;      // 2 = 精确，1 = 前缀
        $bestLength = 0;     // 前缀匹配取最长

        foreach ($candidates as $candidate) {
            $path = static::nodePath($candidate);

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
            if ($path === '/' || ! str_starts_with($currentPath, $path.'/')) {
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
    protected static function nodePath(Navigation $node): ?string
    {
        $url = $node->url_info['url'] ?? null;

        if (blank($url) || $url === '#') {
            return null;
        }

        // 外域链接不参与匹配
        if (! str_starts_with($url, '/')) {
            $host = request()->getSchemeAndHttpHost();
            if (! str_starts_with($url, $host)) {
                return null;
            }

            $url = substr($url, strlen($host));
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        return '/'.trim($path, '/');
    }

    /**
     * 清空缓存（长连接进程 / 测试隔离用）
     */
    public static function flush(): void
    {
        static::$cache = [];
    }
}
