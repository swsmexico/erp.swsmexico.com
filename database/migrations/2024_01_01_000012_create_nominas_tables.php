<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rfc', 13)->nullable();
            $table->string('curp', 18)->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->decimal('sueldo_mensual', 12, 2);
            $table->date('fecha_ingreso');
            $table->string('cuenta_bancaria')->nullable();
            $table->string('clabe', 18)->nullable();
            $table->enum('periodicidad_pago', ['semanal', 'quincenal', 'mensual'])->default('mensual');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('nomina_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->date('fecha_pago');
            $table->decimal('monto', 12, 2);
            $table->enum('tipo', ['sueldo', 'bono_fijo', 'bono_extraordinario']);
            $table->text('descripcion')->nullable();
            $table->foreignId('cuenta_id')->nullable()->constrained('cuentas')->nullOnDelete();
            $table->foreignId('movimiento_id')->nullable()->constrained('movimientos')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('recibos_sat_nomina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('uuid', 36)->unique()->nullable();
            $table->date('fecha');
            $table->decimal('total', 12, 2);
            $table->string('archivo_xml')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recibos_sat_nomina');
        Schema::dropIfExists('nomina_pagos');
        Schema::dropIfExists('empleados');
    }
};
