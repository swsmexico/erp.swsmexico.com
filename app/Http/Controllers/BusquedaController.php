<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Prospecto;
use App\Models\Prefactura;
use App\Models\Proyecto;

class BusquedaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();

        if ($q->length() < 2) {
            return response()->json([]);
        }

        $resultados = collect();

        // Clientes
        if ($request->user()->can('clientes.ver')) {
            Cliente::where('nombre', 'like', "%{$q}%")
                ->limit(5)
                ->get()
                ->each(fn($c) => $resultados->push([
                    'tipo'        => 'Cliente',
                    'nombre'      => $c->nombre,
                    'url'         => route('clientes.show', $c),
                    'badgeStyle'  => 'background:#EFF6FF; color:#1D4ED8;',
                ]));
        }

        // Prospectos
        if ($request->user()->can('prospectos.ver')) {
            Prospecto::where('nombre_comercial', 'like', "%{$q}%")
                ->limit(5)
                ->get()
                ->each(fn($p) => $resultados->push([
                    'tipo'        => 'Prospecto',
                    'nombre'      => $p->nombre_comercial,
                    'url'         => route('prospectos.show', $p),
                    'badgeStyle'  => 'background:#F0FDF4; color:#15803D;',
                ]));
        }

        // Prefacturas
        if ($request->user()->can('cobranza.ver')) {
            Prefactura::where('folio', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%")
                ->with('cliente')
                ->limit(5)
                ->get()
                ->each(fn($p) => $resultados->push([
                    'tipo'        => 'Prefactura',
                    'nombre'      => "{$p->folio} — {$p->cliente->nombre}",
                    'url'         => route('cobranza.show', $p),
                    'badgeStyle'  => 'background:#FFF7ED; color:#C2410C;',
                ]));
        }

        // Proyectos
        if ($request->user()->can('proyectos.ver')) {
            Proyecto::where('nombre', 'like', "%{$q}%")
                ->with('cliente')
                ->limit(5)
                ->get()
                ->each(fn($p) => $resultados->push([
                    'tipo'        => 'Proyecto',
                    'nombre'      => $p->nombre . ($p->cliente ? " — {$p->cliente->nombre}" : ''),
                    'url'         => route('proyectos.show', $p),
                    'badgeStyle'  => 'background:#FAF5FF; color:#7E22CE;',
                ]));
        }

        return response()->json($resultados->take(12)->values());
    }
}
