<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('grupo')->nullable()->comment('TM, HY, IM, HUGO, LA, RYA');
            $table->year('cliente_desde')->nullable();
            $table->text('notas')->nullable()->comment('Servidor y carpeta de hosting');
            $table->string('vendedor_email')->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('razon_social')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
