<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Livewire\Component;
use Wsmallnews\Cms\Livewire\Concerns\HasView;
use Wsmallnews\Cms\Livewire\Concerns\HasBlockContainerWrapper;
use Wsmallnews\Support\Livewire\Concerns\Scopeable;

class Base extends Component
{
    use HasBlockContainerWrapper;
    use HasView;
    use Scopeable;
}
