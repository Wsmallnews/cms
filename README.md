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
    'scopeable' => [
        'scope_type' => 'sn-cms',
        'scope_id' => 0,
    ],

    'models' => [
        'navigation' => Wsmallnews\Cms\Models\Navigation::class,
        'navigation_type' => Wsmallnews\Cms\Models\NavigationType::class,
        'post' => Wsmallnews\Cms\Models\Post::class,
    ],

    'routes' => [
        'enabled' => true,
        'prefix' => 'cms',
        'name' => 'sn-cms.',
    ],
];
```

Use `sn-cms.panel_register` to control which Filament pages/resources are registered, `sn-cms.routes` to control frontend route generation, and `sn-cms.themes` to control frontend layout and dark mode behavior.

## Filament plugin

Register the plugin on your Filament panel:

```php
use Wsmallnews\Cms\CmsPlugin;

$panel
    ->plugin(CmsPlugin::make());
```

`CmsPlugin` registers the pages and resources configured in `sn-cms.panel_register`, including:

- `Wsmallnews\Cms\Filament\Pages\Navigation\NavigationPage`
- `Wsmallnews\Cms\Filament\Pages\Category`
- `Wsmallnews\Cms\Filament\Pages\GeneralSetting`
- `Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource`
- `Wsmallnews\Cms\Filament\Resources\Posts\PostResource`
- `Wsmallnews\Cms\Filament\Resources\Tags\TagResource`

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
