<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\FilamentNestedset\Livewire\Components\Nestedset;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

class Navigation extends Nestedset
{
    use HasThemeView;
    use Navigationable;
    use Scopeable;

    public function getNestedset(): Collection
    {
        // withDepth：树节点带 depth（根为 0），供手风琴缩进块等使用
        return $this->getScopedQuery()?->normal()->defaultOrder()->withDepth()->get()->toTree() ?? collect([]);
    }

    public function render(): View
    {
        return view($this->getThemeView('components.navigation.navigation'));
    }
}
