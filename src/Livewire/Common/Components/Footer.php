<?php

namespace Wsmallnews\Cms\Livewire\Common\Components;

use Wsmallnews\Cms\Models\Navigation as NavigationModel;

// use Wsmallnews\Cms\Settings\GeneralSettings;

class Footer extends BaseComponent
{
    public function render()
    {
        $navigations = NavigationModel::scoped(has_tenancy() ? ['team_id' => current_tenant()->id] : [])
            ->normal()
            ->where(function ($query) {
                $query->where('options->footer_show', true)
                    ->orWhereNull('options->footer_show');
            })
            ->defaultOrder()->get()
            ->map(function (NavigationModel $navigation) {
                return $navigation->resolveNavigation($navigation);
            })->toTree();

        return view('sn-cms::livewire.common.components.footer', [
            'navigations' => $navigations,
            // 'general' => app(GeneralSettings::class)
        ]);
    }
}
