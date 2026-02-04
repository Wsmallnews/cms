<?php

namespace Wsmallnews\Cms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static register(string $scopeType, array $typeInfo)
 * @method static static registers(string $scopeType, array $typeInfos)
 * @method static Collection getScopes()
 * @method static Collection getTypes(string $scopeType)
 * @method static array|null getType(string $scopeType, string $type)
 * @method static array getTypesOptions(string $scopeType)
 * @method static bool hasTypeForms(string $scopeType, string $type, array $arguments = [])
 * @method static array getTypeForms(string $scopeType, string $type, array $arguments = [])
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
