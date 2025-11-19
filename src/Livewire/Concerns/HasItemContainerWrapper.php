<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Wsmallnews\Cms\Support\Utils;

trait HasItemContainerWrapper
{
    public ?string $itemContainerWrapperView;

    public function getItemContainerWrapperView(): string
    {
        if (isset($this->itemContainerWrapperView)) {
            return $this->itemContainerWrapperView;
        }

        return Utils::getThemeContainer('item-container') ?? 'sn-cms::base.empty';
    }
}
