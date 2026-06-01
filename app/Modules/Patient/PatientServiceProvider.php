<?php

namespace App\Modules\Patient;

use Illuminate\Support\ServiceProvider;

class PatientServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Enregistrement des repositories
        $this->app->bind(
            \App\Modules\Patient\Repositories\PatientRepositoryInterface::class,
            \App\Modules\Patient\Repositories\PatientRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrement de l'Observer
        \App\Modules\Patient\Models\Patient::observe(\App\Modules\Patient\Observers\PatientObserver::class);

        // Chargement des routes du module
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
    }
}
