<?php

namespace Wsmallnews\Cms\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Wsmallnews\Cms\Support\SiteUtils;

class Authenticate extends BaseAuthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @return string|null
     */
    protected function redirectTo(Request $request)
    {
        return SiteUtils::route('login');
    }
}
