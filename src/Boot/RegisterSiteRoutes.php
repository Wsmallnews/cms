<?php

namespace Wsmallnews\Cms\Boot;

use Wsmallnews\Cms\Support\CmsFrontendRoutes;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteService;

/** Frontend route group of one site. */
final class RegisterSiteRoutes extends SiteService
{
    public function register(Site $site): void
    {
        if ($site->routable) {
            CmsFrontendRoutes::register($site);
        }
    }
}
