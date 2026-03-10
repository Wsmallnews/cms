<?php

namespace Wsmallnews\Cms\Livewire;

use Livewire\Component;
use Wsmallnews\Cms\Livewire\Concerns\HasThemeView;
use Wsmallnews\Cms\Support\Utils;

class Base extends Component
{
    use HasThemeView;

    public function getScopeType(): ?string
    {
        return Utils::getScopeType() ?? null;
    }

    public function getScopeId(): ?int
    {
        return Utils::getScopeId() ?? null;
    }

    public function getScopeable(): array
    {
        return Utils::getScopeable();
    }

    public function getPageContainer(): string
    {
        return Utils::getPageContainer();
    }
}
