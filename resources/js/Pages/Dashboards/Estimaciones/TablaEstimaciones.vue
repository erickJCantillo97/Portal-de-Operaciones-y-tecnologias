<script setup>
import CustomDataTable from '@/Components/CustomDataTable.vue';
import { onMounted, ref } from 'vue';

const columnas = [
    { field: 'consecutive', header: 'Consecutivo', filter: true, sortable: true },
    { field: 'name', header: 'Nombre', filter: true, sortable: true },
    { field: 'clases', header: 'Clases', filter: true, sortable: true },
    { field: 'customer', header: 'Cliente', filter: true, sortable: true },
    { field: 'estimador', header: 'Estimador', filter: true, sortable: true },
    {
        field: 'get_status', header: 'Estado', filter: true, sortable: true, type: 'tag', filtertype: 'EQUALS',
        severitys: [
            { text: 'Proceso', severity: 'primary', class: 'bg-primary text-white' },
            { text: 'Entregada', severity: 'warning', class: '' },
            { text: 'Pendiente por Firma', severity: 'warning', class: '' },
            { text: 'Firmada', severity: 'success', class: 'bg-success text-white' },
            { text: 'No Firmada', severity: 'danger', class: 'bg-danger  text-white' },
            { text: 'Contratada', severity: 'success', class: '' }
        ]
    },
    { field: 'total_cost', header: 'Precio', type: 'currency', sortable: true, filter: true, },
    { field: 'expeted_answer_date', header: 'Fecha esperada', type: 'date', sortable: true, filter: true, },
    { field: 'estimador_anaswer_date', header: 'Fecha respuesta', type: 'date', sortable: true, filter: true },
    { field: 'route', header: 'Ruta', filter: false },
    { field: 'file', header: 'Documento', filter: false },
    { field: 'observation', header: 'Observacion', filter: false },
    { field: 'created_at', header: 'Fecha creacion', filter: false },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true },
]
const filterButtons = [
    { field: 'get_status', label: 'En proceso', data: 'Proceso', severity: 'primary' },
    { field: 'get_status', label: 'Entregadas', data: 'Entregada', severity: 'warning' },
    { field: 'get_status', label: 'Firmadas', data: 'Firmada', severity: 'success' },
    { field: 'get_status', label: 'Por firmar', data: 'Pendiente por Firma', severity: 'warning' },
]
const quotes = ref([])
onMounted(() => {
    quotes.value = []
});
</script>
<template>
    <CustomDataTable :data="quotes" title="Estimaciones" :columnas="columnas" :filterButtons="filterButtons">
    </CustomDataTable>
</template>
