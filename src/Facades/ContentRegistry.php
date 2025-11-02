<?php

namespace Wsmallnews\Cms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static register(array $typeInfo)
 * @method static static registers(array $typeInfos)
 * @method static Collection getTypes()
 * @method static array getType(string $type)
 * @method static array getOptions()
 * @method static bool hasForms(string $type, array $arguments = [])
 * @method static array getTypeForms(string $type, array $arguments = [])
 *
 * @see \Wsmallnews\Cms\ContentRegistry
 */
class ContentRegistry extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wsmallnews\Cms\ContentRegistry::class;
    }
}
