<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Component;
use Wsmallnews\Cms\Livewire\Concerns\HasView;
use Wsmallnews\Cms\Support\Utils;

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
