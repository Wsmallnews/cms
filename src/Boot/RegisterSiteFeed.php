<?php

namespace Wsmallnews\Cms\Boot;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Feed;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteRegistry;
use Wsmallnews\Support\Features\Site\SiteService;

/**
 * RSS channel metadata and the posts stream for one site.
 *
 * A site with feed.enabled=false registers nothing, so the module has no feed
 * endpoint there.
 */
final class RegisterSiteFeed extends SiteService
{
    public function register(Site $site): void
    {
        $module = app(CmsPlugin::class)->getId();

        // 本站关闭 feed：不注册任何流（模块优先结构下，未注册即空）
        if (! $site->getOption('feed.enabled', true)) {
            return;
        }

        // 站点 feed 整节透传（enabled 是启用门控，不进配置），行为参数 uri/limit 等经 resolveConfig 统一解析；
        // 频道元数据站点声明优先，未声明时用 GeneralSettings 闭包兜底
        $config = collect($site->getOption('feed', []))->except('enabled')->all();
        $config['title'] ??= function () {
            $general = app(GeneralSettings::class);

            return filled($general->site_name) ? $general->site_name : config('app.name');
        };
        $config['description'] ??= function () {
            $general = app(GeneralSettings::class);

            return filled($general->seo_description) ? $general->seo_description : $general->site_slogan;
        };
        $config['link'] ??= fn () => $site->route('index');

        Feed::config($module, $config, $site)->register($module, 'posts', [
            'title' => function () {
                $general = app(GeneralSettings::class);
                $siteName = filled($general->site_name) ? $general->site_name : config('app.name');

                return $siteName . ' - ' . __('sn-cms::cms.frontend.rss_posts');
            },
            'label' => fn () => __('sn-cms::cms.frontend.rss_posts'),
            'description' => function () {
                $general = app(GeneralSettings::class);

                return filled($general->seo_description) ? $general->seo_description : $general->site_slogan;
            },
            'link' => fn () => $site->route('index'),
            'items' => fn (): iterable => Utils::getPostModel()::query()
                ->published()
                ->forScopeable(app(SiteRegistry::class)->scopeableFor($site))
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit((int) Feed::resolveConfig($site, 'limit', 50))
                ->get(['id', 'slug', 'title', 'description', 'published_at'])
                ->map(fn ($post): array => [
                    'title' => $post->title ?? '',
                    'url' => $site->route('posts.show', $post),
                    'description' => $post->description,
                    'updated_at' => $post->published_at,
                ])
                ->all(),
        ], $site);
    }
}
