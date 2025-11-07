<?php

namespace Wsmallnews\Cms\Livewire\Components\Navigation;

use Wsmallnews\Cms\Livewire\Components\Base;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;

class Navigation extends Base
{
    public ?int $navigationTypeId = null;

    public function getNavigations()
    {
        $navigationType = NavigationTypeModel::scopeable(...$this->getScopeable())->when($this->navigationTypeId, function ($query) {
            $query->where('id', $this->navigationTypeId);
        })->firstOrFail();

        $scoped = [
            ...$this->getScopeable(),
            'type_id' => $navigationType->id,
        ];
        has_tenancy() && $scoped['team_id'] = current_tenant()?->id;

        return NavigationModel::scoped($scoped)
            ->normal()->defaultOrder()->get()
            ->map(function (NavigationModel $navigation) {
                return $navigation->resolveNavigation($navigation);
            })->toTree();
    }

    public function render()
    {
        return view('sn-cms::livewire.components.navigation.navigation');
    }
}
