<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Support\Livewire\Base as BaseComponent;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

class Base extends BaseComponent
{
    use HasThemeView;
    use Scopeable;
}
