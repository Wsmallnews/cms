<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Facades\Seo;

class VerifyEmail extends Base
{
    public string $type = 'check';

    public function mount()
    {
        $this->type = request()->query('type', 'check');
    }

    public function render()
    {
        Seo::title(__('sn-cms::cms.auth.verify_email'))->robots('noindex');

        return view($this->getThemeView('auth.verify-email'), [
        ])->layout(Utils::getLayout());
    }
}
