<template>
    <AppLayout>
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">Clientes</h1>
            <Link v-if="can('clientes.crear')" :href="route('clientes.create')" class="btn-primary">
                <PlusIcon class="w-4 h-4" />
                Nuevo cliente
            </Link>
        </div>

        <!-- Filtros -->
        <div class="card p-4 mb-5 flex flex-wrap gap-3">
            <div class="flex-1 min-w-48 relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#595959;" />
                <input
                    v-model="filtros.buscar"
                    @input="buscar"
                    type="text"
                    placeholder="Buscar por nombre o RFC..."
                    class="input pl-9"
                />
            </div>
            <select v-model="filtros.grupo" @change="buscar" class="input w-40">
                <option value="">Todos los grupos</option>
                <option v-for="g in grupos" :key="g" :value="g">{{ g }}</option>
            </select>
            <button v-if="filtros.buscar || filtros.grupo" @click="limpiar" class="btn-secondary">
                <XMarkIcon class="w-4 h-4" />
                Limpiar
            </button>
        </div>

        <!-- Tabla -->
        <div class="card overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>RFC</th>
                        <th>Grupo</th>
                        <th>Cliente desde</th>
                        <th>Contacto</th>
                        <th>Pendiente</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="cliente in clientes.data"
                        :key="cliente.id"
                        class="cursor-pointer"
                        @click="ir(cliente)"
                    >
                        <td>
                            <span class="font-medium" style="color:#0D0D0D;">{{ cliente.nombre }}</span>
                        </td>
                        <td>
                            <span style="color:#595959;">{{ cliente.rfc || '—' }}</span>
                        </td>
                        <td>
                            <span v-if="cliente.grupo" class="badge" style="background:#EFF6FF; color:#1D4ED8;">
                                {{ cliente.grupo }}
                            </span>
                            <span v-else style="color:#595959;">—</span>
                        </td>
                        <td style="color:#595959;">{{ cliente.cliente_desde || '—' }}</td>
                        <td>
                            <span v-if="cliente.contacto_principal" class="text-sm" style="color:#595959;">
                                {{ cliente.contacto_principal.nombre }}
                            </span>
                            <span v-else style="color:#9CA3AF;">Sin contacto</span>
                        </td>
                        <td>
                            <span
                                v-if="cliente.total_pendiente > 0"
                                class="font-semibold"
                                style="color:#EF4444;"
                            >{{ formatMXN(cliente.total_pendiente) }}</span>
                            <span v-else style="color:#22C55E;">Al corriente</span>
                        </td>
                        <td class="text-right" @click.stop>
                            <Link
                                :href="route('clientes.show', cliente.id)"
                                class="text-sm hover:underline"
                                style="color:#5CC8F2;"
                            >Ver perfil</Link>
                        </td>
                    </tr>
                    <tr v-if="!clientes.data.length">
                        <td colspan="7" class="text-center py-12" style="color:#595959;">
                            No se encontraron clientes
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div v-if="clientes.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                <span class="text-sm" style="color:#595959;">
                    {{ clientes.from }}–{{ clientes.to }} de {{ clientes.total }} clientes
                </span>
                <div class="flex gap-1">
                    <Link
                        v-for="link in clientes.links"
                        :key="link.label"
                        :href="link.url ?? ''"
                        :class="['px-3 py-1.5 text-sm rounded-lg transition-colors', link.active ? 'text-white' : 'hover:bg-gray-100']"
                        :style="link.active ? 'background:#5CC8F2;' : 'color:#595959;'"
                        v-html="link.label"
                        :disabled="!link.url"
                        preserve-scroll
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePermissions } from '@/composables/usePermissions'
import { PlusIcon, MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    clientes: Object,
    filtros:  Object,
    grupos:   Array,
})

const { can } = usePermissions()
const filtros = ref({ buscar: props.filtros?.buscar ?? '', grupo: props.filtros?.grupo ?? '' })

let debounce = null
const buscar = () => {
    clearTimeout(debounce)
    debounce = setTimeout(() => {
        router.get(route('clientes.index'), filtros.value, { preserveState: true, replace: true })
    }, 300)
}

const limpiar = () => {
    filtros.value = { buscar: '', grupo: '' }
    buscar()
}

const ir = (cliente) => router.visit(route('clientes.show', cliente.id))

const formatMXN = (val) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val)
</script>
