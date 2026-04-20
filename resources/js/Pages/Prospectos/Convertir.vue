<template>
    <AppLayout>
        <div class="page-header">
            <div class="flex items-center gap-3">
                <Link :href="route('prospectos.show', prospecto.id)" class="p-1.5 rounded-lg hover:bg-gray-200">
                    <ArrowLeftIcon class="w-5 h-5" style="color:#595959;" />
                </Link>
                <h1 class="page-title">Convertir a cliente — {{ prospecto.nombre_comercial }}</h1>
            </div>
        </div>

        <!-- Pasos -->
        <div class="flex items-center gap-2 mb-8">
            <div v-for="(paso, i) in pasos" :key="i" class="flex items-center gap-2">
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold transition-colors"
                    :style="pasoActivo >= i
                        ? 'background:#5CC8F2; color:white;'
                        : 'background:#E5E7EB; color:#595959;'"
                >{{ i + 1 }}</div>
                <span class="text-sm hidden sm:block"
                    :style="pasoActivo === i ? 'color:#0D0D0D; font-weight:600;' : 'color:#595959;'">
                    {{ paso }}
                </span>
                <ChevronRightIcon v-if="i < pasos.length - 1" class="w-4 h-4" style="color:#D1D5DB;" />
            </div>
        </div>

        <div class="max-w-xl">

            <!-- Paso 1: RFC -->
            <div v-if="pasoActivo === 0" class="card p-6 space-y-4">
                <h2 class="text-sm font-semibold" style="color:#0D0D0D;">Datos fiscales del cliente</h2>
                <div>
                    <label class="label">RFC</label>
                    <input v-model="form.rfc" type="text" class="input" placeholder="Opcional" />
                </div>
                <div>
                    <label class="label">Razón social</label>
                    <input v-model="form.razon_social" type="text" class="input" placeholder="Opcional" />
                </div>
                <p class="text-xs" style="color:#9CA3AF;">
                    Estos datos se pueden completar después desde el perfil del cliente.
                </p>
            </div>

            <!-- Paso 2: Prefactura inicial -->
            <div v-if="pasoActivo === 1" class="card p-6 space-y-4">
                <h2 class="text-sm font-semibold" style="color:#0D0D0D;">Primera prefactura</h2>
                <div>
                    <label class="label">Descripción del servicio *</label>
                    <input v-model="form.prefactura_descripcion" type="text" class="input" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Monto *</label>
                        <input v-model="form.prefactura_monto" type="number" class="input" min="0" />
                    </div>
                    <div>
                        <label class="label">Recurrencia</label>
                        <select v-model="form.prefactura_recurrencia" class="input">
                            <option value="unica">Único pago</option>
                            <option value="mensual">Mensual</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                </div>
                <p class="text-xs" style="color:#9CA3AF;">
                    También se puede omitir y crear desde Cobranza después.
                </p>
            </div>

            <!-- Paso 3: Confirmar contactos -->
            <div v-if="pasoActivo === 2" class="card p-6 space-y-3">
                <h2 class="text-sm font-semibold" style="color:#0D0D0D;">Contactos</h2>
                <p class="text-sm" style="color:#595959;">
                    Se importarán los siguientes contactos del prospecto:
                </p>
                <div v-if="prospecto.contactos?.length" class="space-y-2">
                    <div v-for="c in prospecto.contactos" :key="c.id"
                        class="flex items-center gap-3 p-2.5 rounded-lg border border-gray-100">
                        <input type="checkbox" :value="c.id" v-model="form.contactos_ids" class="rounded" />
                        <div>
                            <p class="text-sm font-medium" style="color:#0D0D0D;">{{ c.nombre || 'Sin nombre' }}</p>
                            <p v-if="c.correo" class="text-xs" style="color:#595959;">{{ c.correo }}</p>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm" style="color:#9CA3AF;">Sin contactos en el prospecto.</p>
            </div>

            <!-- Paso 4: Confirmar -->
            <div v-if="pasoActivo === 3" class="card p-6 space-y-4">
                <h2 class="text-sm font-semibold" style="color:#0D0D0D;">Confirmar conversión</h2>
                <div class="rounded-lg p-4 space-y-2 text-sm" style="background:#F0FDF4;">
                    <p><span class="font-medium">Cliente:</span> {{ prospecto.nombre_comercial }}</p>
                    <p v-if="form.rfc"><span class="font-medium">RFC:</span> {{ form.rfc }}</p>
                    <p v-if="form.prefactura_descripcion">
                        <span class="font-medium">Prefactura:</span>
                        {{ form.prefactura_descripcion }} — {{ formatMXN(form.prefactura_monto) }}
                    </p>
                    <p><span class="font-medium">Contactos a importar:</span> {{ form.contactos_ids.length }}</p>
                </div>
                <p class="text-xs" style="color:#9CA3AF;">
                    El prospecto quedará archivado y se creará el cliente automáticamente.
                </p>
            </div>

            <!-- Navegación -->
            <div class="flex justify-between mt-6">
                <button v-if="pasoActivo > 0" @click="pasoActivo--" class="btn-secondary">
                    Anterior
                </button>
                <div v-else />
                <div class="flex gap-3">
                    <button v-if="pasoActivo < pasos.length - 1" @click="pasoActivo++" class="btn-primary">
                        Siguiente
                    </button>
                    <button v-else @click="convertir" :disabled="form.processing" class="btn-primary">
                        {{ form.processing ? 'Convirtiendo...' : 'Crear cliente' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ArrowLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({ prospecto: Object })

const pasoActivo = ref(0)
const pasos = ['RFC', 'Prefactura', 'Contactos', 'Confirmar']

const form = useForm({
    prospecto_id:             props.prospecto.id,
    rfc:                      '',
    razon_social:             '',
    prefactura_descripcion:   '',
    prefactura_monto:         '',
    prefactura_recurrencia:   'unica',
    contactos_ids:            props.prospecto.contactos?.map(c => c.id) ?? [],
})

const convertir = () => {
    form.post(route('clientes.store'), {
        onSuccess: () => {},
    })
}

const formatMXN = (val) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0)
</script>
