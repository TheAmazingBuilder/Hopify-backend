<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
    }

    public function boot(): void
    {
        //
    }
}