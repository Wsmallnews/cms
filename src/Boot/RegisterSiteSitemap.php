<?php

namespace Wsmallnews\Cms\Boot;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Support\CmsFrontendRoutes;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Sitemap;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteService;
use Wsmallnews\Support\Support\Utils as SupportUtils;

/**
 * Sitemap sources of one site, plus its search-page disallow rule (robots.txt is
 * one global file, flushed in finish()).
 */
final class RegisterSiteSitemap extends SiteService
{
    /** @var list<string> */
    protected array $disallow = [];

    public function register(Site $site): void
    {
        $module = app(CmsPlugin::class)->getId();

        // 本站关闭 sitemap：不注册任何来源（未注册即空），但搜索页的 robots 禁爬照常收集
        if (! $site->getOption('sitemap.enabled', true)) {
            $this->collectDisallow($site);

            return;
        }

        // 站点 sitemap.* 整节透传（enabled 是启用门控，不透传）；
        // 行为参数（如 cache_ttl）经模块声明覆盖 support 兜底 config('sn-support.sitemap.*')
        $sitemapOptions = collect($site->getOption('sitemap', []))->except('enabled')->all();
        Sitemap::config($module, $sitemapOptions, $site)->registers($module, [
            [
                'key' => 'home',
                'urls' => fn (): array => [
                    ['loc' => $site->route('index')],
                ],
            ],
            [
                'key' => 'posts-list',
                'urls' => fn (): array => [
                    ['loc' => $site->route('posts')],
                ],
            ],
            [
                'key' => 'post',
                'urls' => fn (): array => Utils::getPostModel()::snScope(...Utils::getScopeable())->published()
                    ->get(['id', 'slug', 'updated_at'])
                    ->map(fn ($post): array => [
                        'loc' => $site->route('posts.show', $post),
                        'lastmod' => $post->updated_at,
                    ])
                    ->values()
                    ->all(),
            ],
            [
                // 站点页面以 Page 实体为源（published 即进 sitemap，与是否挂导航无关）
                'key' => 'pages',
                'urls' => fn (): array => SupportUtils::getPageModel()::query()->snScope(...Utils::getScopeable())->published()
                    ->get(['id', 'slug', 'updated_at'])
                    ->map(fn ($page): array => [
                        'loc' => $site->route('pages.show', $page->slug),
                        'lastmod' => $page->updated_at,
                    ])
                    ->values()
                    ->all(),
            ],
        ], $site);

        $this->collectDisallow($site);
    }

    /** Search-page disallow path for robots.txt; tenant params become robots wildcards. */
    protected function collectDisallow(Site $site): void
    {
        // 租户参数替换为通配符，robots 不支持命名参数。
        if ($site->routable && ($path = CmsFrontendRoutes::searchDisallow($site)) !== null) {
            $this->disallow[] = $path;
        }
    }

    public function finish(): void
    {
        if ($this->disallow === []) {
            return;
        }

        Sitemap::robots(app(CmsPlugin::class)->getId(), ['disallow' => $this->disallow]);
    }
}
