<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Components;

use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationForm;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationInfolist;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\FilamentNestedset\Pages\NestedsetPage;

class BaseNavigation extends NestedsetPage
{
    // 所属类型
    public ?NavigationTypeModel $navigationType = null;

    public ?array $properties = [];

    protected static ?string $emptyLabel = '导航数据为空';

    protected static ?string $modelLabel = '导航管理';

    protected static ?string $pluralModelLabel = '导航管理';

    protected static ?string $title = '导航管理';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Bars3BottomLeft;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::Bars3BottomLeft;

    protected static ?string $navigationLabel = '导航管理';

    protected static string | UnitEnum | null $navigationGroup = '网站管理1';

    protected static ?string $slug = 'navigations';

    protected static string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getModel()
    {
        return Utils::getNavigationModel();
    }

    public function createSchema($arguments): array
    {
        $arguments = array_merge($arguments, $this->nestedScoped());

        return $this->schema($arguments);
    }

    public function editSchema($arguments): array
    {
        $arguments = array_merge($arguments, $this->nestedScoped());

        return $this->schema($arguments);
    }

    public function infolistSchema(): array
    {
        return NavigationInfolist::infolist();
    }

    public function getRecordLabel(Model $navigation): HtmlString | string
    {
        return $navigation->name_label;
    }

    public function getLevel(): ?int
    {
        return $this->navigationType?->level;
    }

    public function getEmptyLabel(): ?string
    {
        return (isset($this->properties['emptyLabel']) && filled($this->properties['emptyLabel'])) ? $this->properties['emptyLabel'] : parent::getEmptyLabel();
    }

    protected function nestedScoped()
    {
        return [
            'scope_type' => $this->navigationType?->scope_type,
            'scope_id' => $this->navigationType?->scope_id,
            'type_id' => $this->navigationType?->id,
        ];
    }

    protected function schema(array $arguments): array
    {
        return NavigationForm::forms($arguments);
    }
}
