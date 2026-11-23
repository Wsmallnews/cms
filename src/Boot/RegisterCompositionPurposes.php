<?php

namespace Wsmallnews\Cms\Boot;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\CompositionRegistry as CompositionRegistryFacade;

/**
 * Composition purpose slots (where a composer may be placed).
 */
final class RegisterCompositionPurposes
{
    public function register(): void
    {
        // 注册编排用途槽位（模块自有业务语义；support 侧编排表单按模块读取渲染 purpose 下拉与位置表单）。
        // 标签用闭包延迟翻译（无 boot 顺序竞态）；同槽位多条已发布编排时渲染 order_column 最前的一条
        CompositionRegistryFacade::registerPurposes(app(CmsPlugin::class)->getId(), [
            'post-sidebar' => [
                'label' => fn (): string => __('sn-cms::cms.composition_purposes.post_sidebar'),
                'positions' => ['left', 'right'],
                'default' => 'right',
                // pageContext 提供者：路由参数（slug）→ 当前文章，供 related-posts 等 context 消费者使用。
                // 路由壳据此保持薄壳（不感知 Post 模型），provider 未命中（文章不存在）返回 null 由渲染链剔除。
                // 收调用页面的 scopeable（渲染器已解析：页面分区 ?? 站点 base）；无解析来源时收 null，返回空上下文
                'pageContext' => function (array $params, ?array $scopeable): array {
                    if ($scopeable === null) {
                        return ['post' => null];
                    }

                    $postModel = Utils::getPostModel();

                    return [
                        'post' => $postModel::query()
                            ->published()
                            ->forScopeable($scopeable)
                            ->where((new $postModel)->getRouteKeyName(), $params['slug'] ?? null)
                            ->first(),
                    ];
                },
            ],
        ]);
    }
}
