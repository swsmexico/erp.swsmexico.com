<template>
    <button
        class="flex items-start gap-3 w-full px-4 py-3 hover:bg-gray-50 text-left border-b border-gray-100 last:border-0 transition-colors"
        :class="!notif.leida_at ? 'bg-blue-50/50' : ''"
    >
        <div
            class="w-2 h-2 rounded-full mt-1.5 shrink-0"
            :style="!notif.leida_at ? 'background:#5CC8F2;' : 'background:transparent;'"
        />
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate" style="color:#0D0D0D;">{{ notif.titulo }}</p>
            <p v-if="notif.cuerpo" class="text-xs mt-0.5 line-clamp-2" style="color:#595959;">{{ notif.cuerpo }}</p>
            <p class="text-xs mt-1" style="color:#595959;">{{ fechaRelativa(notif.created_at) }}</p>
        </div>
    </button>
</template>

<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/es'

dayjs.extend(relativeTime)
dayjs.locale('es')

defineProps({ notif: Object })

const fechaRelativa = (fecha) => dayjs(fecha).fromNow()
</script>
