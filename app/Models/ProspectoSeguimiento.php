<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectoSeguimiento extends Model
{
    protected $table    = 'prospecto_seguimientos';
    protected $fillable = [
        'prospecto_id', 'usuario_id', 'mensaje_original',
        'mensaje_formal', 'canal', 'enviado_at', 'respuesta', 'respuesta_at',
    ];

    protected $casts = [
        'enviado_at'   => 'datetime',
        'respuesta_at' => 'datetime',
    ];

    public function prospecto() { return $this->belongsTo(Prospecto::class); }
    public function usuario()   { return $this->belongsTo(User::class, 'usuario_id'); }
}
