<template>
    <AppLayout>
        <div class="page-header">
            <div class="flex items-center gap-3">
                <Link :href="esEdicion ? route('clientes.show', cliente.id) : route('clientes.index')"
                    class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                    <ArrowLeftIcon class="w-5 h-5" style="color:#595959;" />
                </Link>
                <h1 class="page-title">{{ esEdicion ? 'Editar cliente' : 'Nuevo cliente' }}</h1>
            </div>
        </div>

        <div class="max-w-2xl space-y-5">

            <!-- Datos generales -->
            <div class="card p-6">
                <h2 class="text-sm font-semibold mb-5" style="color:#0D0D0D;">Datos generales</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2">
                        <label class="label">Nombre comercial *</label>
                        <input v-model="form.nombre" type="text" class="input"
                            :class="{'border-red-400': errors.nombre}" />
                        <p v-if="errors.nombre" class="text-xs mt-1 text-red-500">{{ errors.nombre }}</p>
                    </div>

                    <div>
                        <label class="label">RFC</label>
                        <input v-model="form.rfc" type="text" class="input" placeholder="Opcional" />
                    </div>

                    <div>
                        <label class="label">Grupo</label>
                        <select v-model="form.grupo" class="input">
                            <option value="">Sin grupo</option>
                            <option v-for="g in grupos" :key="g" :value="g">{{ g }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Cliente desde</label>
                        <input v-model="form.cliente_desde" type="number"
                            class="input" placeholder="Ej: 2018" min="1990" :max="anioActual" />
                    </div>

                    <div>
                        <label class="label">Vendedor</label>
                        <input v-model="form.vendedor_email" type="email"
                            class="input" placeholder="correo@swsmexico.com" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="label">Notas internas</label>
                        <textarea v-model="form.notas" rows="3" class="input resize-none"
                            placeholder="Ej: Servidor: SW3 | Carpeta: dominio.com" />
                    </div>
                </div>
            </div>

            <!-- Contactos (solo en creación) -->
            <div v-if="!esEdicion" class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold" style="color:#0D0D0D;">Contactos</h2>
                    <button @click="agregarContacto" type="button"
                        class="text-xs font-medium hover:underline" style="color:#5CC8F2;">
                        + Agregar contacto
                    </button>
                </div>

                <div v-if="form.contactos.length" class="space-y-4">
                    <div v-for="(c, i) in form.contactos" :key="i"
                        class="p-4 rounded-lg border border-gray-200 relative">
                        <button @click="form.contactos.splice(i, 1)" type="button"
                            class="absolute top-3 right-3 text-gray-400 hover:text-red-500">
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Nombre</label>
                                <input v-model="c.nombre" type="text" class="input" />
                            </div>
                            <div>
                                <label class="label">Puesto</label>
                                <input v-model="c.puesto" type="text" class="input" />
                            </div>
                            <div>
                                <label class="label">Correo</label>
                                <input v-model="c.correo" type="email" class="input" />
                            </div>
                            <div>
                                <label class="label">Celular</label>
                                <input v-model="c.celular" type="text" class="input" />
                            </div>
                            <div class="col-span-2 flex items-center gap-2">
                                <input v-model="c.es_contacto_pago" type="checkbox"
                                    class="rounded" :id="`pago-${i}`" />
                                <label :for="`pago-${i}`" class="text-sm" style="color:#595959;">
                                    Contacto de pago
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm" style="color:#9CA3AF;">
                    Sin contactos — se pueden agregar después
                </p>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end gap-3 pb-6">
                <Link
                    :href="esEdicion ? route('clientes.show', cliente?.id) : route('clientes.index')"
                    class="btn-secondary"
                >Cancelar</Link>
                <button @click="guardar" :disabled="guardando" class="btn-primary">
                    <span v-if="guardando">Guardando...</span>
                    <span v-else>{{ esEdicion ? 'Guardar cambios' : 'Crear cliente' }}</span>
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ArrowLeftIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    cliente: Object,
    grupos:  Array,
})

const esEdicion = computed(() => !!props.cliente?.id)
const anioActual = new Date().getFullYear()

const form = useForm({
    nombre:         props.cliente?.nombre         ?? '',
    grupo:          props.cliente?.grupo          ?? '',
    cliente_desde:  props.cliente?.cliente_desde  ?? '',
    notas:          props.cliente?.notas          ?? '',
    vendedor_email: props.cliente?.vendedor_email ?? '',
    rfc:            props.cliente?.rfc            ?? '',
    razon_social:   props.cliente?.razon_social   ?? '',
    contactos:      props.cliente?.contactos      ?? [],
})

const guardando = computed(() => form.processing)
const errors    = computed(() => form.errors)

const agregarContacto = () => {
    form.contactos.push({
        nombre: '', correo: '', celular: '',
        telefono: '', puesto: '', es_contacto_pago: false,
    })
}

const guardar = () => {
    if (esEdicion.value) {
        form.patch(route('clientes.update', props.cliente.id))
    } else {
        form.post(route('clientes.store'))
    }
}
</script>
