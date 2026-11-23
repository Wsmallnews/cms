<?php

namespace Wsmallnews\Cms\Support;

use Illuminate\Support\Facades\Route;
use Wsmallnews\Support\Features\Site\Site;

/**
 * Builds the route-name options for the navigation form.
 *
 * Only routes registered for the current site are exposed; the stored value is
 * the full route name and rendering resolves it through sn_route() at render
 * time, so the site's URL prefix follows the live route table — nothing derived
 * is baked into the node.
 */
final class NavigationRouteOptions
{
    /** @return array<string, string> route name => "name (uri)" */
    public static function forSite(?Site $site): array
    {
        if ($site === null) {
            return [];
        }

        $options = [];

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $name = $route->getName();

            if (blank($name) || ! str_starts_with($name, $site->routeNamePrefix)) {
                continue;
            }

            if (! in_array('GET', $route->methods(), true) || self::isTechnicalRoute($name)) {
                continue;
            }

            $options[$name] = "{$name}  ({$route->uri()})";
        }

        ksort($options);

        return $options;
    }

    protected static function isTechnicalRoute(string $name): bool
    {
        return str_ends_with($name, '.sitemap')
            || str_ends_with($name, '.feed')
            || str_ends_with($name, '.feed.show');
    }
}
