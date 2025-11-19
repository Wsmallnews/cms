<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Support\Utils;

class Index extends Base
{
    #[Title('首页')]
    public function render()
    {
        return view($this->getView('index'), [
        ])->layout(Utils::getLayout());
    }
}
