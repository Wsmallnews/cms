<x-dynamic-component :component="$this->getBlockContainerWrapperView()" class="w-full">
    {!! $content?->content !!}
</x-dynamic-component>