<template>
    <span class="badge" :style="estilos[estatus] ?? estilos.default">
        {{ etiquetas[estatus] ?? estatus }}
    </span>
</template>

<script setup>
defineProps({ estatus: String })

const etiquetas = {
    en_progreso: 'En progreso',
    en_pausa:    'En pausa',
    terminado:   'Terminado',
    cancelado:   'Cancelado',
}

const estilos = {
    en_progreso: 'background:#EFF6FF; color:#1D4ED8;',
    en_pausa:    'background:#FEF9C3; color:#854D0E;',
    terminado:   'background:#F0FDF4; color:#15803D;',
    cancelado:   'background:#F3F4F6; color:#6B7280;',
    default:     'background:#F3F4F6; color:#6B7280;',
}
</script>
