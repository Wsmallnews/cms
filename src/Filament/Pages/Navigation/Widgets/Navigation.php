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

class Navigation extends Nestedset
{
    #[Reactive]
    public ?NavigationTypeModel $record = null;

    public function boot()
    {
        static::$level = $this->record?->level ?? null;
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
        return $query->with(['content']);
    }
}
