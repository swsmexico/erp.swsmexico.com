<template>
    <Link
        :href="route('cobranza.show', prefactura.id)"
        class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50 transition-all"
    >
        <div class="flex items-center gap-3 min-w-0">
            <span class="text-xs font-mono px-2 py-0.5 rounded" style="background:#F3F4F6; color:#595959;">
                {{ prefactura.folio }}
            </span>
            <span class="text-sm truncate" style="color:#0D0D0D;">{{ prefactura.descripcion }}</span>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-sm font-medium">{{ formatMXN(prefactura.total) }}</span>
            <EstadoBadge :estado="prefactura.estado" />
        </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import EstadoBadge from '@/Components/UI/EstadoBadge.vue'

defineProps({ prefactura: Object })

const formatMXN = (val) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0)
</script>
