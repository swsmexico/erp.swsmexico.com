<?php

namespace App\Http\Controllers;

use App\Models\Prospecto;
use App\Models\KanbanEstado;
use App\Models\ProspectoSeguimiento;
use App\Http\Resources\ProspectoResource;
use App\Services\AnthropicService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class ProspectoController extends Controller
{
    // ── Kanban ────────────────────────────────────────────────────────────────

    public function index(): Response
    {
        $estados = KanbanEstado::where('activo', true)
            ->orderBy('orden')
            ->with(['prospectos' => fn($q) => $q
                ->with(['contactoPrincipal', 'ultimoSeguimiento'])
                ->orderBy('valor', 'asc')
            ])
            ->get();

        return Inertia::render('Prospectos/Index', [
            'estados' => $estados->map(fn($e) => [
                'id'          => $e->id,
                'nombre'      => $e->nombre,
                'color'       => $e->color,
                'prospectos'  => ProspectoResource::collection($e->prospectos),
            ]),
        ]);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Prospecto $prospecto): Response
    {
        $prospecto->load([
            'estado', 'vendedor', 'contactos',
            'documentos', 'cotizaciones', 'seguimientos.usuario',
        ]);

        $estados = KanbanEstado::where('activo', true)->orderBy('orden')->get();

        return Inertia::render('Prospectos/Show', [
            'prospecto' => new ProspectoResource($prospecto),
            'estados'   => $estados,
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_comercial'    => 'required|string|max:255',
            'valor'               => 'nullable|numeric|min:0',
            'estado_id'           => 'required|exists:kanban_estados,id',
            'proximo_seguimiento' => 'nullable|date',
            'contactos'           => 'nullable|array',
            'contactos.*.nombre'  => 'nullable|string',
            'contactos.*.correo'  => 'nullable|email',
            'contactos.*.telefono'=> 'nullable|string',
        ]);

        $prospecto = Prospecto::create(array_merge($data, [
            'vendedor_id' => $request->user()->id,
        ]));

        if (! empty($data['contactos'])) {
            foreach ($data['contactos'] as $c) {
                $prospecto->contactos()->create(array_merge($c, ['activo' => true]));
            }
        }

        return redirect()
            ->route('prospectos.show', $prospecto)
            ->with('success', 'Prospecto creado correctamente.');
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, Prospecto $prospecto)
    {
        $data = $request->validate([
            'nombre_comercial'    => 'required|string|max:255',
            'valor'               => 'nullable|numeric|min:0',
            'estado_id'           => 'required|exists:kanban_estados,id',
            'proximo_seguimiento' => 'nullable|date',
        ]);

        $prospecto->update($data);

        return back()->with('success', 'Prospecto actualizado.');
    }

    // ── Mover columna (drag & drop) ───────────────────────────────────────────

    public function mover(Request $request, Prospecto $prospecto)
    {
        $request->validate([
            'estado_id' => 'required|exists:kanban_estados,id',
        ]);

        $prospecto->update(['estado_id' => $request->estado_id]);

        return response()->json(['ok' => true]);
    }

    // ── Subir documento ───────────────────────────────────────────────────────

    public function subirDocumento(Request $request, Prospecto $prospecto)
    {
        $request->validate([
            'titulo'   => 'required|string|max:255',
            'archivo'  => 'required|file|max:20480',
        ]);

        $path = $request->file('archivo')->store("prospectos/{$prospecto->id}/docs", 'local');

        $prospecto->documentos()->create([
            'titulo'  => $request->titulo,
            'archivo' => $path,
        ]);

        return back()->with('success', 'Documento agregado.');
    }

    // ── Cotizacion ────────────────────────────────────────────────────────────

    public function storeCotizacion(Request $request, Prospecto $prospecto)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:20480',
            'subtotal'    => 'required|numeric|min:0',
            'incluye_iva' => 'boolean',
        ]);

        $path = null;
        if ($request->hasFile('archivo_pdf')) {
            $path = $request->file('archivo_pdf')->store("prospectos/{$prospecto->id}/cotizaciones", 'local');
        }

        $subtotal = $request->subtotal;
        $iva      = $request->boolean('incluye_iva') ? round($subtotal * 0.16, 2) : 0;
        $total    = $subtotal + $iva;

        $prospecto->cotizaciones()->create([
            'nombre'      => $request->nombre,
            'archivo_pdf' => $path,
            'subtotal'    => $subtotal,
            'iva'         => $iva,
            'total'       => $total,
            'incluye_iva' => $request->boolean('incluye_iva'),
        ]);

        return back()->with('success', 'Cotización agregada.');
    }

    // ── Seguimiento: pulir con IA y enviar ────────────────────────────────────

    public function pulirMensaje(Request $request, Prospecto $prospecto)
    {
        $request->validate([
            'mensaje' => 'required|string|max:2000',
            'canal'   => 'required|in:correo,whatsapp',
        ]);

        $anthropic = app(AnthropicService::class);
        $formal    = $anthropic->pulirMensaje($request->mensaje, $request->canal, $prospecto->nombre_comercial);

        return response()->json(['mensaje_formal' => $formal]);
    }

    public function enviarSeguimiento(Request $request, Prospecto $prospecto)
    {
        $request->validate([
            'mensaje_original' => 'required|string',
            'mensaje_formal'   => 'required|string',
            'canal'            => 'required|in:correo,whatsapp',
        ]);

        $seguimiento = $prospecto->seguimientos()->create([
            'usuario_id'       => $request->user()->id,
            'mensaje_original' => $request->mensaje_original,
            'mensaje_formal'   => $request->mensaje_formal,
            'canal'            => $request->canal,
            'enviado_at'       => now(),
        ]);

        // TODO: encolar envío real de correo/WhatsApp
        // EnviarSeguimientoJob::dispatch($seguimiento);

        $prospecto->update(['ultimo_seguimiento' => today()]);

        return back()->with('success', 'Seguimiento enviado correctamente.');
    }

    // ── Convertir a cliente ───────────────────────────────────────────────────

    public function convertir(Prospecto $prospecto): Response
    {
        $prospecto->load(['contactos', 'cotizaciones']);

        return Inertia::render('Prospectos/Convertir', [
            'prospecto' => new ProspectoResource($prospecto),
        ]);
    }
}
