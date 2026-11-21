# Wsmallnews system cms modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wsmallnews/cms.svg?style=flat-square)](https://packagist.org/packages/wsmallnews/cms)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/wsmallnews/cms/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/wsmallnews/cms/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/wsmallnews/cms/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/wsmallnews/cms/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/wsmallnews/cms.svg?style=flat-square)](https://packagist.org/packages/wsmallnews/cms)

`wsmallnews/cms` is a Filament CMS package for navigation management, post management, tags, frontend CMS routes, authentication pages, and user profile pages. Navigation trees are powered by `wsmallnews/filament-nestedset`, and posts integrate with the Wsmallnews support, comment, preference, and category packages.

## Features

- Navigation tree management with `wsmallnews/filament-nestedset`
- Navigation type management for separating different navigation trees
- Post resource with media, tags, categories, comments, preferences, counters, and publishing states
- Configurable frontend CMS routes and theme layout
- Livewire frontend components for navigation, post lists, post detail, footer, and breadcrumbs
- User auth/profile/settings pages for CMS-facing users
- Scopeable data via `scope_type` and `scope_id`
- Optional Filament tenancy support

## AI Guidelines

This package ships Laravel Boost AI Guidelines in `resources/boost/guidelines/core.blade.php`.

Install or enable Laravel Boost in your application, then refresh Boost resources so this package's guidelines are discovered and added to the project overview:

```bash
php artisan boost:update --discover
```

Boost updates the root `boost.json` and `CLAUDE.md` automatically. Check those files after running the command to confirm `wsmallnews/cms` is included.

## Installation

You can install the package via composer:

```bash
composer require wsmallnews/cms:^1.0
```

The package provides an install command. By default it also installs its support, comment, and preference dependencies:

```bash
php artisan sn-cms:install
```

To skip dependency installation and interactive prompts:

```bash
php artisan sn-cms:install --no-deps --no-interaction
```

You can publish only the config file individually:

```bash
php artisan vendor:publish --tag="cms-config"
```

Publish and run only the migrations individually:

```bash
php artisan vendor:publish --tag="cms-migrations"
php artisan migrate
```

Multi language support, you can publish the language files using:

```bash
php artisan vendor:publish --tag="sn-support-translations"
```

Optionally, you can publish the views using:

```bash
php artisan vendor:publish --tag="cms-views"
```

## Configuration

The package configuration lives in `config/sn-cms.php`:

```php
return [
    'models' => [
        'navigation' => Wsmallnews\Cms\Models\Navigation::class,
        'navigation_type' => Wsmallnews\Cms\Models\NavigationType::class,
        'post' => Wsmallnews\Cms\Models\Post::class,
        'link' => Wsmallnews\Cms\Models\Link::class,
    ],

    'routes' => [
        'route_key_name' => [
            'post' => 'slug',
        ],
    ],

    'options' => [
        'auth' => [
            'guard' => 'web',
            'user_type' => 'member',
        ],
        // feed / search / comments / two_factor / navigation / themes / route_uris …
    ],
];
```

`sn-cms.routes` only keeps `route_key_name`; shared URI defaults live in `sn-cms.options.route_uris`, and sites override them via `options(['route_uris' => [...]])`. Other site behaviour defaults (feed/search/comments/two_factor/navigation/themes) live under `sn-cms.options`; prefix, domain, middleware, route name and frontend flag are declared on each site.

## Filament plugin

Cms is a standalone site plugin and requires an explicit site:

```php
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Support\Features\Site\SiteConfig;

$panel->plugin(
    CmsPlugin::make()
        ->site('main', fn (SiteConfig $site) => $site
            ->prefix('cms/{tenant:slug}')   // required
            ->routeNamePrefix('sn-cms.tenant.')   // optional; auto-generated when omitted
            ->options([
                'route_uris' => ['search' => 's'],
                'search' => ['display' => 'page'],
                'two_factor' => ['enabled' => false],
            ])
        )
);
```

`scopeable()` is optional: `main` uses `sn-cms` (the plugin id), additional
sites derive `sn-cms-{siteKey}` automatically. `footer` style sub-scopes derive
`{site.scopeType}::footer`; a site scope type itself must not contain `:`.

Register several sites in one panel when the same admin hosts more than one
frontend content space (for example a main site and a campaign site). Each site
gets its own scope and its own copy of the resources/pages:

```php
CmsPlugin::make()
    ->site('main', fn (SiteConfig $site) => $site
        ->prefix('cms/{tenant:slug}'))
    ->site('campaign', fn (SiteConfig $site) => $site
        ->prefix('cms/{tenant:slug}/campaign')
        ->label('Campaign')
        // Per-site admin defaults, merged between global_default and the entry
        ->panelDefaults(['navigation_group' => 'Campaign']))
```

