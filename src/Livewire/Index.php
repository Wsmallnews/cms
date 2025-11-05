<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Attributes\Title;
use Wsmallnews\Cms\Livewire\Common\BasePage;

class Index extends BasePage
{

    #[Title('首页')]
    public function render()
    {
        // $indexBlocks = Block::query()->normal()->with(['media'])->orderBy('order_column', 'asc')->get();

        return view('sn-cms::livewire.index', [
            // 'projects' => $projects,
            // 'indexBlocks' => $indexBlocks
        ]);
    }
}
