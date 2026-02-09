<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Livewire\Component;
use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

class Base extends Component
{
    use HasThemeView;
    use Scopeable;
}
