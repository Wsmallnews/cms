<?php

namespace Wsmallnews\Cms\Filament\Pages\Navigation\Footer;

use Wsmallnews\Support\Filament\Concerns\CanBeConfigured;

/**
 * 底部导航管理页面（配置解析层）：与头部 NavigationPage 同构。
 * 数据分区由类级 getScopeSuffix() 声明为 footer 子 scope（{site.scopeType}::footer），
 * 与前台 Footer 组件共用同一后缀字符串；默认值（图标、slug、层级、标签翻译）在 Footer\Base。
 */
final class FooterNavigationPage extends Base
{
    use CanBeConfigured;

    /** Footer data lives in the site's footer sub-scope. */
    public static function getScopeSuffix(): ?string
    {
        return 'footer';
    }

    public static function getEmptyLabel(): ?string
    {
        return self::resolveCustomProperty('emptyLabel') ?? parent::getEmptyLabel();
    }

    public static function getEmptyTipLabel(): ?string
    {
        return self::resolveCustomProperty('emptyTipLabel') ?? parent::getEmptyTipLabel();
    }
}
