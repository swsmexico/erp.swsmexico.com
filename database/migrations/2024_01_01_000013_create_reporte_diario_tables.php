<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte_diario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->boolean('a_destiempo')->default(false)->comment('true si se llenó después del día');
            $table->timestamps();

            $table->unique(['usuario_id', 'fecha']);
        });

        Schema::create('reporte_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_id')->constrained('reporte_diario')->cascadeOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->text('descripcion');
            $table->string('linear_id')->nullable()->comment('ID del ticket en Linear');
            $table->string('linear_titulo')->nullable();
            $table->string('linear_estatus')->nullable();
            $table->integer('horas')->default(1)->comment('Siempre 1 hora por actividad');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte_actividades');
        Schema::dropIfExists('reporte_diario');
    }
};
