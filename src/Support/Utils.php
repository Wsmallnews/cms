<?php

declare(strict_types=1);

namespace Wsmallnews\Cms\Support;

use Filament\Facades\Filament;
use Filament\Panel;

class Utils
{
    public static function getConfig($name = null)
    {
        $config = config('sn-cms');

        return $name ? ($config[$name] ?? null) : $config;
    }

    public static function currentPanel(): ?Panel
    {
        return Filament::getCurrentOrDefaultPanel();
    }

    // public static function getModel($name)
    // {
    //     return self::getConfig('models')[$name] ?? \Wsmallnews\Cms\Models\Content::class;
    // }

    public static function isTenancyEnabled(): bool
    {
        return self::currentPanel()?->hasTenancy() ?? false;
    }

    public static function getTenantModel(): ?string
    {
        return self::isTenancyEnabled() ? self::currentPanel()?->getTenantModel() : null;
    }
}
