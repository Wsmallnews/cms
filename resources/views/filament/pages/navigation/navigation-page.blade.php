@php
    $canManage = static::getCanManage();
@endphp

<x-filament-panels::page>
    @if ($canManage)
        {{ $this->form }}
    @endif

    {{ $this->content }}

    @if ($navigationType)
        <livewire:sn-filament-nestedset-fi-nestedset
            :page-class="static::class"
            :active-tab="$activeTab"
            :model="static::getModel()"
            :tab-field-name="static::getTabFieldName()"
            :record-title-attribute="static::getRecordTitleAttribute()"
            :level="static::getLevel()"
            :empty-label="static::getEmptyLabel()"
            :empty-tip-label="static::getEmptyTipLabel()"
            :is-scoped-to-tenant="static::isScopedToTenant()"
            :key="'fi-components-sn-navigation-' . $navigationType->id . '-' . $navigationType->level"
        />
    @endif
</x-filament-panels::page>
