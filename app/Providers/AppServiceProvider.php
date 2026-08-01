<?php

namespace App\Providers;

use App\Models\Permiso;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        // RBAC: el administrador puede todo; el resto según sus permisos por rol.
        Gate::before(function ($user) {
            return $user->role && $user->role->nombre === 'administrador' ? true : null;
        });

        try {
            if (Schema::hasTable('permisos')) {
                foreach (Permiso::pluck('clave') as $clave) {
                    Gate::define($clave, fn ($user) => $user->tienePermiso($clave));
                }
            }
        } catch (\Throwable $e) {
            // La BD aún no está lista (p. ej. durante migraciones): se ignora.
        }
    }
}
