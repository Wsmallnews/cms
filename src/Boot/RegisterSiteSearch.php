<?php

namespace Wsmallnews\Cms\Boot;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Search;
use Wsmallnews\Support\Features\Search\SearchSource;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteService;

/**
 * Search options and sources for one site.
 *
 * A site with search.enabled=false registers nothing, so the module has no
 * search there.
 */
final class RegisterSiteSearch extends SiteService
{
    public function register(Site $site): void
    {
        $module = app(CmsPlugin::class)->getId();

        // 本站关闭搜索：不注册任何来源与选项（模块优先结构下，未注册即无该搜索）
        if (! $site->getOption('search.enabled', true)) {
            return;
        }

        // 站点 search 整节透传（enabled 是启用门控，不进配置）；page 未声明时用本包结果页兜底
        $config = collect($site->getOption('search', []))->except('enabled')->all();
        $config['page'] ??= fn (?string $query) => $site->route('search', ['q' => $query]);

        Search::config($module, $config, $site)
            ->registers($module, [
                new SearchSource([
                    'key' => 'post',
                    'model' => Utils::getPostModel(),
                    'group' => __('sn-cms::cms.post_resource.model_label'),
                    // with('categories')：条目视图展示分类标签，预加载避免 N+1
                    'query' => fn ($query) => $query->published()->with('categories'),
                    'scopeable' => fn (): array => Utils::getScopeable(),
                    'url' => fn ($record) => $site->route('posts.show', $record),
                    // 自定义条目视图（结构参考 cms/posts 页面的文章条目；数据：$result 含 ->record、$query、$highlight）
                    'view' => 'sn-cms::components.search.post-item',
                ]),
            ], $site);
    }
}
