<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm w-full pointer-events-none">
            <TransitionGroup
                enter-from-class="translate-x-full opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-full opacity-0"
                enter-active-class="transition-all duration-300"
                leave-active-class="transition-all duration-300"
            >
                <div
                    v-for="msg in messages"
                    :key="msg.id"
                    class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white"
                    :style="bgColor(msg.type)"
                >
                    <component :is="icon(msg.type)" class="w-5 h-5 shrink-0 mt-0.5" />
                    <span class="flex-1">{{ msg.text }}</span>
                    <button @click="remove(msg.id)" class="opacity-70 hover:opacity-100 transition-opacity">
                        <XMarkIcon class="w-4 h-4" />
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import {
    CheckCircleIcon, ExclamationCircleIcon,
    ExclamationTriangleIcon, XMarkIcon,
} from '@heroicons/vue/24/solid'

const page = usePage()
const messages = ref([])
let counter = 0

const bgColor = (type) => ({
    success: 'background:#22C55E;',
    error:   'background:#EF4444;',
    warning: 'background:#F59E0B;',
}[type] || 'background:#6B7280;')

const icon = (type) => ({
    success: CheckCircleIcon,
    error:   ExclamationCircleIcon,
    warning: ExclamationTriangleIcon,
}[type] || ExclamationCircleIcon)

const remove = (id) => {
    messages.value = messages.value.filter(m => m.id !== id)
}

const add = (text, type) => {
    if (!text) return
    const id = ++counter
    messages.value.push({ id, text, type })
    setTimeout(() => remove(id), 4000)
}

watch(() => page.props.flash, (flash) => {
    if (flash?.success) add(flash.success, 'success')
    if (flash?.error)   add(flash.error,   'error')
    if (flash?.warning) add(flash.warning, 'warning')
}, { deep: true })
</script>
