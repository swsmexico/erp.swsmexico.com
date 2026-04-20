<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_estados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color', 7)->default('#5CC8F2');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('prospectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_comercial');
            $table->decimal('valor', 14, 2)->nullable();
            $table->foreignId('estado_id')->constrained('kanban_estados')->restrictOnDelete();
            $table->foreignId('vendedor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('proximo_seguimiento')->nullable();
            $table->date('ultimo_seguimiento')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prospecto_contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospecto_id')->constrained('prospectos')->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('prospecto_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospecto_id')->constrained('prospectos')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('archivo');
            $table->timestamps();
        });

        Schema::create('prospecto_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospecto_id')->constrained('prospectos')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('archivo_pdf')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('incluye_iva')->default(true);
            $table->timestamps();
        });

        Schema::create('prospecto_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospecto_id')->constrained('prospectos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->text('mensaje_original')->comment('Texto informal del usuario');
            $table->text('mensaje_formal')->comment('Versión pulida por IA');
            $table->enum('canal', ['correo', 'whatsapp']);
            $table->timestamp('enviado_at')->nullable();
            $table->text('respuesta')->nullable();
            $table->timestamp('respuesta_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospecto_seguimientos');
        Schema::dropIfExists('prospecto_cotizaciones');
        Schema::dropIfExists('prospecto_documentos');
        Schema::dropIfExists('prospecto_contactos');
        Schema::dropIfExists('prospectos');
        Schema::dropIfExists('kanban_estados');
    }
};
