@php
    $record = $getRecord();

    $user = $record->publisher;
    $src = filament()->getUserAvatarUrl($user);
    $alt = __('filament-panels::layout.avatar.alt', ['name' => filament()->getUserName($user)]);
@endphp

<div class="w-40 flex gap-2">
    <x-filament::avatar
        :src="$src"
        :alt="$alt"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->class(['fi-user-avatar'])
        "
    />

    <div class="shrink-0">
        {{ filament()->getUserName($user) }}
    </div>
</div>