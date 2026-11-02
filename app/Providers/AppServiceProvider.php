<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Blade::anonymousComponentPath(resource_path('views/emails/components'), 'emails');
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });
    }
}
