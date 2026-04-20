<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;

Route::middleware('permission:clientes.ver')
    ->prefix('clientes')
    ->name('clientes.')
    ->group(function () {

        Route::get('/',           [ClienteController::class, 'index'])->name('index');
        Route::get('/{cliente}',  [ClienteController::class, 'show'])->name('show');

        Route::middleware('permission:clientes.crear')->group(function () {
            Route::get('/nuevo',  [ClienteController::class, 'create'])->name('create');
            Route::post('/',      [ClienteController::class, 'store'])->name('store');
        });

        Route::middleware('permission:clientes.editar')->group(function () {
            Route::get('/{cliente}/editar',              [ClienteController::class, 'edit'])->name('edit');
            Route::patch('/{cliente}',                   [ClienteController::class, 'update'])->name('update');
            Route::post('/{cliente}/contactos',          [ClienteController::class, 'storeContacto'])->name('contactos.store');
            Route::patch('/{cliente}/contactos/{contacto}', [ClienteController::class, 'updateContacto'])->name('contactos.update');
            Route::delete('/{cliente}/contactos/{contacto}',[ClienteController::class, 'destroyContacto'])->name('contactos.destroy');
        });
    });
