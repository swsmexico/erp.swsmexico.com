<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contacto;
use App\Http\Resources\ClienteResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ClienteController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): Response
    {
        $clientes = Cliente::query()
            ->with(['contactoPrincipal', 'prefacturasPendientes'])
            ->when($request->buscar, fn($q, $v) => $q->buscar($v))
            ->when($request->grupo,  fn($q, $v) => $q->delGrupo($v))
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Clientes/Index', [
            'clientes' => ClienteResource::collection($clientes),
            'filtros'  => $request->only(['buscar', 'grupo']),
            'grupos'   => ['TM', 'HY', 'IM', 'HUGO', 'LA', 'RYA'],
        ]);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Cliente $cliente): Response
    {
        $cliente->load([
            'contactos',
            'prefacturas' => fn($q) => $q->with('conceptos', 'pagos')->latest(),
            'proyectos'   => fn($q) => $q->with('cobros')->latest(),
        ]);

        return Inertia::render('Clientes/Show', [
            'cliente' => new ClienteResource($cliente),
        ]);
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function create(): Response
    {
        return Inertia::render('Clientes/Form', [
            'grupos' => ['TM', 'HY', 'IM', 'HUGO', 'LA', 'RYA'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'grupo'         => 'nullable|string|max:50',
            'cliente_desde' => 'nullable|integer|min:1990|max:2099',
            'notas'         => 'nullable|string',
            'vendedor_email'=> 'nullable|email',
            'rfc'           => 'nullable|string|max:20',
            'razon_social'  => 'nullable|string|max:255',
            'contactos'     => 'nullable|array',
            'contactos.*.nombre'           => 'nullable|string|max:255',
            'contactos.*.correo'           => 'nullable|email',
            'contactos.*.celular'          => 'nullable|string|max:20',
            'contactos.*.telefono'         => 'nullable|string|max:20',
            'contactos.*.puesto'           => 'nullable|string|max:100',
            'contactos.*.es_contacto_pago' => 'boolean',
        ]);

        $cliente = Cliente::create($data);

        if (! empty($data['contactos'])) {
            foreach ($data['contactos'] as $c) {
                $cliente->contactos()->create(array_merge($c, ['activo' => true]));
            }
        }

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', "Cliente {$cliente->nombre} creado correctamente.");
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function edit(Cliente $cliente): Response
    {
        $cliente->load('contactos');

        return Inertia::render('Clientes/Form', [
            'cliente' => new ClienteResource($cliente),
            'grupos'  => ['TM', 'HY', 'IM', 'HUGO', 'LA', 'RYA'],
        ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'grupo'         => 'nullable|string|max:50',
            'cliente_desde' => 'nullable|integer|min:1990|max:2099',
            'notas'         => 'nullable|string',
            'vendedor_email'=> 'nullable|email',
            'rfc'           => 'nullable|string|max:20',
            'razon_social'  => 'nullable|string|max:255',
        ]);

        $cliente->update($data);

        return back()->with('success', 'Cliente actualizado correctamente.');
    }

    // ── Contactos ─────────────────────────────────────────────────────────────

    public function storeContacto(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'           => 'nullable|string|max:255',
            'correo'           => 'nullable|email',
            'celular'          => 'nullable|string|max:20',
            'telefono'         => 'nullable|string|max:20',
            'puesto'           => 'nullable|string|max:100',
            'es_contacto_pago' => 'boolean',
        ]);

        $cliente->contactos()->create(array_merge($data, ['activo' => true]));

        return back()->with('success', 'Contacto agregado.');
    }

    public function updateContacto(Request $request, Cliente $cliente, Contacto $contacto)
    {
        abort_if($contacto->cliente_id !== $cliente->id, 403);

        $data = $request->validate([
            'nombre'           => 'nullable|string|max:255',
            'correo'           => 'nullable|email',
            'celular'          => 'nullable|string|max:20',
            'telefono'         => 'nullable|string|max:20',
            'puesto'           => 'nullable|string|max:100',
            'activo'           => 'boolean',
            'es_contacto_pago' => 'boolean',
        ]);

        $contacto->update($data);

        return back()->with('success', 'Contacto actualizado.');
    }

    public function destroyContacto(Cliente $cliente, Contacto $contacto)
    {
        abort_if($contacto->cliente_id !== $cliente->id, 403);
        $contacto->delete();
        return back()->with('success', 'Contacto eliminado.');
    }
}
