<template>
    <div
        class="bg-white rounded-xl border border-gray-200 p-3 cursor-pointer hover:shadow-md hover:border-gray-300 transition-all select-none"
        @click="$emit('click')"
    >
        <!-- Nombre y valor -->
        <div class="flex items-start justify-between gap-2 mb-2">
            <span class="text-sm font-semibold leading-tight" style="color:#0D0D0D;">
                {{ prospecto.nombre_comercial }}
            </span>
            <span v-if="prospecto.valor" class="text-xs font-bold shrink-0" style="color:#5CC8F2;">
                {{ formatMXN(prospecto.valor) }}
            </span>
        </div>

        <!-- Contacto principal -->
        <div v-if="prospecto.contacto_principal?.nombre" class="flex items-center gap-1.5 mb-2">
            <UserIcon class="w-3.5 h-3.5 shrink-0" style="color:#595959;" />
            <span class="text-xs truncate" style="color:#595959;">
                {{ prospecto.contacto_principal.nombre }}
            </span>
        </div>

        <!-- Fechas de seguimiento -->
        <div class="flex items-center gap-3 mt-2">
            <div v-if="prospecto.proximo_seguimiento" class="flex items-center gap-1">
                <CalendarIcon class="w-3.5 h-3.5" :style="esSeguimientoVencido ? 'color:#EF4444;' : 'color:#595959;'" />
                <span
                    class="text-xs"
                    :style="esSeguimientoVencido ? 'color:#EF4444;' : 'color:#595959;'"
                >{{ formatFecha(prospecto.proximo_seguimiento) }}</span>
            </div>
            <div v-if="prospecto.ultimo_seguimiento" class="flex items-center gap-1 ml-auto">
                <ClockIcon class="w-3.5 h-3.5" style="color:#9CA3AF;" />
                <span class="text-xs" style="color:#9CA3AF;">
                    {{ formatFechaRelativa(prospecto.ultimo_seguimiento) }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { UserIcon, CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/es'

dayjs.extend(relativeTime)
dayjs.locale('es')

const props = defineProps({ prospecto: Object })
defineEmits(['click'])

const esSeguimientoVencido = computed(() =>
    props.prospecto.proximo_seguimiento &&
    dayjs(props.prospecto.proximo_seguimiento).isBefore(dayjs(), 'day')
)

const formatMXN = (val) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 }).format(val)

const formatFecha = (fecha) => dayjs(fecha).format('DD MMM')

const formatFechaRelativa = (fecha) => dayjs(fecha).fromNow()
</script>
