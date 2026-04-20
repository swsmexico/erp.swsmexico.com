<template>
    <AppLayout>
        <div class="flex items-center justify-center h-64 rounded-xl border-2 border-dashed border-gray-300">
            <div class="text-center">
                <ChartBarIcon class="w-12 h-12 mx-auto mb-3" style="color:#5CC8F2;" />
                <p class="text-lg font-semibold" style="color:#0D0D0D;">Dashboard</p>
                <p class="text-sm mt-1" style="color:#595959;">En construcción</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ChartBarIcon } from '@heroicons/vue/24/outline'
</script>
