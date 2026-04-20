<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="$emit('cerrar')" />
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-base font-semibold mb-5" style="color:#0D0D0D;">
                    {{ contacto ? 'Editar contacto' : 'Nuevo contacto' }}
                </h3>

                <div class="space-y-3">
                    <div>
                        <label class="label">Nombre</label>
                        <input v-model="form.nombre" type="text" class="input" />
                    </div>
                    <div>
                        <label class="label">Puesto</label>
                        <input v-model="form.puesto" type="text" class="input" />
                    </div>
                    <div>
                        <label class="label">Correo</label>
                        <input v-model="form.correo" type="email" class="input" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Celular</label>
                            <input v-model="form.celular" type="text" class="input" />
                        </div>
                        <div>
                            <label class="label">Teléfono</label>
                            <input v-model="form.telefono" type="text" class="input" />
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-1">
                        <div class="flex items-center gap-2">
                            <input v-model="form.es_contacto_pago" type="checkbox"
                                id="es_pago" class="rounded" />
                            <label for="es_pago" class="text-sm" style="color:#595959;">Contacto de pago</label>
                        </div>
                        <div v-if="contacto" class="flex items-center gap-2">
                            <input v-model="form.activo" type="checkbox" id="activo" class="rounded" />
                            <label for="activo" class="text-sm" style="color:#595959;">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="$emit('cerrar')" class="btn-secondary">Cancelar</button>
                    <button @click="guardar" :disabled="form.processing" class="btn-primary">
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ contacto: Object, clienteId: Number })
const emit  = defineEmits(['cerrar'])

const form = useForm({
    nombre:           props.contacto?.nombre           ?? '',
    puesto:           props.contacto?.puesto           ?? '',
    correo:           props.contacto?.correo           ?? '',
    celular:          props.contacto?.celular          ?? '',
    telefono:         props.contacto?.telefono         ?? '',
    es_contacto_pago: props.contacto?.es_contacto_pago ?? false,
    activo:           props.contacto?.activo           ?? true,
})

const guardar = () => {
    if (props.contacto) {
        form.patch(
            route('clientes.contactos.update', { cliente: props.clienteId, contacto: props.contacto.id }),
            { onSuccess: () => emit('cerrar'), preserveScroll: true }
        )
    } else {
        form.post(
            route('clientes.contactos.store', { cliente: props.clienteId }),
            { onSuccess: () => emit('cerrar'), preserveScroll: true }
        )
    }
}
</script>
