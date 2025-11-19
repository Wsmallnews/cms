<x-dynamic-component :component="$this->getBlockContainerWrapperView()" class="w-full flex items-center gap-2 text-sm text-gray-500 text-left">
    当前位置 :
    <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
</x-dynamic-component>