@props(['record', 'first', 'last', 'current-level'])

@php
    $hasChild = $record->children->count() > 0;
    $hasActive = $this->getHasActive($record);
@endphp

<li
    class="flex flex-col"
    @if ($hasChild)
        x-data="{ isExpanded: {{ $hasActive ? 'true' : 'false' }} }"
        aria-controls="accordionItem{{$record->id}}"
        :aria-expanded="isExpanded ? 'true' : 'false'"
        aria-haspopup="true"
    @endif
    role="menuitem"
>
    <a @class([
            'flex w-full h-14 justify-between items-center px-4 gap-2 font-bold text-white group',
            'hover:bg-primary-600 dark:hover:bg-primary-700' => !$hasActive,
            'bg-primary-600 dark:bg-primary-700' => $hasActive,
        ])
        @if ($hasChild)
            @click="isExpanded = ! isExpanded"
            wire:click="$dispatch('sn-filament-nestedset-node-click', { recordId: {{ $record->id }}, hasChild: {{ $hasChild ? 1 : 0 }} })"
            {{ $this->getRecordUrl($record) ?? 'href=javascript:;' }}
        @else
            wire:click="$dispatch('sn-filament-nestedset-leaf-click', { recordId: {{ $record->id }}, hasChild: {{ $hasChild ? 1 : 0 }} })"
            {{ $this->getRecordUrl($record) ?? 'href=javascript:;' }}
        @endif
    >
        <div class="flex items-center gap-1">
            @if ($currentLevel > 1)
                @for ($i = 0; $i < ($currentLevel - 1); $i++)
                    {{-- 填充 --}}
                    <div class="w-4"></div>
                @endfor
            @endif
            
            {{ $this->getRecordLabel($record) }}
        </div>
        @if ($hasChild)
            <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-bold transform transition-transform duration-300" ::class="isExpanded ? 'rotate-180' : ''" aria-hidden="true" />
        @endif
    </a>

    @if ($hasChild) 
        @php
            $currentLevel++;
        @endphp
        <ul @class([
            'w-full flex flex-col border-t border-primary-400 divide-y divide-primary-400',
        ])
            id="accordionItemCategory{{$record->id}}"
            x-cloak x-show="isExpanded"
            aria-labelledby="controlsAccordionItemOne{{$record->id}}"
            x-collapse
            role="menu"
        >
            @foreach ($record->children as $child)
                <x-dynamic-component 
                    @class([
                        'w-full',
                    ]) 
                    :component="$this->getRecordView()" 
                    key="categories-component-{{ $child->getKey() }}" 
                    :record="$child" 
                    :first="$loop->first" 
                    :last="$loop->last" 
                    :current-level="$currentLevel" 
                />
            @endforeach
        </ul>
    @endif 
</li>
