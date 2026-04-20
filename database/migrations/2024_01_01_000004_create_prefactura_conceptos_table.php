<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prefactura_conceptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefactura_id')->constrained('prefacturas')->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('monto', 12, 2);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefactura_conceptos');
    }
};
