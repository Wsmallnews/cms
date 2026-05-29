<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;
use Wsmallnews\Cms\Models\NavigationType;
use Wsmallnews\Support\Livewire\Concerns\CanBeContained;
use Wsmallnews\Support\Livewire\Concerns\HasProperties;

class Navigation extends Widget
{
    use CanBeContained;
    use HasProperties;

    #[Reactive]
    public ?NavigationType $record = null;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'sn-cms::filament.pages.navigation.widgets.navigation';
}
