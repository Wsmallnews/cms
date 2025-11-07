<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Settings\GeneralSettings;

class Footer extends Base
{
    public ?int $navigationTypeId = null;

    public function render()
    {
        $navigationType = NavigationTypeModel::scopeable(...$this->getScopeable())->when($this->navigationTypeId, function ($query) {
            $query->where('id', $this->navigationTypeId);
        })->firstOrFail();

        $scoped = [
            ...$this->getScopeable(),
            'type_id' => $navigationType->id,
        ];
        has_tenancy() && $scoped['team_id'] = current_tenant()?->id;

        $navigations = NavigationModel::scoped($scoped)
            ->normal()
            ->where(function ($query) {
                $query->where('options->footer_show', true)
                    ->orWhereNull('options->footer_show');
            })
            ->defaultOrder()->get()
            ->map(function (NavigationModel $navigation) {
                return $navigation->resolveNavigation($navigation);
            })->toTree();

        return view('sn-cms::livewire.components.footer', [
            'navigations' => $navigations,
            'general' => app(GeneralSettings::class),
        ]);
    }
}
