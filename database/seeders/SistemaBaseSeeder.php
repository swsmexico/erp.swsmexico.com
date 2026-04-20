<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemaBaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Departamentos ─────────────────────────────────────────────────────
        $departamentos = [
            ['nombre' => 'Administración', 'nombre_corto' => 'Admin',      'color' => '#595959'],
            ['nombre' => 'Instalaciones',  'nombre_corto' => 'Instal',     'color' => '#F2A71B'],
            ['nombre' => 'Desarrollo',     'nombre_corto' => 'Desarr',     'color' => '#5CC8F2'],
            ['nombre' => 'CEO',            'nombre_corto' => 'CEO',        'color' => '#0D0D0D'],
            ['nombre' => 'Publicidad',     'nombre_corto' => 'Publicidad', 'color' => '#A855F7'],
        ];

        foreach ($departamentos as $d) {
            DB::table('departamentos')->insertOrIgnore(array_merge($d, [
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Cuentas bancarias ─────────────────────────────────────────────────
        $cuentas = [
            [
                'nombre'        => 'SWS Banamex',
                'banco'         => 'Banamex',
                'numero_cuenta' => '3833',
                'tipo'          => 'mx',
                'saldo_inicial' => 0,
                'saldo_actual'  => 0,
            ],
            [
                'nombre'        => 'LLC Banamex',
                'banco'         => 'Banamex',
                'numero_cuenta' => null,
                'tipo'          => 'llc',
                'saldo_inicial' => 0,
                'saldo_actual'  => 0,
            ],
        ];

        foreach ($cuentas as $c) {
            DB::table('cuentas')->insertOrIgnore(array_merge($c, [
                'activa'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Estados Kanban ────────────────────────────────────────────────────
        $estados = [
            ['nombre' => 'Nuevo contacto',    'color' => '#5CC8F2', 'orden' => 1],
            ['nombre' => 'Propuesta enviada', 'color' => '#F2A71B', 'orden' => 2],
            ['nombre' => 'En negociación',    'color' => '#A855F7', 'orden' => 3],
            ['nombre' => 'Cierre',            'color' => '#22C55E', 'orden' => 4],
            ['nombre' => 'Perdido',           'color' => '#EF4444', 'orden' => 5],
        ];

        foreach ($estados as $e) {
            DB::table('kanban_estados')->insertOrIgnore(array_merge($e, [
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Configuración base ────────────────────────────────────────────────
        $config = [
            ['clave' => 'whatsapp_conectado',         'valor' => '0',  'descripcion' => 'Estado de conexión WhatsApp'],
            ['clave' => 'linear_api_token',            'valor' => null, 'descripcion' => 'Token de API de Linear'],
            ['clave' => 'cobranza_dias_antes_aviso',   'valor' => '3',  'descripcion' => 'Días antes del vencimiento para primer aviso'],
            ['clave' => 'sat_rfc_empresa',             'valor' => null, 'descripcion' => 'RFC de la empresa para descarga SAT'],
            ['clave' => 'sat_ciec',                    'valor' => null, 'descripcion' => 'CIEC para acceso SAT (encriptada)'],
            ['clave' => 'smtp_host',                   'valor' => null, 'descripcion' => 'Servidor SMTP Ionos'],
            ['clave' => 'smtp_usuario',                'valor' => null, 'descripcion' => 'Usuario SMTP'],
            ['clave' => 'smtp_password',               'valor' => null, 'descripcion' => 'Contraseña SMTP (encriptada)'],
        ];

        foreach ($config as $c) {
            DB::table('configuracion')->insertOrIgnore(array_merge($c, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
