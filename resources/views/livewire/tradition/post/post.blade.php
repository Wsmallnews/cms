@php
    use Wsmallnews\Cms\Support\Utils;

    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();

    $user = Utils::getUser();

    $canComment = Utils::canComment('post');
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="container mx-auto flex flex-col grow gap-4 my-4">
        @if($breadcrumbs)
            <div class="sn-descript-text w-full flex items-center gap-2 text-left">
                当前位置 :
                <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        @endif

        <livewire:sn-cms-components-post 
            :scope-type="$scopeType" 
            :scope-id="$scopeId" 
            :user="$user" :slug="$slug" 
            :can-comment="$canComment"
        />
    </div>
</x-dynamic-component>