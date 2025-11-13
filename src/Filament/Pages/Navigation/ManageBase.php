<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation;

use BackedEnum;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\Schemas\NavigationTypeForm;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Filament\Pages\Concerns\Scopeable;

abstract class ManageBase extends Page
{
    use Scopeable;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public ?NavigationTypeModel $record = null;

    protected static ?string $title = '导航管理';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Bars3BottomLeft;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Bars3BottomLeft;

    protected static ?string $navigationLabel = '导航管理';

    protected static string | UnitEnum | null $navigationGroup = '网站管理';

    protected static ?string $slug = 'manage-navigations';

    protected static string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected string $view = 'sn-cms::filament.pages.navigation.manage-navigation';

    public function mount(): void
    {
        $this->record = $this->getRecord();
        $this->form->fill($this->record?->attributesToArray());
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
            ->record($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (! $this->record) {
            $this->record = new (Utils::getNavigationTypeModel());
            $this->record->scope_type = static::getScopeType();
            $this->record->scope_id = static::getScopeId();
        }

        $this->record->fill($data);
        $this->record->save();

        if ($this->record->wasRecentlyCreated) {
            $this->form->record($this->record)->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title('保存成功')
            ->send();
    }

    public function getRecord(): ?NavigationTypeModel
    {
        return Utils::getNavigationTypeModel()::query()
            ->scopeable(static::getScopeType(), static::getScopeId())
            ->first();
    }

    public function getProperties(): array
    {
        return [
            'emptyLabel' => method_exists($this, 'getEmptyLabel') ? $this->getEmptyLabel() : null,
        ];
    }
}
