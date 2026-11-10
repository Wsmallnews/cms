<?php

namespace Wsmallnews\Cms;

use Illuminate\Contracts\Container\Container;
use Wsmallnews\Cms\Boot\RegisterComposition;
use Wsmallnews\Cms\Boot\RegisterCompositionPurposes;
use Wsmallnews\Cms\Boot\RegisterScheduledTasks;
use Wsmallnews\Cms\Boot\RegisterSiteFeed;
use Wsmallnews\Cms\Boot\RegisterSiteRoutes;
use Wsmallnews\Cms\Boot\RegisterSiteSearch;
use Wsmallnews\Cms\Boot\RegisterSiteSeo;
use Wsmallnews\Cms\Boot\RegisterSiteSidebar;
use Wsmallnews\Cms\Boot\RegisterSiteSitemap;
use Wsmallnews\Cms\Boot\RegisterUserConfig;
use Wsmallnews\Support\Features\Site\SiteService;
use Wsmallnews\Support\Features\Site\SiteServicePipeline;

/**
 * All registrations owned by the cms module, split by lifecycle:
 *
 * - MODULE_REGISTRARS run once at boot (module config, sources, menus);
 * - SITE_SERVICES run once per registered site after every panel booted.
 *
 * A module-keyed registry must not move into SITE_SERVICES before it supports
 * site partitioning, otherwise its entries are registered once per site.
 */
final class CmsSiteServices
{
    /** @var list<class-string> */
    public const MODULE_REGISTRARS = [
        RegisterComposition::class,
        RegisterCompositionPurposes::class,
        RegisterScheduledTasks::class,
    ];

    /** @var list<class-string<SiteService>> */
    public const SITE_SERVICES = [
        RegisterSiteFeed::class,
        RegisterSiteSeo::class,
        RegisterSiteSidebar::class,
        RegisterSiteRoutes::class,
        RegisterSiteSearch::class,
        RegisterSiteSitemap::class,
        RegisterUserConfig::class,
    ];

    public function __construct(private readonly Container $container) {}

    public function registerModules(): void
    {
        foreach (self::MODULE_REGISTRARS as $registrar) {
            $this->container->make($registrar)->register();
        }
    }

    public function pipeline(): SiteServicePipeline
    {
        return new SiteServicePipeline(array_map(
            fn (string $service): SiteService => $this->container->make($service),
            self::SITE_SERVICES,
        ));
    }
}
