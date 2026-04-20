<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rfc', 20)->nullable();
            $table->string('origen')->default('manual')->comment('sat o manual');
            $table->timestamps();
        });

        Schema::create('facturas_sat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->string('uuid', 36)->unique()->nullable();
            $table->string('folio')->nullable();
            $table->date('fecha');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('archivo_xml')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->enum('estatus', ['pendiente', 'asignada', 'pagada'])->default('pendiente');
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_sat_id')->nullable()->constrained('facturas_sat')->nullOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('monto', 12, 2);
            $table->text('descripcion')->nullable();
            $table->enum('estatus', ['pendiente', 'asignado', 'pagado'])->default('pendiente');
            $table->foreignId('cuenta_id')->nullable()->constrained('cuentas')->nullOnDelete();
            $table->date('fecha_pago')->nullable();
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->foreignId('prefactura_id')->nullable()->constrained('prefacturas')->nullOnDelete()
                  ->comment('Prefactura standalone cuando no va a proyecto');
            $table->foreignId('movimiento_id')->nullable()->constrained('movimientos')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('facturas_sat');
        Schema::dropIfExists('proveedores');
    }
};
