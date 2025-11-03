<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation;

use Illuminate\Support\Str;
use Wsmallnews\Cms\Enums\NavigationTypeStatus;
use Wsmallnews\Cms\Filament\Pages\Navigation\Components\BaseNavigation;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Support\Filament\Pages\Concerns\Scopeable;

abstract class Base extends BaseNavigation
{
    use Scopeable;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?NavigationTypeModel $navigationType = null;

    public function mount(): void
    {
        $this->navigationType = $this->getNavigationType();
        parent::mount();
    }

    public function getNavigationType(): ?NavigationTypeModel
    {
        $navigationType = NavigationTypeModel::query()
            ->firstOrCreate(
                static::getScopeInfo(),
                [
                    'name' => Str::title(static::getScopeType()),
                    'level' => static::getLevel(),
                    'status' => NavigationTypeStatus::Normal,
                ]
            );

        return $navigationType;
    }
}
