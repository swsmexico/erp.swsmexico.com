<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('tipo', ['deposito', 'retiro', 'transferencia']);
            $table->string('descripcion');
            $table->decimal('monto', 14, 2);
            $table->decimal('saldo_tras_movimiento', 14, 2)->nullable();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('proveedor')->nullable();
            $table->enum('origen', ['manual', 'cobranza', 'pago', 'nomina', 'transferencia'])
                  ->default('manual');
            $table->morphs('relacionable');
            // relacionable_type + relacionable_id → polimórfico hacia prefactura_pagos, pagos, nomina_pagos
            $table->foreignId('cuenta_destino_id')->nullable()->constrained('cuentas')->nullOnDelete()
                  ->comment('Solo para transferencias entre cuentas');
            $table->timestamps();

            $table->index(['cuenta_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
