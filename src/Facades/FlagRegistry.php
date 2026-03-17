<?php

namespace Wsmallnews\Cms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static register(string $scopeType, array $flagInfo)
 * @method static static registers(string scopeType, array $flagInfos)
 * @method static \Illuminate\Support\Collection getFlags()
 * @method static \Illuminate\Support\Collection getTypes(string $scopeType)
 * @method static array getType(string $scopeType, string $type)
 * @method static array getTypesOptions(string $scopeType)
 * @method static array getTypesColors(string $scopeType)
 * @method static array getTypesIcons(string $scopeType)
 *
 * @see \Wsmallnews\Cms\FlagRegistry
 */
class FlagRegistry extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wsmallnews\Cms\FlagRegistry::class;
    }
}
