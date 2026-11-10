<?php

namespace Wsmallnews\Cms\Http\Middleware;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated as BaseRedirectIfAuthenticated;
use Illuminate\Http\Request;
use Wsmallnews\Cms\Support\SiteUtils;

class RedirectIfAuthenticated extends BaseRedirectIfAuthenticated
{
    /**
     * Get the path the user should be redirected to when they are authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return SiteUtils::route('profile');
    }
}
