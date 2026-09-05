<?php

namespace Wsmallnews\Category\Livewire\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\FilamentNestedset\Livewire\Components\Nestedset;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

class NavigationNestedset extends Nestedset
{
    use Navigationable;
    use Scopeable;

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
        return $this->getNavigationTree();
    }

    public function render(): View
    {
        return view($this->view);
    }
}
