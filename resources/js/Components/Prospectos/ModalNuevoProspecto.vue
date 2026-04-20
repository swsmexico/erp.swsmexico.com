<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="$emit('cerrar')" />
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-base font-semibold mb-5" style="color:#0D0D0D;">Nuevo prospecto</h3>

                <div class="space-y-4">
                    <div>
                        <label class="label">Nombre comercial *</label>
                        <input v-model="form.nombre_comercial" type="text" class="input"
                            :class="{'border-red-400': form.errors.nombre_comercial}" />
                        <p v-if="form.errors.nombre_comercial" class="text-xs mt-1 text-red-500">
                            {{ form.errors.nombre_comercial }}
                        </p>
                    </div>

                    <div>
                        <label class="label">Valor estimado ($)</label>
                        <input v-model="form.valor" type="number" class="input" placeholder="0.00" min="0" />
                    </div>

                    <div>
                        <label class="label">Estado inicial *</label>
                        <select v-model="form.estado_id" class="input">
                            <option v-for="e in estados" :key="e.id" :value="e.id">
                                {{ e.nombre }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Próximo seguimiento</label>
                        <input v-model="form.proximo_seguimiento" type="date" class="input" />
                    </div>

                    <!-- Contacto inicial -->
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-medium mb-3" style="color:#595959;">Contacto (opcional)</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Nombre</label>
                                <input v-model="form.contactos[0].nombre" type="text" class="input" />
                            </div>
                            <div>
                                <label class="label">Correo</label>
                                <input v-model="form.contactos[0].correo" type="email" class="input" />
                            </div>
                            <div class="col-span-2">
                                <label class="label">Teléfono</label>
                                <input v-model="form.contactos[0].telefono" type="text" class="input" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="$emit('cerrar')" class="btn-secondary">Cancelar</button>
                    <button @click="guardar" :disabled="form.processing" class="btn-primary">
                        {{ form.processing ? 'Creando...' : 'Crear prospecto' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ estados: Array })
const emit  = defineEmits(['cerrar'])

const form = useForm({
    nombre_comercial:    '',
    valor:               '',
    estado_id:           props.estados[0]?.id ?? null,
    proximo_seguimiento: '',
    contactos:           [{ nombre: '', correo: '', telefono: '' }],
})

const guardar = () => {
    form.post(route('prospectos.store'), {
        onSuccess: () => emit('cerrar'),
    })
}
</script>
