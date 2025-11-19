<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Cms\Livewire\Concerns\HasView;

class Base extends Component
{
    use HasView;

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
