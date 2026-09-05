<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Illuminate\Support\Arr;
use Wsmallnews\Cms\Enums\NavigationType as NavigationTypeEnum;
use Wsmallnews\Cms\Facades\ContentRegistry;
use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class NavigationContainer extends Base
{
    use Navigationable;

    public string $slug;

    public function render()
    {
        $navigationModel = new (Utils::getNavigationModel());

        // 导航类型不存在时导航页本身不可达，直接 404
        if (! $this->hasNavigationType()) {
            abort(404);
        }

        $navigation = $this->getScopedQuery()->normal()->withDepth()->where($navigationModel->getRouteKeyName(), $this->slug)->firstOrFail();

        // 导航页 SEO：以导航名称为标题、导航描述为页面描述
        Seo::title($navigation->name)->description($navigation->description);

        if ($navigation->type == NavigationTypeEnum::Content) {
            $scopeType = $this->getScopeType();

            $components = [];
            $optionComponents = $navigation->options['components'] ?? [];
            foreach ($optionComponents as $optionComponent) {

                // 根据当前导航的内容类型，获取导航的设置
                $typeInfo = ContentRegistry::getType($scopeType, $optionComponent['type']);

                $currentComponents = $typeInfo['components'] ?? $typeInfo['component'];
                $currentComponents = Arr::wrap($currentComponents);

                $currentComponents = Arr::map($currentComponents, function ($currentComponent, $key) use ($optionComponent) {
                    $extras = $optionComponent['extras'] ?? [];          // 额外表单参数，和固定参数合并
                    $extras['componentInfo'] = [
                        'type' => $optionComponent['type'],
                        'label' => $optionComponent['label'] ?? null,
                    ];

                    if (is_scalar($currentComponent)) {
                        return [
                            'component_name' => $currentComponent,
                            'extras' => $extras,
                        ];
                    }

                    return [
                        'component_name' => $key,
                        'extras' => array_merge($currentComponent, $extras),
                    ];
                });

                $components = array_merge($components, array_values($currentComponents));
            }
        } elseif ($navigation->type == NavigationTypeEnum::Page) {
            $contentData = [
                'content' => $navigation->content,
            ];

            $components = [
                [
                    'component_name' => Content::class,
                    'extras' => $contentData,
                ],
            ];
        }

        return view($this->getThemeView('components.navigation.navigation-container'), [
            'navigation' => $navigation,
            'components' => $components ?? [],
        ]);
    }
}
