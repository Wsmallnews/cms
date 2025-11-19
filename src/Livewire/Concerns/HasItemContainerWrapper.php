<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Wsmallnews\Cms\Support\Utils;

trait HasItemContainerWrapper
{

    /**
     * 项容器包装器视图
     * 如果设置了该属性, 则会优先使用该视图作为项容器包装器
     *
     * @var string|null
     */
    public ?string $itemContainerWrapperView;

    /**
     * 是否默认启用项容器包装器
     * 如果设置为 false, 则在没有指定 itemContainerWrapperView 时, 会使用空的项容器包装器 (空的项容器包装器没有任何样式)
     *
     * @var boolean
     */
    public bool $hasDefaultItemContainerWrapper = false;

    public function getItemContainerWrapperView(): string
    {
        if (isset($this->itemContainerWrapperView) && !blank($this->itemContainerWrapperView)) {
            return $this->itemContainerWrapperView;
        }

        if (!$this->hasDefaultItemContainerWrapper) {
            return 'sn-cms::base.empty';
        }

        return Utils::getThemeContainer('item-container') ?? 'sn-cms::base.item-container';
    }
}
