<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Attributes\Title;

class Index extends Base
{
    #[Title('首页')]
    public function render()
    {
        return view('sn-cms::livewire.index', [
        ]);
    }
}
