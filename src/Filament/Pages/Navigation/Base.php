<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation;

use BackedEnum;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use UnitEnum;
use Wsmallnews\Cms\Enums\NavigationTypeStatus;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Schemas\NavigationTypeForm;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Pages\Concerns\Scopeable;

abstract class Base extends Page
{
    use Scopeable;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?NavigationTypeModel $navigationType = null;

    protected static ?int $level = null;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBars3BottomLeft;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Bars3BottomLeft;

    protected static ?string $slug = 'navigations';

    protected static string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static ?string $emptyLabel = null;

    protected static ?string $emptyTipLabel = null;

    /**
     * 是否可管理导航类型
     */
    protected static bool $canManage = false;

    protected string $view = 'sn-cms::filament.pages.navigation.navigation-page';

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? __('sn-cms::cms.navigation_management.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? __('sn-cms::cms.navigation_management.plural_model_label');
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('sn-cms::cms.navigation_management.title');
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? __('sn-cms::cms.navigation_management.navigation_label');
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return static::$navigationGroup ?? __('sn-cms::cms.navigation_management.navigation_group');
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
        return static::$emptyLabel ?? __('sn-cms::cms.navigation_management.empty_label');
    }

    public static function getEmptyTipLabel(): ?string
    {
        return static::$emptyTipLabel ?? __('sn-cms::cms.navigation_management.empty_tip_label');
    }

    public static function getProperties(): array
    {
        return [
            'emptyLabel' => static::getEmptyLabel(),
            'emptyTipLabel' => static::getEmptyTipLabel(),
        ];
    }

    public function mount(): void
    {
        $this->navigationType = $this->getNavigationType();

        // 可管理导航类型，填充表单数据
        if (static::getCanManage()) {
            $attributes = $this->navigationType ? $this->navigationType->attributesToArray() : [];
            $attributes['level'] = $attributes['level'] ?? static::getLevel();

            $this->form->fill($attributes);
        }
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

        Notification::make()
            ->success()
            ->title(__('sn-cms::cms.navigation_management.save_success'))
            ->send();
    }
}
