<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
use Wsmallnews\Cms\Models\Navigation as NavigationModel;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Settings\GeneralSettings;

class Footer extends Base
{
    use Navigationable;

    public function render()
    {
        $navigations = $this->getScopedQuery()->normal()
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