A panel with several sites names each navigation group after the site label
unless the site sets `panelDefaults(['navigation_group' => ...])`; an entry's own
`navigation_group` still wins.

The default resources and pages live in the published config under
`panel_register` — comment an entry out to drop it globally. `resources()` and
`pages()` on the plugin **append** entries or override an existing class by
merging its overrides. Keys are always derived from panel, plugin, site and
class (a default entry must never pin one); URLs stay clean (single site uses
the class default slug, several sites nest under the site key).

```php
CmsPlugin::make()
    ->site('main', fn (SiteConfig $site) => $site->prefix('cms'))
    ->resources([PostResource::class, PageResource::class])
    ->pages([
        NavigationPage::class,
        // 子 scope 由类自述：FooterNavigationPage::getScopeSuffix() = 'footer'
        FooterNavigationPage::class,
    ]);
```

Pages from other packages can be registered the same way — for example the
category page is registered by cms into its own site scope:

```php
->pages([\Wsmallnews\Category\Filament\Pages\Category\CategoryPage::class])
```

Sub-scopes (`{site.scopeType}::{suffix}`) are declared on the component class
via `getScopeSuffix()`, or per entry with `scope_suffix`. The suffix is a
**name** and the pair is always derived from the site, so the frontend and the
admin can never disagree about where a partition lives. Entries cannot declare
`scope_type` / `scope_id` / `scopeable`: the pair belongs to the site, and two
non-aggregate sites may never share one.

Registering the same class twice needs the tuple form (a map entry only
overrides the default entry). Every extra instance must pass its own `key` and
declare its own `scope_suffix`:

```php
->resources([
    ProductResource::class,                                                   // main scope
    [ProductResource::class, ['key' => 'vip', 'scope_suffix' => 'vip']],      // sn-shop::vip
    [ProductResource::class, ['key' => 'group', 'scope_suffix' => 'group']],  // sn-shop::group
])
```

The frontend page for a partition looks it up by name
(`$site->getScope('vip')`) and fails closed when the site did not register it.

## Usage

### Navigation page

`NavigationPage` extends `Wsmallnews\Cms\Filament\Pages\Navigation\Base`, which extends `Wsmallnews\FilamentNestedset\Filament\Pages\NestedsetPage`.

The base page automatically resolves or creates a `NavigationType` for the configured scope, applies the type's level to the nestedset tree, and scopes navigation records by:

- `scope_type`
- `scope_id`
- `type_id`
- `team_id` when tenancy is enabled

### Custom navigation page

Create your own navigation page by extending the base page:

```php
<?php

namespace App\Filament\Pages;

use Wsmallnews\Cms\Filament\Pages\Navigation\Base;

class FooterNavigation extends Base
{
    protected static ?string $slug = 'footer-navigation';

    protected static ?string $scopeType = 'footer';

    protected static int $scopeId = 0;

    protected static ?int $level = 2;
}
```

### Posts

`Wsmallnews\Cms\Models\Post` supports:

- `PostStatus` enum states
- media via Spatie Media Library
- tags via Spatie Tags
- categories through `sn_category_post`
- comments through the Wsmallnews comment package
- preferences/views through the Wsmallnews preference package
- `HasSnSubject` methods for preference display components

### Frontend components

The service provider registers frontend Livewire components such as:

```blade
<livewire:sn-cms-components-navigation />
<livewire:sn-cms-components-navigation-breadcrumb />
<livewire:sn-cms-components-posts />
<livewire:sn-cms-components-post />
<livewire:sn-cms-components-footer />
```

Routes are enabled by default under the `cms` prefix. Configure `sn-cms.routes.enabled`, `sn-cms.routes.prefix`, and `sn-cms.routes.name` for your application.

## Namespace Quick Reference

| Category | Namespace |
| --- | --- |
| Plugin | `Wsmallnews\Cms\CmsPlugin` |
| ServiceProvider | `Wsmallnews\Cms\CmsServiceProvider` |
| Navigation Page Base | `Wsmallnews\Cms\Filament\Pages\Navigation\Base` |
| Navigation Page | `Wsmallnews\Cms\Filament\Pages\Navigation\NavigationPage` |
| Navigation Widget | `Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\Navigation` |
| Post Resource | `Wsmallnews\Cms\Filament\Resources\Posts\PostResource` |
| Navigation Type Resource | `Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource` |
| Tag Resource | `Wsmallnews\Cms\Filament\Resources\Tags\TagResource` |
| Navigation Model | `Wsmallnews\Cms\Models\Navigation` |
| Navigation Type Model | `Wsmallnews\Cms\Models\NavigationType` |
| Post Model | `Wsmallnews\Cms\Models\Post` |
| Install Command | `Wsmallnews\Cms\Commands\CmsInstallCommand` |
| Utils | `Wsmallnews\Cms\Support\Utils` |

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [smallnews@qq.com](https://github.com/Wsmallnews)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
