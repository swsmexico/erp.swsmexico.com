<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'grupo',
        'cliente_desde',
        'notas',
        'vendedor_email',
        'rfc',
        'razon_social',
    ];

    protected $casts = [
        'cliente_desde' => 'integer',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    public function contactoPrincipal()
    {
        return $this->hasOne(Contacto::class)->where('activo', true)->oldest();
    }

    public function prefacturas()
    {
        return $this->hasMany(Prefactura::class);
    }

    public function prefacturasPendientes()
    {
        return $this->hasMany(Prefactura::class)->where('estado', 'pendiente');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    public function prospectos()
    {
        return $this->hasMany(Prospecto::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeBuscar($query, string $termino)
    {
        return $query->where('nombre', 'like', "%{$termino}%")
                     ->orWhere('rfc', 'like', "%{$termino}%");
    }

    public function scopeDelGrupo($query, string $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function totalPendiente(): float
    {
        return $this->prefacturasPendientes()->sum('total');
    }
}
