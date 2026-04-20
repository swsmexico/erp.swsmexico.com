<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prefacturas', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique()->comment('PF-XXXX');
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('descripcion');
            $table->enum('tipo', ['prefactura', 'invoice'])->default('prefactura')
                  ->comment('prefactura=empresa MX con IVA, invoice=LLC sin IVA');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('recurrencia', ['anual', 'mensual', 'semanal', 'unica'])->default('anual');
            $table->tinyInteger('mes_vencimiento')->nullable()->comment('1=Enero … 12=Diciembre');
            $table->enum('estado', ['pendiente', 'pagada', 'cancelada'])->default('pendiente');
            $table->string('folio_factura', 20)->nullable()->comment('C-XXXX del sistema anterior');
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefacturas');
    }
};
