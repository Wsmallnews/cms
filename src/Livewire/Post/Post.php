<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Wsmallnews\Cms\Livewire\Common\BasePage;

class Post extends BasePage
{
    public int $id;

    public function render()
    {
        return view('sn-cms::livewire.post');
    }
}
