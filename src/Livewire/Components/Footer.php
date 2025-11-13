<?php

namespace Wsmallnews\Cms\Livewire\Components;

use Wsmallnews\Cms\Livewire\Concerns\Navigationable;
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
            ->toTree();

        return view('sn-cms::livewire.components.footer', [
            'navigations' => $navigations,
            'general' => app(GeneralSettings::class),
        ]);
    }
}
