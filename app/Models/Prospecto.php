<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospecto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre_comercial',
        'valor',
        'estado_id',
        'vendedor_id',
        'proximo_seguimiento',
        'ultimo_seguimiento',
    ];

    protected $casts = [
        'valor'               => 'decimal:2',
        'proximo_seguimiento' => 'date',
        'ultimo_seguimiento'  => 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function estado()
    {
        return $this->belongsTo(KanbanEstado::class, 'estado_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function contactos()
    {
        return $this->hasMany(ProspectoContacto::class);
    }

    public function contactoPrincipal()
    {
        return $this->hasOne(ProspectoContacto::class)->where('activo', true)->oldest();
    }

    public function documentos()
    {
        return $this->hasMany(ProspectoDocumento::class);
    }

    public function cotizaciones()
    {
        return $this->hasMany(ProspectoCotizacion::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(ProspectoSeguimiento::class)->latest();
    }

    public function ultimoSeguimiento()
    {
        return $this->hasOne(ProspectoSeguimiento::class)->latest();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeBuscar($query, string $termino)
    {
        return $query->where('nombre_comercial', 'like', "%{$termino}%");
    }
}
