<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Wsmallnews\Cms\Support\Utils;

trait HasBlockContainerWrapper
{
    public ?string $blockContainerWrapperView;

    public function getBlockContainerWrapperView(): string
    {
        if (isset($this->blockContainerWrapperView)) {
            return $this->blockContainerWrapperView;
        }

        return Utils::getThemeContainer('block-container') ?? 'sn-cms::base.empty';
    }
}
