@php
    $nestedset = $this->getNestedset();
    $style = 'simple';
@endphp

<ul
    @class([
        'w-full flex flex-col',
        'sn-primary-bg divide-y divide-primary-400' => $style === 'vivid',
        'space-y-1 mt-1' => $style === 'simple',
    ])
    role="menu"
>
    @forelse($nestedset as $treeKey => $record)
        <x-dynamic-component 
            @class([
                'w-full',
            ]) 
            :component="$this->getRecordView()" 
            key="nestedset-record-component-{{ $record->getKey() }}" 
            :record="$record" 
            :first="$loop->first" 
            :last="$loop->last" 
            :style="$style" 
            :current-level="1" 
        />
    @empty
        <li 
            class="w-full px-3 py-2 text-center"
        >
            {{ $this->getEmptyLabel() ?: __('sn-filament-nestedset::nestedset.nestedset.empty_label')}}
        </li>
    @endforelse
</ul>
