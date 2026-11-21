<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Widgets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Reactive;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationForm;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationInfolist;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\FilamentNestedset\Filament\Pages\Widgets\Nestedset;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Filament\Widgets\Concerns\ResolvesSite;

class Navigation extends Nestedset
{
    use ResolvesSite;

    // Reactive: synced from the parent page, so it cannot be #[Locked] (the
    // reactive update path triggers the update hook). Tenant isolation comes
    // from the record's type_id.
    #[Reactive]
    public ?NavigationTypeModel $record = null;

    public function mount(?NavigationTypeModel $record = null): void
    {
        static::$level = $record?->level;
    }

    public function boot(): void
    {
        static::$level = $this->record?->level;
    }

    public static function getModel(): ?string
    {
        return Utils::getNavigationModel();
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.navigation_page.model_label');
    }

    public function getRecordLabel(Model $record): HtmlString | string
    {
        return $record->name_label;
    }

    /** Site owning the navigation type being edited, passed by the rendering page. */
    public function getNavigationSite(): ?Site
    {
        return $this->getSite();
    }

    public function nestedScoped(): array
    {
        return [
            'scope_type' => $this->record?->scope_type,
            'scope_id' => $this->record?->scope_id,
            'type_id' => $this->record?->id,
        ];
    }

    public function schema(array $arguments): array
    {
        $arguments = array_merge($arguments, $this->nestedScoped());

        return NavigationForm::forms($arguments);
    }

    public function infolistSchema(): array
    {
        return NavigationInfolist::infolist();
    }

    public function getEloquentQuery($query)
    {
        return $query->with(['page']);
    }
}
