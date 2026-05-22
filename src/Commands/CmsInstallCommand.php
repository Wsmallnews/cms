<?php

namespace Wsmallnews\Cms\Commands;

use Illuminate\Support\Facades\Artisan;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;

class CmsInstallCommand extends InstallCommand
{
    public function __construct(Package $package)
    {
        $package->name('sn-cms');

        parent::__construct($package);

        $this->signature = 'sn-cms:install
                            {--no-deps : Install without dependencies and skip interactive prompts}';
        $this->description = 'Install sn-cms';
        $this->hidden = false;

        $this->configureUsingFluentDefinition();
        $this->specifyParameters();

        $this->publishConfigFile();
        $this->publishMigrations();
        $this->askToRunMigrations();
        $this->askToStarRepoOnGitHub('wsmallnews/cms');
    }

    public function handle()
    {
        $noDeps = $this->option('no-deps');
        $isDependency = ! $this->input->isInteractive();

        if ($noDeps || $isDependency) {
            $this->askToRunMigrations = false;
            $this->starRepo = null;
        }

        if (! $noDeps) {
            // 安装 wsmallnews/support
            $this->comment('Installing dependency: wsmallnews/support');
            $this->comment(str_repeat('─', 46));

            Artisan::call('sn-support:install', [
                '--no-interaction' => true,
            ], $this->getOutput());

            $this->newLine();

            // 安装 wsmallnews/comment
            $this->comment('Installing dependency: wsmallnews/comment');
            $this->comment(str_repeat('─', 46));

            Artisan::call('sn-comment:install', [
                '--no-deps' => true,
                '--no-interaction' => true,
            ], $this->getOutput());

            $this->newLine();

            // 安装 wsmallnews/preference
            $this->comment('Installing dependency: wsmallnews/preference');
            $this->comment(str_repeat('─', 46));

            Artisan::call('sn-preference:install', [
                '--no-deps' => true,
                '--no-interaction' => true,
            ], $this->getOutput());

            $this->newLine();
        }

        parent::handle();

        return self::SUCCESS;
    }
}
