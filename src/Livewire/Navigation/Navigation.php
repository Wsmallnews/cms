<?php

namespace Wsmallnews\Cms\Livewire\Navigation;

use Wsmallnews\Cms\Livewire\Base;

class Navigation extends Base
{
    public string $slug;

    public function render()
    {
        return view('sn-cms::livewire.navigation.navigation');
    }
}