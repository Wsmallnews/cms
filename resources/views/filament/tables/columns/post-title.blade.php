@php
    $record = $getRecord();
@endphp

<div class="w-80 flex flex-col p-2 gap-2">
    <div class="sn-content-text w-full truncate" title="{{ $record->title }}">
        {{ $record->title }}
    </div>
    <div class="sn-tip-text w-full truncate" title="{{ $record->description }}">
        {{ $record->description }}
    </div>
</div>