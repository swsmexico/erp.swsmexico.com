<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->string('correo')->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('puesto')->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('es_contacto_pago')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
