<template>
    <span class="badge" :style="estilos[estado] ?? estilos.default">
        {{ etiquetas[estado] ?? estado }}
    </span>
</template>

<script setup>
defineProps({ estado: String })

const etiquetas = {
    pendiente: 'Pendiente',
    pagada:    'Pagada',
    cancelada: 'Cancelada',
}

const estilos = {
    pendiente: 'background:#FEF9C3; color:#854D0E;',
    pagada:    'background:#F0FDF4; color:#15803D;',
    cancelada: 'background:#F3F4F6; color:#6B7280;',
    default:   'background:#F3F4F6; color:#6B7280;',
}
</script>
