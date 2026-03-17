@php
    use Filament\Support\View\Components\ButtonComponent;
    use Wsmallnews\Cms\Facades\FlagRegistry;

    $record = $getRecord();
    $flags = $record->flags ?? [];

    $flagTypes = FlagRegistry::getTypes($scopeType)
@endphp

<div class="flex gap-2">
    @foreach ($flags as $flag)
        @php
            $typeInfo = $flagTypes[$flag] ?? null;
            $label = $typeInfo['label'] ?? null;
            $color = $typeInfo['color'] ?? 'primary';
            $fontColor = $typeInfo['fontColor'] ?? '#ffffff';
            $icon = $typeInfo['icon'] ?? null;

            $colorClass = null;
            if (in_array($color, ['danger', 'gray', 'info', 'primary', 'success', 'warning'])) {
                $colorClass = 'fi-color fi-bg-color-400 dark:fi-bg-color-600 fi-text-color-900 dark:fi-text-color-950';
                $colorClass .= ' bg-(--bg) text-(--text) dark:bg-(--dark-bg) dark:text-(--dark-text)';
                $colorClass .= ' fi-color-' . $color;
            }

            $styleClass = null;
            if (!$colorClass) {
                $styleClass = 'background-color: ' . $color . ';';
                $styleClass .= ' color: ' . $fontColor . ';';
            }
        @endphp

        @if ($typeInfo)
            <div
                {{-- {{
                    $attributes
                        ->class([
                            'fi-btn',
                            "fi-size-sm",
                        ])
                        ->color(app(ButtonComponent::class, []), $color)
                }} --}}
                @class([
                    'flex items-center gap-1 px-2 py-1 text-sm rounded-md',
                    $colorClass,
                ])
                style="{{ $styleClass }}"
            >
                @if ($icon)
                    <x-filament::icon
                        :icon="$icon"
                        class="size-2"
                    />
                @endif
                {{ $label }}
            </div>
        @endif
    @endforeach
</div>