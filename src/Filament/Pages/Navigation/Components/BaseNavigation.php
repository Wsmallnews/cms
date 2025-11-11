<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Components;

use BackedEnum;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use UnitEnum;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationForm;
use Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationInfolist;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\FilamentNestedset\Pages\NestedsetPage;

use function Filament\Support\generate_icon_html;

class BaseNavigation extends NestedsetPage
{
    // 所属类型
    public ?NavigationTypeModel $navigationType = null;

    public ?array $properties = [];

    protected static ?string $emptyLabel = '导航数据为空';

    protected static ?string $model = NavigationModel::class;

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

    public function getRecordLabel(Model $item): HtmlString | string
    {
        $recordLabel = '<span class="flex items-center gap-2">';
        $icon_type = $item->options['icon_type'] ?? 'none';
        if ($icon_type == 'icon') {
            $icon = $item->options['icon'] ?? ($item->options['active_icon'] ?? '');
            $icon && $recordLabel .= generate_icon_html($icon, size: IconSize::Large)->toHtml();
        } else if ($icon_type == 'image') {
            $image = $item->options['icon_src'] ?? ($item->options['active_icon_src'] ?? '');
            $image && $recordLabel .= "<img src=\"" . files_url($image) . "\" class=\"size-6\" />";
        }

        $recordLabel .= parent::getRecordLabel($item) . '</span>';

        return new HtmlString($recordLabel);
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
