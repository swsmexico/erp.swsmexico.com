<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProspectoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'nombre_comercial'    => $this->nombre_comercial,
            'valor'               => $this->valor,
            'estado_id'           => $this->estado_id,
            'estado'              => $this->whenLoaded('estado', fn() => [
                'id'     => $this->estado->id,
                'nombre' => $this->estado->nombre,
                'color'  => $this->estado->color,
            ]),
            'vendedor'            => $this->whenLoaded('vendedor', fn() => [
                'id'    => $this->vendedor->id,
                'name'  => $this->vendedor->name,
                'email' => $this->vendedor->email,
            ]),
            'contacto_principal'  => $this->whenLoaded('contactoPrincipal', fn() => [
                'nombre'  => $this->contactoPrincipal?->nombre,
                'correo'  => $this->contactoPrincipal?->correo,
                'telefono'=> $this->contactoPrincipal?->telefono,
            ]),
            'contactos'           => $this->whenLoaded('contactos',    fn() => $this->contactos),
            'documentos'          => $this->whenLoaded('documentos',   fn() => $this->documentos),
            'cotizaciones'        => $this->whenLoaded('cotizaciones', fn() => $this->cotizaciones),
            'seguimientos'        => $this->whenLoaded('seguimientos', fn() => $this->seguimientos->map(fn($s) => [
                'id'               => $s->id,
                'canal'            => $s->canal,
                'mensaje_original' => $s->mensaje_original,
                'mensaje_formal'   => $s->mensaje_formal,
                'enviado_at'       => $s->enviado_at?->format('Y-m-d H:i'),
                'respuesta'        => $s->respuesta,
                'respuesta_at'     => $s->respuesta_at?->format('Y-m-d H:i'),
                'usuario'          => $s->usuario?->name,
            ])),
            'proximo_seguimiento' => $this->proximo_seguimiento?->format('Y-m-d'),
            'ultimo_seguimiento'  => $this->ultimo_seguimiento?->format('Y-m-d'),
            'created_at'          => $this->created_at?->format('Y-m-d'),
        ];
    }
}
