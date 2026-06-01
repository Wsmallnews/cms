<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Widgets;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\Reactive;
use Wsmallnews\Cms\Enums\NavigationTypeStatus;
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

    public static function getModel(): ?string
    {
        return Utils::getNavigationModel();
    }

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.navigation_page.model_label');
    }

    public static function getRecordLabel(Model $record): HtmlString | string
    {
        return $record->name_label;
    }

    public static function nestedScoped(): array
    {
        $navigationType = static::getNavigationType();

        return [
            'scope_type' => $navigationType?->scope_type,
            'scope_id' => $navigationType?->scope_id,
            'type_id' => $navigationType?->id,
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

    public static function getNavigationType(): ?NavigationTypeModel
    {
        $navigationType = Utils::getNavigationTypeModel()::query()
            ->snScope(static::getScopeType(), static::getScopeId())
            ->first();

        if (! $navigationType && ! static::getCanManage()) {
            // 自动创建导航类型
            $navigationType = Utils::getNavigationTypeModel()::create([
                'name' => Str::title(static::getScopeType()),
                'level' => static::getLevel(),
                'status' => NavigationTypeStatus::Normal,
                ...static::getScopeable(),
                'team_id' => Filament::getTenant()?->id,
            ]);
        }

        return $navigationType;
    }
}
