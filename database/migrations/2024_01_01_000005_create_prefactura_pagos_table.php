<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prefactura_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefactura_id')->constrained('prefacturas')->cascadeOnDelete();
            $table->year('anio')->comment('Año al que corresponde este pago');
            $table->date('fecha_promesa')->nullable()->comment('Día 1 del mes de vencimiento de ese año');
            $table->date('fecha_pago')->nullable()->comment('Fecha real de pago, null = pagada sin fecha');
            $table->string('folio_factura', 20)->nullable()->comment('C-XXXX del sistema anterior');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('cuenta')->nullable()->comment('SWS Banamex o LLC Banamex');
            $table->foreignId('movimiento_id')->nullable()->constrained('movimientos')->nullOnDelete()
                  ->comment('Entrada generada en Estados de Cuenta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefactura_pagos');
    }
};
