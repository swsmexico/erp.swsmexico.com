<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'nombre'           => $this->nombre,
            'correo'           => $this->correo,
            'celular'          => $this->celular,
            'telefono'         => $this->telefono,
            'puesto'           => $this->puesto,
            'activo'           => $this->activo,
            'es_contacto_pago' => $this->es_contacto_pago,
        ];
    }
}
