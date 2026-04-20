<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historial de fechas promesa por prefactura
        Schema::create('cobranza_promesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefactura_id')->constrained('prefacturas')->cascadeOnDelete();
            $table->date('fecha_promesa');
            $table->text('comentario')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Comunicaciones automáticas y manuales por prefactura
        Schema::create('cobranza_comunicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefactura_id')->constrained('prefacturas')->cascadeOnDelete();
            $table->enum('tipo', ['correo', 'whatsapp']);
            $table->enum('origen', ['automatico', 'manual']);
            $table->text('mensaje');
            $table->timestamp('enviado_at')->nullable();
            $table->text('respuesta')->nullable();
            $table->timestamp('respuesta_at')->nullable();
            $table->timestamps();
        });

        // Reglas de notificación personalizadas por cliente
        Schema::create('cobranza_reglas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->cascadeOnDelete()
                  ->comment('null = regla global del sistema');
            $table->integer('dias_antes')->default(3)->comment('Días antes del vencimiento para primer aviso');
            $table->boolean('aviso_correo_antes')->default(true);
            $table->boolean('aviso_vencimiento_correo')->default(true);
            $table->boolean('aviso_vencimiento_whatsapp')->default(true);
            $table->boolean('aviso_post_correo_diario')->default(true);
            $table->boolean('aviso_post_whatsapp_diario')->default(true);
            $table->timestamps();
        });

        // Pagos múltiples: un movimiento que cubre N prefacturas
        Schema::create('cobranza_pagos_grupales', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_pago');
            $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            $table->decimal('total', 14, 2);
            $table->foreignId('movimiento_id')->nullable()->constrained('movimientos')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('cobranza_pagos_grupales_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_grupal_id')->constrained('cobranza_pagos_grupales')->cascadeOnDelete();
            $table->foreignId('prefactura_id')->constrained('prefacturas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobranza_pagos_grupales_items');
        Schema::dropIfExists('cobranza_pagos_grupales');
        Schema::dropIfExists('cobranza_reglas');
        Schema::dropIfExists('cobranza_comunicaciones');
        Schema::dropIfExists('cobranza_promesas');
    }
};
