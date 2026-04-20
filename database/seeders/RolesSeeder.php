<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            // Prospectos
            'prospectos.ver', 'prospectos.crear', 'prospectos.editar', 'prospectos.eliminar',
            // Clientes
            'clientes.ver', 'clientes.crear', 'clientes.editar',
            // Cobranza
            'cobranza.ver', 'cobranza.pagar', 'cobranza.configurar',
            // Proyectos
            'proyectos.ver', 'proyectos.crear', 'proyectos.editar',
            // Estados de cuenta
            'estados_cuenta.ver', 'estados_cuenta.crear',
            // Pagos
            'pagos.ver', 'pagos.asignar', 'pagos.pagar',
            // Nóminas
            'nominas.ver', 'nominas.pagar',
            // Reporte diario
            'reporte_diario.ver', 'reporte_diario.crear',
            // Dashboard
            'dashboard.ver',
            // Configuración
            'configuracion.ver', 'configuracion.editar',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // ── Administrador — acceso total ──────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'administrador']);
        $admin->givePermissionTo(Permission::all());

        // ── Atención a Cliente ────────────────────────────────────────────────
        $atencion = Role::firstOrCreate(['name' => 'atencion_cliente']);
        $atencion->givePermissionTo([
            'prospectos.ver', 'prospectos.crear', 'prospectos.editar',
            'clientes.ver', 'clientes.crear', 'clientes.editar',
            'cobranza.ver', 'cobranza.pagar',
            'proyectos.ver', 'proyectos.crear', 'proyectos.editar',
            'dashboard.ver',
        ]);

        // ── Finanzas ──────────────────────────────────────────────────────────
        $finanzas = Role::firstOrCreate(['name' => 'finanzas']);
        $finanzas->givePermissionTo([
            'cobranza.ver', 'cobranza.pagar',
            'pagos.ver', 'pagos.asignar', 'pagos.pagar',
            'estados_cuenta.ver', 'estados_cuenta.crear',
            'nominas.ver', 'nominas.pagar',
            'dashboard.ver',
        ]);

        // ── Operativo / Empleado ──────────────────────────────────────────────
        $operativo = Role::firstOrCreate(['name' => 'operativo']);
        $operativo->givePermissionTo([
            'reporte_diario.ver', 'reporte_diario.crear',
        ]);
    }
}
