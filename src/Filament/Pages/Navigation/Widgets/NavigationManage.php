<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;
use Wsmallnews\Cms\Models\NavigationType;

class NavigationManage extends Widget
{
    #[Reactive]
    public ?NavigationType $record = null;

    public ?array $properties = [];

    protected int | string | array $columnSpan = 'full';
    
    protected string $view = 'sn-cms::filament.pages.navigation.widgets.navigation-manage';
}
