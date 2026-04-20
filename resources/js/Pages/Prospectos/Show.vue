<template>
    <AppLayout>
        <div class="page-header">
            <div class="flex items-center gap-3">
                <Link :href="route('prospectos.index')" class="p-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                    <ArrowLeftIcon class="w-5 h-5" style="color:#595959;" />
                </Link>
                <div>
                    <h1 class="page-title">{{ prospecto.nombre_comercial }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="w-2 h-2 rounded-full" :style="`background:${prospecto.estado?.color};`" />
                        <span class="text-sm" style="color:#595959;">{{ prospecto.estado?.nombre }}</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <Link
                    v-if="can('prospectos.editar')"
                    :href="route('prospectos.convertir', prospecto.id)"
                    class="btn-primary"
                >Convertir a cliente</Link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Columna izquierda -->
            <div class="space-y-5">

                <!-- Datos generales -->
                <div class="card p-5">
                    <h2 class="text-sm font-semibold mb-4" style="color:#0D0D0D;">Datos generales</h2>

                    <!-- Cambiar estado -->
                    <div class="mb-4">
                        <label class="label">Estado</label>
                        <select
                            :value="prospecto.estado_id"
                            @change="cambiarEstado($event.target.value)"
                            class="input"
                        >
                            <option v-for="e in estados" :key="e.id" :value="e.id">
                                {{ e.nombre }}
                            </option>
                        </select>
                    </div>

                    <div v-if="prospecto.valor" class="mb-3">
                        <span class="label">Valor estimado</span>
                        <p class="text-lg font-bold" style="color:#5CC8F2;">{{ formatMXN(prospecto.valor) }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="label">Próximo seguimiento</label>
                        <input
                            :value="editForm.proximo_seguimiento"
                            @change="editForm.proximo_seguimiento = $event.target.value; guardarFecha()"
                            type="date"
                            class="input"
                        />
                    </div>

                    <div v-if="prospecto.ultimo_seguimiento">
                        <span class="label">Último seguimiento</span>
                        <p class="text-sm" style="color:#595959;">
                            {{ dayjs(prospecto.ultimo_seguimiento).format('DD MMM YYYY') }}
                        </p>
                    </div>
                </div>

                <!-- Contactos -->
                <div class="card p-5">
                    <h2 class="text-sm font-semibold mb-4" style="color:#0D0D0D;">Contactos</h2>
                    <div v-if="prospecto.contactos?.length" class="space-y-2">
                        <div v-for="c in prospecto.contactos" :key="c.id"
                            class="p-2.5 rounded-lg border border-gray-100 text-sm">
                            <p class="font-medium" style="color:#0D0D0D;">{{ c.nombre || 'Sin nombre' }}</p>
                            <a v-if="c.correo" :href="`mailto:${c.correo}`"
                                class="text-xs hover:underline" style="color:#5CC8F2;">{{ c.correo }}</a>
                            <p v-if="c.telefono" class="text-xs" style="color:#595959;">{{ c.telefono }}</p>
                        </div>
                    </div>
                    <p v-else class="text-sm" style="color:#9CA3AF;">Sin contactos</p>
                </div>

                <!-- Cotizaciones -->
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold" style="color:#0D0D0D;">Cotizaciones</h2>
                        <button @click="modalCotizacion = true"
                            class="text-xs font-medium hover:underline" style="color:#5CC8F2;">
                            + Agregar
                        </button>
                    </div>
                    <div v-if="prospecto.cotizaciones?.length" class="space-y-2">
                        <div v-for="c in prospecto.cotizaciones" :key="c.id"
                            class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100">
                            <span class="text-sm truncate" style="color:#0D0D0D;">{{ c.nombre }}</span>
                            <span class="text-sm font-semibold shrink-0 ml-2" style="color:#5CC8F2;">
                                {{ formatMXN(c.total) }}
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-sm" style="color:#9CA3AF;">Sin cotizaciones</p>
                </div>
            </div>

            <!-- Columna derecha: seguimientos -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Nuevo seguimiento con IA -->
                <div class="card p-5">
                    <h2 class="text-sm font-semibold mb-4" style="color:#0D0D0D;">Nuevo seguimiento</h2>

                    <div class="space-y-3">
                        <!-- Canal -->
                        <div class="flex gap-2">
                            <button
                                v-for="c in canales" :key="c.key"
                                @click="canalSeleccionado = c.key"
                                :class="['flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors border', canalSeleccionado === c.key ? 'border-transparent text-white' : 'border-gray-200 hover:bg-gray-50']"
                                :style="canalSeleccionado === c.key ? `background:${c.color};` : 'color:#595959;'"
                            >
                                <component :is="c.icon" class="w-4 h-4" />
                                {{ c.label }}
                            </button>
                        </div>

                        <!-- Texto informal -->
                        <div>
                            <label class="label">Tu mensaje (informal)</label>
                            <textarea
                                v-model="mensajeInformal"
                                rows="3"
                                class="input resize-none"
                                placeholder="Escribe libremente lo que quieres comunicar..."
                            />
                        </div>

                        <!-- Botón pulir -->
                        <button
                            @click="pulirMensaje"
                            :disabled="!mensajeInformal.trim() || puliendoIA"
                            class="btn-secondary w-full justify-center"
                        >
                            <SparklesIcon class="w-4 h-4" style="color:#5CC8F2;" />
                            {{ puliendoIA ? 'Puliendo con IA...' : 'Pulir con IA' }}
                        </button>

                        <!-- Mensaje formal editable -->
                        <div v-if="mensajeFormal">
                            <label class="label">Mensaje formal (editable)</label>
                            <textarea
                                v-model="mensajeFormal"
                                rows="5"
                                class="input resize-none"
                            />
                            <div class="flex justify-end mt-2">
                                <button @click="enviarSeguimiento" :disabled="enviando" class="btn-primary">
                                    <PaperAirplaneIcon class="w-4 h-4" />
                                    {{ enviando ? 'Enviando...' : `Enviar por ${canalSeleccionado === 'correo' ? 'correo' : 'WhatsApp'}` }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de seguimientos -->
                <div class="card p-5">
                    <h2 class="text-sm font-semibold mb-4" style="color:#0D0D0D;">Historial de seguimientos</h2>

                    <div v-if="prospecto.seguimientos?.length" class="space-y-4">
                        <SeguimientoItem
                            v-for="s in prospecto.seguimientos"
                            :key="s.id"
                            :seguimiento="s"
                        />
                    </div>
                    <p v-else class="text-sm text-center py-6" style="color:#9CA3AF;">
                        Sin seguimientos registrados
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal cotización -->
        <ModalCotizacion
            v-if="modalCotizacion"
            :prospecto-id="prospecto.id"
            @cerrar="modalCotizacion = false"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SeguimientoItem from '@/Components/Prospectos/SeguimientoItem.vue'
import ModalCotizacion from '@/Components/Prospectos/ModalCotizacion.vue'
import { usePermissions } from '@/composables/usePermissions'
import {
    ArrowLeftIcon, SparklesIcon, PaperAirplaneIcon,
    EnvelopeIcon,
} from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'
import 'dayjs/locale/es'
import axios from 'axios'
dayjs.locale('es')

const props  = defineProps({ prospecto: Object, estados: Array })
const { can } = usePermissions()

const canalSeleccionado = ref('correo')
const mensajeInformal   = ref('')
const mensajeFormal     = ref('')
const puliendoIA        = ref(false)
const enviando          = ref(false)
const modalCotizacion   = ref(false)

const canales = [
    { key: 'correo',    label: 'Correo',    color: '#5CC8F2', icon: EnvelopeIcon },
    { key: 'whatsapp',  label: 'WhatsApp',  color: '#22C55E', icon: EnvelopeIcon },
]

const editForm = useForm({
    nombre_comercial:    props.prospecto.nombre_comercial,
    valor:               props.prospecto.valor,
    estado_id:           props.prospecto.estado_id,
    proximo_seguimiento: props.prospecto.proximo_seguimiento ?? '',
})

const cambiarEstado = (estadoId) => {
    editForm.estado_id = Number(estadoId)
    editForm.patch(route('prospectos.update', props.prospecto.id), { preserveScroll: true })
}

const guardarFecha = () => {
    editForm.patch(route('prospectos.update', props.prospecto.id), { preserveScroll: true })
}

const pulirMensaje = async () => {
    puliendoIA.value = true
    try {
        const { data } = await axios.post(route('prospectos.pulir', props.prospecto.id), {
            mensaje: mensajeInformal.value,
            canal:   canalSeleccionado.value,
        })
        mensajeFormal.value = data.mensaje_formal
    } finally {
        puliendoIA.value = false
    }
}

const enviarSeguimiento = () => {
    enviando.value = true
    router.post(route('prospectos.seguimiento.store', props.prospecto.id), {
        mensaje_original: mensajeInformal.value,
        mensaje_formal:   mensajeFormal.value,
        canal:            canalSeleccionado.value,
    }, {
        onSuccess:  () => { mensajeInformal.value = ''; mensajeFormal.value = '' },
        onFinish:   () => { enviando.value = false },
        preserveScroll: true,
    })
}

const formatMXN = (val) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0)
</script>
