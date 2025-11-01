<?php

namespace Wsmallnews\Cms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wsmallnews\Cms\Cms
 */
class Cms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wsmallnews\Cms\Cms::class;
    }
}
