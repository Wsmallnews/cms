<x-filament-widgets::widget>
    <livewire:sn-cms-fi-navigation :navigation-type="$record" :properties="$properties" :key="'components-' . $record->id . '-' . $record->level"/>
</x-filament-widgets::widget>
