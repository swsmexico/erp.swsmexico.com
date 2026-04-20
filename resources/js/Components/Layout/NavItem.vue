<template>
    <Link
        :href="route(item.route)"
        :class="[
            'flex items-center gap-3 mx-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150',
            isActive
                ? 'text-white'
                : 'hover:bg-gray-100',
        ]"
        :style="isActive ? 'background:#5CC8F2; color:#fff;' : 'color:#595959;'"
    >
        <component :is="item.icon" class="w-5 h-5 shrink-0" />
        <span
            v-if="!collapsed"
            class="truncate transition-opacity duration-200"
        >{{ item.label }}</span>

        <!-- Tooltip cuando colapsado -->
        <div
            v-if="collapsed"
            class="absolute left-16 px-2 py-1 text-xs text-white rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none z-50 transition-opacity"
            style="background:#0D0D0D;"
        >{{ item.label }}</div>
    </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    item: Object,
    collapsed: Boolean,
})

const page = usePage()
const isActive = computed(() => page.url.startsWith('/' + props.item.route.split('.')[0]))
</script>
