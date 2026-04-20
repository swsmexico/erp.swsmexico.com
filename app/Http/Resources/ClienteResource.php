<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'nombre'        => $this->nombre,
            'grupo'         => $this->grupo,
            'cliente_desde' => $this->cliente_desde,
            'notas'         => $this->notas,
            'vendedor_email'=> $this->vendedor_email,
            'rfc'           => $this->rfc,
            'razon_social'  => $this->razon_social,
            'contactos'     => ContactoResource::collection($this->whenLoaded('contactos')),
            'prefacturas'   => PrefacturaResource::collection($this->whenLoaded('prefacturas')),
            'proyectos'     => ProyectoResource::collection($this->whenLoaded('proyectos')),
            'total_pendiente' => $this->whenLoaded('prefacturas', fn() => $this->totalPendiente()),
            'created_at'    => $this->created_at?->format('Y-m-d'),
        ];
    }
}
