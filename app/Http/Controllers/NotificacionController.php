<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()
            ->notificaciones()
            ->latest()
            ->limit(30)
            ->get(['id', 'titulo', 'cuerpo', 'relacionable_type', 'relacionable_id', 'leida_at', 'created_at']);
    }

    public function leerTodas(Request $request)
    {
        $request->user()
            ->notificacionesNoLeidas()
            ->update(['leida_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function leer(Request $request, Notificacion $notificacion)
    {
        abort_if($notificacion->usuario_id !== $request->user()->id, 403);
        $notificacion->update(['leida_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
