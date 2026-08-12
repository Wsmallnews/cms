<?php

namespace Wsmallnews\Cms\Livewire\Auth;

use Wsmallnews\Cms\Livewire\Base;
use Wsmallnews\Cms\Support\Utils;

class VerifyEmail extends Base
{
    public string $type = 'check';

    public function mount()
    {
        $this->type = request()->query('type', 'check');
    }

    public function render()
    {
        return view($this->getThemeView('auth.verify-email'), [
        ])->layout(Utils::getLayout())->title(__('sn-cms::cms.auth.verify_email'));
    }
}
