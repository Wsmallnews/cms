<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation;

use BackedEnum;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;
use Wsmallnews\Cms\Enums\NavigationTypeStatus;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationForm;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationInfolist;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Schemas\NavigationTypeForm;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\FilamentNestedset\Filament\Pages\NestedsetPage;
use Wsmallnews\Support\Filament\Pages\Concerns\Scopeable;

abstract class Base extends NestedsetPage
{
    use Scopeable;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?NavigationTypeModel $navigationType = null;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBars3BottomLeft;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Bars3BottomLeft;

    protected static ?string $slug = 'navigations';

    protected static ?int $navigationSort = 1;

    /**
     * 是否可管理导航类型
     */
    protected static bool $canManage = false;

    protected string $view = 'sn-cms::filament.pages.navigation.navigation-page';

    public function mount(): void
    {
        $this->navigationType = $this->getNavigationType();

        static::$level = $this->navigationType?->level ?? null;

        // 可管理导航类型，填充表单数据
        if (static::getCanManage()) {
            $attributes = $this->navigationType ? $this->navigationType->attributesToArray() : [];
            $attributes['level'] = $attributes['level'] ?? static::getLevel();

            $this->form->fill($attributes);
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getNestedsetActions()
    {
        return [
            $this->createAction(),
            $this->fixNestedsetAction(),
        ];
    }

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

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('sn-cms::cms.navigation_page.title');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? __('sn-cms::cms.navigation_page.navigation_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return static::$navigationGroup ?? __('sn-cms::cms.global_default.navigation_group');
    }

    public static function getCanManage(): bool
    {
        return static::$canManage;
    }

    public static function getLevel(): ?int
    {
        return static::$level;
    }

    public static function getEmptyLabel(): ?string
    {
        return static::$emptyLabel ?? __('sn-cms::cms.navigation_page.empty_label');
    }

    public static function getEmptyTipLabel(): ?string
    {
        return static::$emptyTipLabel ?? __('sn-cms::cms.navigation_page.empty_tip_label');
    }

    public function getNavigationType(): ?NavigationTypeModel
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

        // 不可管理模式，自动更新自定义设置的层级
        if ($navigationType && ! static::getCanManage()) {
            // 固定层级
            $navigationType->level = static::getLevel();
            $navigationType->save();
        }

        return $navigationType;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Form::make(function () {
                    $forms = NavigationTypeForm::forms();

                    return $forms;
                })
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Schemas\Components\Actions::make([
                            Actions\Action::make('save')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->navigationType)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (! $this->navigationType) {
            $this->navigationType = new (Utils::getNavigationTypeModel());
            if (static::isScopedToTenant() && ($tenant = Filament::getTenant())) {
                $this->navigationType->team_id = $tenant->id;
            }
            $this->navigationType->scope_type = static::getScopeType();
            $this->navigationType->scope_id = static::getScopeId();
        }

        $this->navigationType->fill($data);
        $this->navigationType->save();

        if ($this->navigationType->wasRecentlyCreated) {
            $this->form->record($this->navigationType)->saveRelationships();
        }

        static::$level = $this->navigationType->level ?? null;

        Notification::make()
            ->success()
            ->title(__('sn-cms::cms.navigation_page.save_success'))
            ->send();
    }

    protected function getRecordLabel(Model $record): HtmlString | string
    {
        return $record->name_label;
    }

    protected function nestedScoped(): array
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

    protected function infolistSchema(): array
    {
        return NavigationInfolist::infolist();
    }

    protected function getEloquentQuery($query)
    {
        return $query->with(['content']);
    }
}
