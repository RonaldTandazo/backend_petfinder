<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fuera de `local`, Scramble (RestrictedDocsAccess) exige este Gate
        // para ver /docs/api — solo cuentas autenticadas, no público abierto.
        Gate::define('viewApiDocs', fn ($user = null) => $user !== null);
    }
}
