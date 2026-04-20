<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id', 'titulo', 'cuerpo',
        'relacionable_type', 'relacionable_id', 'leida_at',
    ];

    protected $casts = [
        'leida_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function relacionable()
    {
        return $this->morphTo();
    }

    // Helper estático para crear notificaciones fácilmente
    public static function crear(int $userId, string $titulo, string $cuerpo = '', $relacionable = null): self
    {
        return self::create([
            'usuario_id'        => $userId,
            'titulo'            => $titulo,
            'cuerpo'            => $cuerpo,
            'relacionable_type' => $relacionable ? get_class($relacionable) : null,
            'relacionable_id'   => $relacionable?->id,
        ]);
    }
}
