<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    public function notificacionesNoLeidas()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id')->whereNull('leida_at');
    }

    public function prospectos()
    {
        return $this->hasMany(Prospecto::class, 'vendedor_id');
    }

    public function reportesDiarios()
    {
        return $this->hasMany(ReporteDiario::class, 'usuario_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function esAdmin(): bool
    {
        return $this->hasRole('administrador');
    }
}
