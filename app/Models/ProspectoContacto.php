<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectoContacto extends Model
{
    protected $table    = 'prospecto_contactos';
    protected $fillable = ['prospecto_id', 'nombre', 'correo', 'telefono', 'activo'];
    protected $casts    = ['activo' => 'boolean'];

    public function prospecto() { return $this->belongsTo(Prospecto::class); }
}
