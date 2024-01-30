<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';

const props = defineProps({
    tools: Array,
});

const columnas = [
    { field: 'name', header: 'Proyecto' },
    { field: 'avance', header: 'Ejecución' },
    { field: 'contrato', header: 'Contrato' },
    { field: 'costo', header: 'Valor Venta', type: 'currency', class: 'w-32', },
    { field: 'fechaF', header: 'Fin Producción' },
]

const actions = [
    { event: 'showProgramming', severity: 'success', icon: 'fa-solid fa-list-check', text: true, outlined: false, rounded: false },
    { event: 'showGantt', severity: 'primary', icon: 'fa-solid fa-chart-gantt', text: true, outlined: false, rounded: false },
    // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

</script>

<template>
    <AppLayout>
        <CustomDataTable :data="[]" :columnas="columnas" :actions="actions">

        </CustomDataTable>
    </AppLayout>
</template>