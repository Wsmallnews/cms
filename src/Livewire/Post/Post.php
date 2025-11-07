<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Wsmallnews\Cms\Livewire\Base;

class Post extends Base
{
    public int $id;

    public function render()
    {
        return view('sn-cms::livewire.post');
    }
}
