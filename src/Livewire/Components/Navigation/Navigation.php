<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\FilamentNestedset\Livewire\Components\Nestedset;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

use function Filament\Support\generate_href_html;

class Navigation extends Nestedset
{
    use HasThemeView;
    use Navigationable;
    use Scopeable;

    public function getRecordUrl(Model $record): string | HtmlString | null
    {
        // 没有子导航时，才返回 url
        if (! $record->children->count()) {
            $urlInfo = $record->url_info;

            return generate_href_html($urlInfo['url'], $urlInfo['target']);
        }

        return null;
    }

    public function getRecordLabel(Model $record): HtmlString | string
    {
        return $record->name_label;
    }

    public function getHasActive(Model $record): bool
    {
        return $record->has_active;
    }

    public function getNestedset(): Collection
    {
        return $this->getScopedQuery()->normal()->defaultOrder()
            ->get()->toTree();
    }

    public function getRecordView(): string
    {
        return $this->getBladeThemeView('components.navigation.navigation-record');
    }

    public function render(): View
    {
        return view($this->getThemeView('components.navigation.navigation'));
    }
}
