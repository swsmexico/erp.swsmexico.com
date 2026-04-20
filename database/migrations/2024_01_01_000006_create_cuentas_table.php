<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('banco')->nullable();
            $table->string('numero_cuenta', 20)->nullable();
            $table->string('clabe', 18)->nullable();
            $table->decimal('saldo_inicial', 14, 2)->default(0);
            $table->decimal('saldo_actual', 14, 2)->default(0);
            $table->enum('tipo', ['mx', 'llc'])->default('mx')
                  ->comment('mx=empresa mexicana, llc=LLC');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
