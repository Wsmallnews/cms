<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Wsmallnews\Cms\Support\Utils;

#[Layout('sn-cms::components.layouts.app')]
class Base extends Component
{
    public function getScopeType(): ?string
    {
        return Utils::getScopeable()['scope_type'] ?? null;
    }

    public function getScopeId(): ?int
    {
        return Utils::getScopeable()['scope_id'] ?? null;
    }

    public function getScopeable(): array
    {
        return Utils::getScopeable();
    }
}
