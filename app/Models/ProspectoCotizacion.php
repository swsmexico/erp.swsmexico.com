<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectoCotizacion extends Model
{
    protected $table    = 'prospecto_cotizaciones';
    protected $fillable = [
        'prospecto_id', 'nombre', 'archivo_pdf',
        'subtotal', 'iva', 'total', 'incluye_iva',
    ];

    protected $casts = [
        'subtotal'    => 'decimal:2',
        'iva'         => 'decimal:2',
        'total'       => 'decimal:2',
        'incluye_iva' => 'boolean',
    ];

    public function prospecto() { return $this->belongsTo(Prospecto::class); }
}
