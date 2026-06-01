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
use Wsmallnews\Support\Filament\Pages\Concerns\Scopeable;

class Navigation extends Nestedset
{
    use Scopeable;

    #[Reactive]
    public ?NavigationTypeModel $record = null;

    protected static ?NavigationTypeModel $navigationType = null;

    public function mount(): void
    {
        parent::mount();

        static::$navigationType = $this->record;
    }

    public static function getModel(): ?string
    {
        return Utils::getNavigationModel();
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.navigation_page.model_label');
    }

    public static function getRecordLabel(Model $record): HtmlString|string
    {
        return $record->name_label;
    }

    public static function nestedScoped(): array
    {
        return [
            'scope_type' => static::$navigationType?->scope_type,
            'scope_id' => static::$navigationType?->scope_id,
            'type_id' => static::$navigationType?->id,
        ];
    }

    public static function schema(array $arguments): array
    {
        return NavigationForm::forms($arguments);
    }

    public static function infolistSchema(): array
    {
        return NavigationInfolist::infolist();
    }
}
