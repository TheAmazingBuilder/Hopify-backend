<?php

namespace App\Modules\Doctor;

use Illuminate\Support\ServiceProvider;

class DoctorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindings des Repositories Doctor si nécessaire
    }

    public function boot(): void
    {
        if (file_exists(__DIR__ . '/Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        }

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
