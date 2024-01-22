<script setup>
import CustomDataTable from '@/Components/CustomDataTable.vue';
import { ResourceTimeRangeModel } from '@bryntum/gantt';
import { router } from "@inertiajs/vue3";
import { onMounted, ref } from 'vue';

const columnas = [
    { field: 'name', header: 'Proyecto' },
    { field: 'avance', header: 'Ejecución' },
    { field: 'contrato', header: 'Contrato' },
    { field: 'costo', header: 'Valor Venta' },
    { field: 'fechaF', header: 'Fin Producción' },
]
//#region Botones de CustomDatatable
const buttons = [
    { event: 'showProgramming', severity: 'success', class: '', icon: 'fa-solid fa-list-check', text: true, outlined: false, rounded: false },
    { event: 'showGantt', severity: 'primary', class: '', icon: 'fa-solid fa-list-check', text: true, outlined: false, rounded: false },
    // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

const projects = ref([])
onMounted(() => {
    axios.get(route("project.active")).then((res) => {
        projects.value = res.data.projects
    });
});

const gannt = (event, data) => {
    router.get(route('createSchedule.create', data.project_id))
}
</script>
<template>
    <CustomDataTable title="Proyectos" :data="projects" :rowsDefault="10" :columnas="columnas" :actions="buttons"
        @showProgramming="router.get(route('programming'), { id: $event.data.project_id })" @showGantt="gannt">
    </CustomDataTable>
</template>
    