<template>
    <AppLayout>
        <!-- Header -->
        <div class="page-header">
            <div class="flex items-center gap-3">
                <Link :href="route('clientes.index')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                    <ArrowLeftIcon class="w-5 h-5" style="color:#595959;" />
                </Link>
                <div>
                    <h1 class="page-title">{{ cliente.nombre }}</h1>
                    <p v-if="cliente.cliente_desde" class="text-sm mt-0.5" style="color:#595959;">
                        Cliente desde {{ cliente.cliente_desde }}
                    </p>
                </div>
                <span v-if="cliente.grupo" class="badge ml-2" style="background:#EFF6FF; color:#1D4ED8;">
                    {{ cliente.grupo }}
                </span>
            </div>
            <Link
                v-if="can('clientes.editar')"
                :href="route('clientes.edit', cliente.id)"
                class="btn-secondary"
            >
                <PencilIcon class="w-4 h-4" />
                Editar
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Columna izquierda: datos + contactos -->
            <div class="space-y-5">

                <!-- Datos generales -->
                <div class="card p-5">
                    <h2 class="text-sm font-semibold mb-4" style="color:#0D0D0D;">Datos generales</h2>
                    <dl class="space-y-3">
                        <div v-if="cliente.rfc">
                            <dt class="label">RFC</dt>
                            <dd class="text-sm font-mono">{{ cliente.rfc }}</dd>
                        </div>
                        <div v-if="cliente.razon_social">
                            <dt class="label">Razón social</dt>
                            <dd class="text-sm">{{ cliente.razon_social }}</dd>
                        </div>
                        <div v-if="cliente.vendedor_email">
                            <dt class="label">Vendedor</dt>
                            <dd class="text-sm">{{ cliente.vendedor_email }}</dd>
                        </div>
                        <div v-if="cliente.notas">
                            <dt class="label">Notas internas</dt>
                            <dd class="text-sm whitespace-pre-wrap" style="color:#595959;">{{ cliente.notas }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Contactos -->
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold" style="color:#0D0D0D;">Contactos</h2>
                        <button
                            v-if="can('clientes.editar')"
                            @click="modalContacto = true"
                            class="text-xs font-medium hover:underline"
                            style="color:#5CC8F2;"
                        >+ Agregar</button>
                    </div>

                    <div v-if="cliente.contactos?.length" class="space-y-3">
                        <ContactoCard
                            v-for="c in cliente.contactos"
                            :key="c.id"
                            :contacto="c"
                            :puede-editar="can('clientes.editar')"
                            @editar="editarContacto(c)"
                            @eliminar="eliminarContacto(c)"
                        />
                    </div>
                    <p v-else class="text-sm" style="color:#9CA3AF;">Sin contactos registrados</p>
                </div>
            </div>

            <!-- Columna derecha: tabs con prefacturas, proyectos -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Tabs -->
                <div class="card overflow-hidden">
                    <div class="flex border-b border-gray-100">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="tabActivo = tab.key"
                            :class="[
                                'px-5 py-3 text-sm font-medium transition-colors border-b-2 -mb-px',
                                tabActivo === tab.key
                                    ? 'border-[#5CC8F2]'
                                    : 'border-transparent hover:bg-gray-50',
                            ]"
                            :style="tabActivo === tab.key ? 'color:#5CC8F2;' : 'color:#595959;'"
                        >
                            {{ tab.label }}
                            <span
                                v-if="tab.count !== undefined"
                                class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full"
                                :style="tabActivo === tab.key ? 'background:#EFF9FF; color:#5CC8F2;' : 'background:#F3F4F6; color:#6B7280;'"
                            >{{ tab.count }}</span>
                        </button>
                    </div>

                    <!-- Tab: Prefacturas -->
                    <div v-if="tabActivo === 'prefacturas'" class="p-5">
                        <div v-if="cliente.prefacturas?.length" class="space-y-2">
                            <PrefacturaRow
                                v-for="p in cliente.prefacturas"
                                :key="p.id"
                                :prefactura="p"
                            />
                        </div>
                        <p v-else class="text-sm text-center py-8" style="color:#9CA3AF;">
                            Sin prefacturas registradas
                        </p>
                    </div>

                    <!-- Tab: Proyectos -->
                    <div v-if="tabActivo === 'proyectos'" class="p-5">
                        <div v-if="cliente.proyectos?.length" class="space-y-2">
                            <ProyectoRow
                                v-for="p in cliente.proyectos"
                                :key="p.id"
                                :proyecto="p"
                            />
                        </div>
                        <p v-else class="text-sm text-center py-8" style="color:#9CA3AF;">
                            Sin proyectos registrados
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal nuevo/editar contacto -->
        <ModalContacto
            v-if="modalContacto"
            :contacto="contactoEditar"
            :cliente-id="cliente.id"
            @cerrar="cerrarModalContacto"
        />
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ContactoCard from '@/Components/Clientes/ContactoCard.vue'
import PrefacturaRow from '@/Components/Clientes/PrefacturaRow.vue'
import ProyectoRow from '@/Components/Clientes/ProyectoRow.vue'
import ModalContacto from '@/Components/Clientes/ModalContacto.vue'
import { usePermissions } from '@/composables/usePermissions'
import { ArrowLeftIcon, PencilIcon } from '@heroicons/vue/24/outline'

const props = defineProps({ cliente: Object })
const { can } = usePermissions()

const tabActivo = ref('prefacturas')
const modalContacto = ref(false)
const contactoEditar = ref(null)

const tabs = computed(() => [
    { key: 'prefacturas', label: 'Prefacturas', count: props.cliente.prefacturas?.length ?? 0 },
    { key: 'proyectos',   label: 'Proyectos',   count: props.cliente.proyectos?.length ?? 0 },
])

const editarContacto = (c) => { contactoEditar.value = c; modalContacto.value = true }
const cerrarModalContacto = () => { modalContacto.value = false; contactoEditar.value = null }

const eliminarContacto = (c) => {
    if (!confirm(`¿Eliminar contacto ${c.nombre}?`)) return
    router.delete(route('clientes.contactos.destroy', { cliente: props.cliente.id, contacto: c.id }), {
        preserveScroll: true,
    })
}
</script>
