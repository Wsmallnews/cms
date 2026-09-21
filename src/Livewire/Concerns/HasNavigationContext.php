<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

/**
 * 导航形态上下文（cms 领域语义）：navigation 配置节的便捷读取。
 *
 * 依赖 support 的 HasModuleContext（moduleConfig 的模块优先/owner 回落机制）；
 * navigation 节是 cms 发明的领域配置结构，不放 support。
 */
trait HasNavigationContext
{
    /**
     * 导航配置读取（消费模块的 navigation 节优先，回落组件所有方）
     */
    public function navigationConfig(?string $key = null, mixed $default = null): mixed
    {
        return $this->moduleConfig('navigation' . ($key ? '.' . $key : ''), $default);
    }

    /**
     * PC 主行父项是否可点击（仅 hover 级联生效：直达第一个可用叶子）
     */
    public function isDesktopParentClickable(): bool
    {
        if ($this->navigationConfig('desktop_submenu_style', 'cascade') !== 'cascade'
            || $this->navigationConfig('desktop_submenu_trigger', 'hover') !== 'hover') {
            return false;
        }

        return (bool) $this->navigationConfig('parent_clickable', true);
    }
}
