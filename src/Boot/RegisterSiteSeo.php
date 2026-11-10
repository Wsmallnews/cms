<?php

namespace Wsmallnews\Cms\Boot;

use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Cms\Settings\GeneralSettings;
use Wsmallnews\Support\Facades\Seo;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteService;

/**
 * Default SEO values of one site, resolved from settings at render time.
 */
final class RegisterSiteSeo extends SiteService
{
    public function register(Site $site): void
    {
        // 闭包在每次渲染时才解析 Settings，自动跟随当前租户（多租户下 GeneralSettings 走 team_database 仓库，每租户一份）
        Seo::config(app(CmsPlugin::class)->getId(), function (): array {
            $general = app(GeneralSettings::class);

            return [
                'site_name' => filled($general->site_name) ? $general->site_name : config('app.name'),
                'description' => filled($general->seo_description) ? $general->seo_description : $general->site_slogan,
                'image' => filled($general->default_og_image) ? files_url($general->default_og_image) : null,
                'favicon' => filled($general->favicon) ? files_url($general->favicon) : null,
                'analytics_code' => $general->analytics_code,
            ];
        }, $site);
    }
}
