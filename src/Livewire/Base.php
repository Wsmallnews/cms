<?php

namespace Wsmallnews\Cms\Livewire;

use Wsmallnews\Cms\Livewire\Concerns\HasCmsConfig;
use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Support\Livewire\Base as BaseComponent;

class Base extends BaseComponent
{
    use HasCmsConfig;
    use HasThemeView;
}
