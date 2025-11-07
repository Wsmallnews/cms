<?php

namespace Wsmallnews\Cms\Livewire\Post;

use Livewire\Attributes\Url;
use Wsmallnews\Cms\Livewire\Base;

class Posts extends Base
{
    #[Url]
    public int $category_id;

    public function mount()
    {
        $this->category_id = request()->get('category_id', 0);
    }

    public function render()
    {
        return view('sn-cms::livewire.posts', []);
    }
}
