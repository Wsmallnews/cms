<?php

namespace Wsmallnews\Cms\Commands;

use Illuminate\Support\Facades\Artisan;
use Wsmallnews\Cms\CmsServiceProvider;
use Wsmallnews\Support\Commands\PackageInstallCommand;

class CmsInstallCommand extends PackageInstallCommand
{
    protected string $packageName = 'sn-cms';

    /**
     * settings 数据迁移（spatie/laravel-settings，应用 database/settings 目录）
     */
    protected function afterPublish(): void
    {
        $this->components->task('Publishing settings migrations', function () {
            return Artisan::call('vendor:publish', [
                '--provider' => CmsServiceProvider::class,
                '--tag' => 'sn-cms-settings',
                '--no-interaction' => true,
            ]) === self::SUCCESS;
        });
    }
}
