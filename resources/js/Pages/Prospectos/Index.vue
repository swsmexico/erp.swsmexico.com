<template>
    <AppLayout>
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">Prospectos</h1>
            <button @click="modalNuevo = true" v-if="can('prospectos.crear')" class="btn-primary">
                <PlusIcon class="w-4 h-4" />
                Nuevo prospecto
            </button>
        </div>

        <!-- Kanban board -->
        <div class="flex gap-4 overflow-x-auto pb-4" style="min-height: calc(100vh - 160px);">
            <div
                v-for="estado in estados"
                :key="estado.id"
                class="flex flex-col rounded-xl flex-shrink-0 w-72"
                style="background:#F9FAFB;"
            >
                <!-- Header columna -->
                <div class="flex items-center justify-between px-3 py-3">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-2.5 h-2.5 rounded-full shrink-0"
                            :style="`background:${estado.color};`"
                        />
                        <span class="text-sm font-semibold" style="color:#0D0D0D;">{{ estado.nombre }}</span>
                        <span class="text-xs px-1.5 py-0.5 rounded-full" style="background:#E5E7EB; color:#595959;">
                            {{ estado.prospectos.length }}
                        </span>
                    </div>
                </div>

                <!-- Cards con drag & drop -->
                <VueDraggable
                    v-model="estado.prospectos"
                    :group="{ name: 'prospectos', pull: true, put: true }"
                    item-key="id"
                    class="flex flex-col gap-2 px-2 pb-2 flex-1 min-h-16"
                    @end="(e) => onDrop(e, estado.id)"
                    ghost-class="opacity-40"
                    drag-class="rotate-1 shadow-xl"
                >
                    <template #item="{ element }">
                        <ProspectoCard
                            :prospecto="element"
                            @click="irA(element)"
                        />
                    </template>
                </VueDraggable>
            </div>
        </div>

        <!-- Modal nuevo prospecto -->
        <ModalNuevoProspecto
            v-if="modalNuevo"
            :estados="estados"
            @cerrar="modalNuevo = false"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { VueDraggable } from 'vuedraggable'
import AppLayout from '@/Layouts/AppLayout.vue'
import ProspectoCard from '@/Components/Prospectos/ProspectoCard.vue'
import ModalNuevoProspecto from '@/Components/Prospectos/ModalNuevoProspecto.vue'
import { usePermissions } from '@/composables/usePermissions'
import { PlusIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

const props  = defineProps({ estados: Array })
const { can } = usePermissions()

const modalNuevo = ref(false)
const estados = ref(props.estados.map(e => ({
    ...e,
    prospectos: [...e.prospectos],
})))

const irA = (prospecto) => router.visit(route('prospectos.show', prospecto.id))

const onDrop = async (event, nuevoEstadoId) => {
    const prospectoId = event.item?.__draggable_context?.element?.id
    if (!prospectoId) return

    try {
        await axios.patch(route('prospectos.mover', prospectoId), {
            estado_id: nuevoEstadoId,
        })
    } catch {
        // revert si falla
        router.reload({ only: ['estados'] })
    }
}
</script>
