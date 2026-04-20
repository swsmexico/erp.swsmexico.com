<template>
    <Link
        :href="route('proyectos.show', proyecto.id)"
        class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50 transition-all"
    >
        <span class="text-sm truncate" style="color:#0D0D0D;">{{ proyecto.nombre }}</span>
        <div class="flex items-center gap-2 shrink-0">
            <span v-if="proyecto.departamento" class="text-xs" style="color:#595959;">
                {{ proyecto.departamento }}
            </span>
            <EstadoProyectoBadge :estatus="proyecto.estatus" />
        </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import EstadoProyectoBadge from '@/Components/UI/EstadoProyectoBadge.vue'
defineProps({ proyecto: Object })
</script>
