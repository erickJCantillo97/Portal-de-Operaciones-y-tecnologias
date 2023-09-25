<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { FilterMatchMode } from 'primevue/api';
import AssignmentModal from './AssignmentModal.vue';
import Button from '@/Components/Button.vue';
import Calendar from 'primevue/calendar';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InputText from 'primevue/inputtext';
import '../../../sass/dataTableCustomized.scss';
import Avatars from '@/Components/Avatars.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import MinimalMenu from '@/Components/MinimalMenu.vue';

const props = defineProps({
    projects: Array,
})


const unidad = {
    day: 'Dias',
    hour: 'Horas'
};

const projects = ref()
onMounted(() => {
    getTask('today')
})
const dates = ref([]);
const tasks = ref([]);
const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'project.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
    'project.id': { value: null, matchMode: FilterMatchMode.EQUALS },
    'manager': { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const optionValue = ref('today')

function obtenerLunesYViernesSemanaActual() {
    const hoy = new Date();
    const diaSemana = hoy.getDay(); // 0 para domingo, 1 para lunes, ..., 6 para sábado

    // Calcula la fecha del lunes de la semana actual
    const lunes = new Date(hoy);
    lunes.setDate(hoy.getDate() - diaSemana + (diaSemana === 0 ? -6 : 1));

    // Calcula la fecha del viernes de la semana actual
    const viernes = new Date(lunes);
    viernes.setDate(lunes.getDate() + 4);


    return {
        lunes: lunes,
        viernes: viernes
    };
}

const getTask = (option) => {

    const today = new Date();
    optionValue.value = option
    switch (option) {
        case 'today':
            dates.value[0] = today;
            dates.value[1] = today;
            break;
        case 'tomorrow':
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dates.value[0] = tomorrow;
            dates.value[1] = tomorrow;
            break;

        case 'week':
            const week = new Date();
            const fechasSemanaActual = obtenerLunesYViernesSemanaActual();
            week.setDate(week.getDate() + 7);
            dates.value[0] = fechasSemanaActual.lunes;
            dates.value[1] = fechasSemanaActual.viernes;
            break;
        default:
            break;
    }

    if (dates.value[1] != null) {
        tasks.value = [];
        loading.value = true;

        axios.get(route('actividadesDeultimonivel', { dates: dates.value })).then((res) => {
            loading.value = false;
            tasks.value = res.data
            projects.value = [...new Set(res.data.map(obj => obj.project.id))]
        })
    }

}

const redondear = (value) => {
    return new Intl.NumberFormat().format(Number(value).toFixed(2))
}

const filterProject = (id) => {
    filters.value['project.id'].value = id;
}

//#region
const items = ref([
    {
        label: 'Editar recursos',
        icon: 'fa-solid fa-chart-gantt',
        url: {
            name: 'createSchedule.create',
            parametter: ''
        }
    },
    {
        label: 'Ver programacion',
        icon: 'fa-solid fa-list-check',
        url: {
            name: 'programming',
            parametter: ''
        }
    },
]);

//#endregion

</script>

<template>
    <AppLayout>
        <div class="w-full p-4 px-auto">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Programación de Actividades
                    </h1>
                </div>
            </div>
            <div>
                <div class="flex space-x-2 my-4 ">
                    <div class="p-1 w-1/12">
                        <Button icon="pi pi-filter-slash shadow-xl" @click="filterProject()" type="button" text=""
                            severity="primary" class="hover:bg-primary ">
                            Ver Todos
                        </Button>
                    </div>
                    <div class="flex w-11/12 space-x-2 shadow-sm shadow-primary rounded-xl p-1">
                        <ProjectCard v-for="project in projects" :projectId=project class="cursor-pointer"
                            @click="filterProject(project)" :activo="filters['project.id'].value == project" />
                    </div>
                </div>

            </div>
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="tasks" v-model:filters="filters" dataKey="id"
                filterDisplay="menu" :loading="loading" sortMode="multiple"
                :globalFilterFields="['name', 'project', 'executor', 'manager', 'duration', 'startDate', 'endDate',]"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">
                <template #empty>
                    <p class="pt-5 pb-5 text-center">No hay tareas para mostrar</p>
                </template>
                <template #loading>
                    <p class="pt-5 pb-5 text-center">Cargando tareas...</p>
                </template>

                <template #header>
                    <div class="flex justify-between w-full mb-2">
                        <div class="flex space-x-4 alturah8">
                            <Button icon="pi pi-filter-slash shadow-xl" @click="clearFilter()" type="button" text=""
                                severity="primary" class="hover:bg-primary ">
                                <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                            </Button>
                            <span class="p-float-label">
                                <InputText id="buscar" v-model="filters.global.value" type="search"
                                    class="block text-gray-900 rounded-md shadow-xl alturah8 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                <label for="buscar">Buscar...</label>
                            </span>
                        </div>
                        <div class="flex justify-end mb-2 space-x-4 alturah8">
                            <span class="shadow-xl alturah8">
                                <button type="button"
                                    :class="optionValue == 'today' ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                                    @click="getTask('today')"
                                    class="relative inline-flex items-center px-3 py-2 text-sm font-semibold alturah8 rounded-l-md 0 ring-1 ring-inset ring-gray-300 focus:z-10">Hoy</button>
                                <button type="button"
                                    :class="optionValue == 'tomorrow' ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                                    @click="getTask('tomorrow')"
                                    class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-semibold alturah8 ring-1 ring-inset ring-gray-300 focus:z-10">Mañana</button>
                                <button type="button"
                                    :class="optionValue == 'week' ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                                    @click="getTask('week')"
                                    class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-semibold alturah8 rounded-r-md ring-1 ring-inset ring-gray-300 focus:z-10">Semana</button>
                            </span>
                            <span class="p-float-label">
                                <calendar id="calendar" selectionMode="range" v-model="dates"
                                    v-on:date-select="getTask('range')"
                                    class="shadow-xl alturah8 focus:ring-2 focus:ring-indigo-600 focus:ring-inset h-9">
                                </calendar>
                                <label for="calendar">Rango de fechas</label>
                            </span>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="name" header="Tarea"></Column>
                <Column field="project.ship.name" header="Proyecto" :show-filter-match-modes="false"
                    v-if="filters['project.id'].value == null">
                    <!-- <template #body="{ data }">
                    {{ data.project.name }}
                </template> -->
                    <!-- <template #filter="{ filterModel, filterCallback }">
                        <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter"
                            placeholder="Busca por proyecto" />
                    </template>
                    <template #body="slotProps">

                    </template> -->
                </Column>
                <Column field="manager" header="Responsable" :show-filter-match-modes="false">
                    <template #filter="{ filterModel, filterCallback }">
                        <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter"
                            placeholder="Busca por Responsable" />
                    </template>
                </Column>
                <Column field="executor" header="Ejecutor"></Column>
                <Column field="duration" header="Duración">
                    <template #body="slotProps">
                        {{ redondear(slotProps.data.duration) }} {{ unidad[slotProps.data.durationUnit] }}
                    </template>
                </Column>
                <Column field="startDate" header="Fecha inicio"></Column>
                <Column field="endDate" header="Fecha fin"></Column>
                <Column field="" header="Recursos">
                    <template #body="slotProps">
                        <Avatars :assignments=slotProps.data.assignments />
                    </template>
                </Column>
                <Column field="" header="Acciones" bodyStyle="text-align:center">
                    <template #body="slotProps">
                        <MinimalMenu :items="items" :header="true">
                            <template #header>
                                <p class="text-black text-center">{{ slotProps.data.name }}</p>
                            </template>
                        </MinimalMenu>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
