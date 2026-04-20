<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanEstado extends Model
{
    protected $fillable = ['nombre', 'color', 'orden', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function prospectos()
    {
        return $this->hasMany(Prospecto::class, 'estado_id');
    }
}
