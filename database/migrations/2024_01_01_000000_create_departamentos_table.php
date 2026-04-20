<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nombre_corto', 20)->comment('Admin, Instal, Desarr, CEO, Publicidad');
            $table->string('color', 7)->default('#595959')->comment('Hex color para UI');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
