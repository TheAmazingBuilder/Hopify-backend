<?php

namespace App\Modules\Hr;

use Illuminate\Support\ServiceProvider;

class HrServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindings des Repositories HR si nécessaire
    }

    public function boot(): void
    {
        if (file_exists(__DIR__ . '/Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        }
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
