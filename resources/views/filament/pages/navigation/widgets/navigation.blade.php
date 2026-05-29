<x-filament-widgets::widget>
    <livewire:sn-cms-fi-navigation
        :navigation-type="$record"
        :properties="$properties"
        :contained="$contained"
        :key="'fi-components-sn-navigation-' . $record->id . '-' . $record->level"
    />
</x-filament-widgets::widget>
