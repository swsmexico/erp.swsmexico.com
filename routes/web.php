<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\DashboardController;

// ── Rutas autenticadas ────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.ver');

    // API interna
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/buscar', [BusquedaController::class, 'index'])->name('buscar');
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::post('/notificaciones/leer-todas', [NotificacionController::class, 'leerTodas'])->name('notificaciones.leerTodas');
        Route::patch('/notificaciones/{notificacion}/leer', [NotificacionController::class, 'leer'])->name('notificaciones.leer');
    });

    // ── Módulos (se irán agregando por módulo) ────────────────────────────────
    require __DIR__.'/modules/clientes.php';
    require __DIR__.'/modules/prospectos.php';
    require __DIR__.'/modules/cobranza.php';
    require __DIR__.'/modules/estados.php';
    require __DIR__.'/modules/pagos.php';
    require __DIR__.'/modules/nominas.php';
    require __DIR__.'/modules/proyectos.php';
    require __DIR__.'/modules/reporte.php';
    require __DIR__.'/modules/configuracion.php';
});

require __DIR__.'/auth.php';
