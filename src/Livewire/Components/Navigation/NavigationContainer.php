<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Illuminate\Support\Arr;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Facades\ContentRegistry;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;

class NavigationContainer extends Base
{
    use Navigationable;

    public string $slug;

    public function render()
    {
        $navigation = $this->getScopedQuery()->normal()->withDepth()->where('slug', $this->slug)->firstOrFail();

        if ($navigation->type == NavigationTypeEnum::Content) {
            $scopeType = $this->getScopeType();
            
            $components = [];
            $optionComponents = $navigation->options['components'] ?? [];
            foreach ($optionComponents as $optionComponent) {

                // 根据当前导航的内容类型，获取导航的设置
                $typeInfo = ContentRegistry::getType($scopeType, $optionComponent['type']);
    
                $currentComponents = $typeInfo['components'] ?? $typeInfo['component'];
                $currentComponents = Arr::wrap($currentComponents);
    
                $currentComponents = Arr::mapWithKeys($currentComponents, function ($currentComponent, $key) use ($optionComponent) {
                    $extras = $optionComponent['extras'] ?? [];          // 额外表单参数，和固定参数合并
    
                    // 如果未开启自定义视图，则移除自定义视图参数
                    $hasCustomView = $extras['hasCustomView'] ?? false;
                    unset($extras['hasCustomView']);
                    if (! $hasCustomView) {
                        unset($extras['view']);
                    }
    
                    return is_scalar($currentComponent) ? [$currentComponent => $extras] : [$key => array_merge($currentComponent, $extras)];
                });
    
                $components = array_merge($components, $currentComponents);
            }
        } elseif ($navigation->type == NavigationTypeEnum::Page) {
            $contentData = [
                'content' => $navigation->content,
            ];

            // 自定义视图参数
            $hasCustomView = $navigation->options['_content_views']['hasCustomView'] ?? false;
            $blockContainerWrapperView = $navigation->options['_content_views']['view'] ?? null;
            if ($hasCustomView) {
                $contentData['view'] = $blockContainerWrapperView;
            }

            // block 容器参数
            $hasDefaultBlockContainerWrapper = $navigation->options['_content_block_container']['hasDefaultBlockContainerWrapper'] ?? false;
            $blockContainerWrapperView = $navigation->options['_content_block_container']['blockContainerWrapperView'] ?? null;
            $contentData['hasDefaultBlockContainerWrapper'] = $hasDefaultBlockContainerWrapper;
            $contentData['blockContainerWrapperView'] = $blockContainerWrapperView;

            $components = [
                Content::class => $contentData,
            ];
        }

        return view($this->getThemeView('components.navigation.navigation-container'), [
            'navigation' => $navigation,
            'components' => $components ?? [],
        ]);
    }
}
