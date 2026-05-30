<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Components;

use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationForm;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationInfolist;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\FilamentNestedset\Pages\NestedsetPage;
use Wsmallnews\Support\Livewire\Concerns\HasProperties;

class Navigation extends NestedsetPage
{
    use HasProperties;

    // 所属类型
    public ?NavigationTypeModel $navigationType = null;

    protected static ?string $emptyLabel = null;

    protected static ?string $emptyTipLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $title = null;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bars3BottomLeft;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Bars3BottomLeft;

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'navigations';

    protected static string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getModel(): ?string
    {
        return Utils::getNavigationModel();
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.navigation_page.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? __('sn-cms::cms.navigation_page.plural_model_label');
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? __('sn-cms::cms.navigation_page.title');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? __('sn-cms::cms.navigation_page.navigation_label');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return static::$navigationGroup ?? __('sn-cms::cms.global_default.navigation_group');
    }

    public function getEmptyLabel(): ?string
    {
        return $this->getProperty('emptyLabel') ?: (static::$emptyLabel ?? __('sn-cms::cms.navigation_page.empty_label'));
    }

    public function getEmptyTipLabel(): ?string
    {
        return $this->getProperty('emptyTipLabel') ?: (static::$emptyTipLabel ?? __('sn-cms::cms.navigation_page.empty_tip_label'));
    }

    public function getRecordLabel(Model $navigation): HtmlString|string
    {
        return $navigation->name_label;
    }

    public function getLevel(): ?int
    {
        return $this->navigationType?->level;
    }

    protected function nestedScoped()
    {
        return [
            'scope_type' => $this->navigationType?->scope_type,
            'scope_id' => $this->navigationType?->scope_id,
            'type_id' => $this->navigationType?->id,
        ];
    }

    protected function createSchema(array $arguments): array
    {
        $arguments = array_merge($arguments, $this->nestedScoped());

        return $this->schema($arguments);
    }

    protected function editSchema(array $arguments): array
    {
        $arguments = array_merge($arguments, $this->nestedScoped());

        return $this->schema($arguments);
    }

    protected function schema(array $arguments): array
    {
        return NavigationForm::forms($arguments);
    }

    public function infolistSchema(): array
    {
        return NavigationInfolist::infolist();
    }
}
