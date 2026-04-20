<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="$emit('cerrar')" />
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-base font-semibold mb-5" style="color:#0D0D0D;">Nueva cotización</h3>

                <div class="space-y-4">
                    <div>
                        <label class="label">Nombre de la cotización *</label>
                        <input v-model="form.nombre" type="text" class="input"
                            placeholder="Ej: Propuesta Sitio Web + Correos" />
                    </div>

                    <div>
                        <label class="label">Subtotal *</label>
                        <input v-model="form.subtotal" type="number" class="input"
                            placeholder="0.00" min="0" step="0.01" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input v-model="form.incluye_iva" type="checkbox" id="iva" class="rounded" />
                        <label for="iva" class="text-sm" style="color:#595959;">
                            Incluir IVA (16%) — Total: {{ totalConIva }}
                        </label>
                    </div>

                    <div>
                        <label class="label">PDF de la cotización (opcional)</label>
                        <input
                            type="file"
                            accept=".pdf"
                            @change="form.archivo_pdf = $event.target.files[0]"
                            class="input py-1.5 text-sm"
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="$emit('cerrar')" class="btn-secondary">Cancelar</button>
                    <button @click="guardar" :disabled="form.processing" class="btn-primary">
                        {{ form.processing ? 'Guardando...' : 'Guardar cotización' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ prospectoId: Number })
const emit  = defineEmits(['cerrar'])

const form = useForm({
    nombre:      '',
    subtotal:    '',
    incluye_iva: true,
    archivo_pdf: null,
})

const totalConIva = computed(() => {
    const sub = parseFloat(form.subtotal) || 0
    const total = form.incluye_iva ? sub * 1.16 : sub
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(total)
})

const guardar = () => {
    form.post(route('prospectos.cotizaciones.store', props.prospectoId), {
        onSuccess: () => emit('cerrar'),
        preserveScroll: true,
        forceFormData: true,
    })
}
</script>
