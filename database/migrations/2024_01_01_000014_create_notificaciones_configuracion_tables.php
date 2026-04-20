<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('cuerpo')->nullable();
            $table->string('relacionable_type')->nullable();
            $table->unsignedBigInteger('relacionable_id')->nullable();
            $table->timestamp('leida_at')->nullable();
            $table->timestamps();

            $table->index(['relacionable_type', 'relacionable_id']);
        });

        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
        Schema::dropIfExists('notificaciones');
    }
};
