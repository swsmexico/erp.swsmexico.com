<template>
    <div class="flex items-start justify-between gap-3 p-3 rounded-lg border border-gray-100 hover:border-gray-200 transition-colors">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium" style="color:#0D0D0D;">
                    {{ contacto.nombre || 'Sin nombre' }}
                </span>
                <span v-if="contacto.puesto" class="text-xs" style="color:#595959;">
                    · {{ contacto.puesto }}
                </span>
                <span v-if="contacto.es_contacto_pago"
                    class="badge text-xs" style="background:#F0FDF4; color:#15803D;">
                    Pago
                </span>
                <span v-if="!contacto.activo"
                    class="badge text-xs" style="background:#FEF2F2; color:#DC2626;">
                    Inactivo
                </span>
            </div>
            <div class="mt-1 space-y-0.5">
                <a v-if="contacto.correo" :href="`mailto:${contacto.correo}`"
                    class="flex items-center gap-1.5 text-xs hover:underline"
                    style="color:#5CC8F2;" @click.stop>
                    <EnvelopeIcon class="w-3.5 h-3.5" />
                    {{ contacto.correo }}
                </a>
                <a v-if="contacto.celular" :href="`tel:${contacto.celular}`"
                    class="flex items-center gap-1.5 text-xs"
                    style="color:#595959;" @click.stop>
                    <PhoneIcon class="w-3.5 h-3.5" />
                    {{ contacto.celular }}
                </a>
            </div>
        </div>
        <div v-if="puedeEditar" class="flex gap-1 shrink-0">
            <button @click="$emit('editar')"
                class="p-1.5 rounded hover:bg-gray-100 transition-colors" style="color:#595959;">
                <PencilIcon class="w-3.5 h-3.5" />
            </button>
            <button @click="$emit('eliminar')"
                class="p-1.5 rounded hover:bg-red-50 transition-colors" style="color:#EF4444;">
                <TrashIcon class="w-3.5 h-3.5" />
            </button>
        </div>
    </div>
</template>

<script setup>
import { EnvelopeIcon, PhoneIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
defineProps({ contacto: Object, puedeEditar: Boolean })
defineEmits(['editar', 'eliminar'])
</script>
