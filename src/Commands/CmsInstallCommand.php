<?php

namespace Wsmallnews\Cms\Commands;

use Wsmallnews\Support\Commands\PackageInstallCommand;

class CmsInstallCommand extends PackageInstallCommand
{
    protected string $packageName = 'sn-cms';
}
