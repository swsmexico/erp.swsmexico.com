<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->enum('estatus', ['en_progreso', 'en_pausa', 'terminado', 'cancelado'])
                  ->default('en_progreso');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('proyecto_cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('nombre');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('modo', ['fecha', 'manual'])->default('manual');
            $table->date('fecha_programada')->nullable()->comment('Solo si modo=fecha');
            $table->timestamp('disparado_at')->nullable()->comment('Cuando se presionó Listo para cobranza');
            $table->foreignId('prefactura_id')->nullable()->constrained('prefacturas')->nullOnDelete()
                  ->comment('Prefactura generada al disparar el cobro');
            $table->string('folio_factura', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('proyecto_cobro_evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_cobro_id')->constrained('proyecto_cobros')->cascadeOnDelete();
            $table->string('titulo')->nullable();
            $table->string('archivo');
            $table->timestamps();
        });

        Schema::create('proyecto_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('archivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_documentos');
        Schema::dropIfExists('proyecto_cobro_evidencias');
        Schema::dropIfExists('proyecto_cobros');
        Schema::dropIfExists('proyectos');
    }
};
