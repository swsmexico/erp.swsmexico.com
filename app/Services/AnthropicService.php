<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    private string $apiKey;
    private string $model = 'claude-sonnet-4-20250514';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
    }

    // ── Pulir mensaje informal a formal ──────────────────────────────────────

    public function pulirMensaje(string $mensajeInformal, string $canal, string $nombreCliente): string
    {
        $canalLabel = $canal === 'correo' ? 'correo electrónico' : 'WhatsApp';

        $prompt = <<<EOT
Eres el asistente de comunicaciones de SWS Mexico, una empresa de tecnología y desarrollo web.

El usuario quiere enviar un mensaje de seguimiento a un prospecto llamado "{$nombreCliente}" por {$canalLabel}.

Mensaje informal del usuario:
"{$mensajeInformal}"

Reescribe este mensaje de forma profesional, cordial y concisa para {$canalLabel}.
- Mantén el tono amigable pero profesional
- Conserva toda la información importante
- Adapta el formato al canal ({$canalLabel})
- No agregues información que no esté en el mensaje original
- Responde ÚNICAMENTE con el mensaje reescrito, sin explicaciones adicionales
EOT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->model,
                'max_tokens' => 500,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                return $response->json('content.0.text', $mensajeInformal);
            }

            Log::error('Anthropic API error', ['response' => $response->body()]);
            return $mensajeInformal;

        } catch (\Exception $e) {
            Log::error('Anthropic service exception', ['error' => $e->getMessage()]);
            return $mensajeInformal;
        }
    }

    // ── Borrador de mensaje de cobranza ───────────────────────────────────────

    public function mensajeCobranza(
        string $nombreCliente,
        string $folio,
        float  $total,
        string $situacion, // 'antes', 'vencimiento', 'post'
        string $canal
    ): string {
        $situacionLabel = match($situacion) {
            'antes'       => 'vence en 3 días',
            'vencimiento' => 'vence hoy',
            'post'        => 'está vencida',
            default       => 'está pendiente',
        };

        $totalFmt = '$' . number_format($total, 2);
        $canalLabel = $canal === 'correo' ? 'correo electrónico' : 'WhatsApp';

        $prompt = <<<EOT
Redacta un mensaje de cobranza profesional y cordial para {$canalLabel}.

Cliente: {$nombreCliente}
Folio: {$folio}
Monto: {$totalFmt}
Situación: La factura {$situacionLabel}

El mensaje debe ser:
- Profesional pero amigable
- Directo al punto
- Incluir el folio y monto
- Invitar al cliente a ponerse en contacto si tiene alguna duda
- Adaptado para {$canalLabel}

Responde ÚNICAMENTE con el mensaje, sin explicaciones adicionales.
EOT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->model,
                'max_tokens' => 400,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return $response->successful()
                ? $response->json('content.0.text', '')
                : '';

        } catch (\Exception $e) {
            Log::error('Anthropic cobranza error', ['error' => $e->getMessage()]);
            return '';
        }
    }
}
