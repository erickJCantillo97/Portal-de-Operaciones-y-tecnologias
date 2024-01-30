<script setup>
import CustomDataTable from '@/Components/CustomDataTable.vue';
import { router } from "@inertiajs/vue3";
import { onMounted, ref } from 'vue';
// import DataChart from "../DataChart.vue";


const columnas = [
    { field: 'name', header: 'Proyecto' },
    { field: 'avance', header: 'Ejecución' },
    { field: 'contrato', header: 'Contrato' },
    { field: 'costo', header: 'Valor Venta', type: 'currency', class: 'w-32', },
    { field: 'fechaF', header: 'Fin Producción' },
]
//#region Botones de CustomDatatable
const buttons = [
    { event: 'showProgramming', severity: 'success', icon: 'fa-solid fa-list-check', text: true, outlined: false, rounded: false },
    { event: 'showGantt', severity: 'primary', icon: 'fa-solid fa-chart-gantt', text: true, outlined: false, rounded: false },
    // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

const projects = ref([])
onMounted(() => {
    axios.get(route("project.active")).then((res) => {
        projects.value = res.data.projects
    });
});

</script>
<template>
    <div class="grid grid-cols-1">
        <CustomDataTable title="Proyectos" :data="projects" :rowsDefault="10" :columnas="columnas" :actions="buttons"
            @showProgramming="router.get(route('programming'), { id: $event.data.project_id })"
            @showGantt="router.get(route('createSchedule.create', $event.data.project_id))">
        </CustomDataTable>
        <!-- <DataChart /> -->
    </div>
</template>
