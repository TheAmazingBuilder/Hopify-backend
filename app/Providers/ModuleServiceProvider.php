<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesPath = app_path('Modules');

        if (! is_dir($modulesPath)) {
            return;
        }

        $finder = new Finder();
        $finder->directories()->in($modulesPath)->depth(0);

        foreach ($finder as $moduleDir) {
            $moduleName = $moduleDir->getFilename();
            $providerClass = "App\\Modules\\{$moduleName}\\{$moduleName}ServiceProvider";

            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    public function boot(): void
    {
        //
    }
}