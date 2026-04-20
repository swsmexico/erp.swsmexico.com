<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectoDocumento extends Model
{
    protected $table    = 'prospecto_documentos';
    protected $fillable = ['prospecto_id', 'titulo', 'archivo'];

    public function prospecto() { return $this->belongsTo(Prospecto::class); }
}
