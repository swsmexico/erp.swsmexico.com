<template>
    <div class="border-l-2 pl-4 pb-4" :style="`border-color:${colorCanal};`">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-semibold" :style="`color:${colorCanal};`">
                {{ labelCanal }}
            </span>
            <span class="text-xs" style="color:#9CA3AF;">
                {{ seguimiento.usuario }} · {{ seguimiento.enviado_at }}
            </span>
        </div>
        <div class="card p-3">
            <p class="text-sm whitespace-pre-wrap" style="color:#0D0D0D;">{{ seguimiento.mensaje_formal }}</p>
            <!-- Respuesta -->
            <div v-if="seguimiento.respuesta" class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs font-medium mb-1" style="color:#22C55E;">Respuesta · {{ seguimiento.respuesta_at }}</p>
                <p class="text-sm whitespace-pre-wrap" style="color:#595959;">{{ seguimiento.respuesta }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ seguimiento: Object })

const colorCanal = computed(() => props.seguimiento.canal === 'whatsapp' ? '#22C55E' : '#5CC8F2')
const labelCanal = computed(() => props.seguimiento.canal === 'whatsapp' ? 'WhatsApp' : 'Correo')
</script>
