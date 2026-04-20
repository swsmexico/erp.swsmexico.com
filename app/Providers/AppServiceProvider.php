<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Compartir permisos del usuario autenticado con todas las páginas Vue
        Inertia::share([
            'auth' => function () {
                $user = Auth::user();
                if (! $user) return ['user' => null, 'permissions' => []];
                return [
                    'user' => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                        'roles' => $user->getRoleNames(),
                    ],
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ];
            },
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error'   => session('error'),
                    'warning' => session('warning'),
                ];
            },
        ]);
    }
}
