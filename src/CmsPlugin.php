<?php

namespace Wsmallnews\Cms;

use BadMethodCallException;
use Filament\Contracts\Plugin;
use Filament\Panel;
use InvalidArgumentException;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Concerns\RegistersSites;

class CmsPlugin implements Plugin
{
    use RegistersSites;

    public function getId(): string
    {
        return 'sn-cms';
    }

    public function register(Panel $panel): void
    {
        if ($this->siteConfigs === []) {
            throw new InvalidArgumentException(
                'CmsPlugin requires at least one explicit site(). Example: '
                . 'CmsPlugin::make()->site("main", fn (SiteConfig $site) => $site->prefix("cms"))'
            );
        }

        $this->registerSiteComponents($panel);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists(Utils::class, $method)) {
            return Utils::$method(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist on CmsPlugin");
    }
}
