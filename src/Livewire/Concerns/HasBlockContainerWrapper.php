<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Wsmallnews\Cms\Support\Utils;

trait HasBlockContainerWrapper
{
    /**
     * 块容器包装器视图
     * 如果设置了该属性, 则会优先使用该视图作为块容器包装器
     */
    public ?string $blockContainerWrapperView;

    /**
     * 是否默认启用块容器包装器
     * 如果设置为 false, 则在没有指定 blockContainerWrapperView 时, 会使用空的块容器包装器 (空的快容器包装器没有任何样式)
     */
    public bool $hasDefaultBlockContainerWrapper = false;

    public function getBlockContainerWrapperView(): string
    {
        if (isset($this->blockContainerWrapperView) && ! blank($this->blockContainerWrapperView)) {
            return $this->blockContainerWrapperView;
        }

        if (! $this->hasDefaultBlockContainerWrapper) {
            return 'sn-cms::base.empty';
        }

        return Utils::getThemeContainer('block-container') ?? 'sn-cms::base.block-container';
    }
}
