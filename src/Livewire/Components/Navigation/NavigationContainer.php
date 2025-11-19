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
            // 根据当前导航的内容类型，获取导航的设置
            $type = ContentRegistry::getType($navigation->options['type']);

            $components = $type['components'] ?? $type['component'];
            $components = Arr::wrap($components);

            $components = Arr::mapWithKeys($components, function ($component, $key) use ($navigation) {
                $extras = $navigation->options['_extras'] ?? [];          // 额外表单参数，和固定参数合并

                return is_scalar($component) ? [$component => $extras] : [$key => array_merge($component, $extras)];
            });
        } elseif ($navigation->type == NavigationTypeEnum::Page) {
            $components = [
                Content::class => [         // 内容组件
                    'content' => $navigation->content,
                ],
            ];
        }

        return view($this->getView('components.navigation.navigation-container'), [
            'navigation' => $navigation,
            'components' => $components ?? [],
        ]);
    }
}
