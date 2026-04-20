<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable = [
        'cliente_id',
        'nombre',
        'correo',
        'celular',
        'telefono',
        'puesto',
        'activo',
        'es_contacto_pago',
    ];

    protected $casts = [
        'activo'            => 'boolean',
        'es_contacto_pago'  => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
