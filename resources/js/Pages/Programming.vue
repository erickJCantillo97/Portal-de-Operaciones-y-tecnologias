<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { FilterMatchMode } from 'primevue/api';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InputText from 'primevue/inputtext';
import '../../sass/dataTableCustomized.scss';

const props = defineProps({
    taskNow: Array,
})

const unidad = {
    day: 'Dias',
    hour: 'Horas'
};

onMounted(() => {
    tasks.value = props.taskNow;
})
const dates = ref([]);
const tasks = ref([]);
const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
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
        })
    }

}


const redondear = (value )=> {
    return new Intl.NumberFormat().format(Number(value).toFixed(2) )
}
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
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="tasks" v-model:filters="filters" dataKey="id"
                filterDisplay="menu" :loading="loading"
                :globalFilterFields="['name', 'gerencia', 'start_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">
                <template #empty>
                    <p class="text-center pt-5 pb-5">No hay tareas para mostrar</p>
                </template>
                <template #loading>
                    <p class="text-center pt-5 pb-5">Cargando tareas...</p>
                </template>

                <template #header>
                    <div class="w-full mb-2">
                        <div class="flex justify-end h-8 mb-2 space-x-4">
                            <span class="shadow-xl">
                                <button type="button" :class="optionValue == 'today' ? 'bg-sky-500 text-white':'bg-white hover:bg-sky-200 text-gray-90'" @click="getTask('today')"
                                    class="relative inline-flex items-center rounded-l-md  px-3 py-2 text-sm font-semibold 0 ring-1 ring-inset ring-gray-300 focus:z-10">Hoy</button>
                                <button type="button" :class="optionValue == 'tomorrow' ? 'bg-sky-500 text-white':'bg-white hover:bg-sky-200 text-gray-90'"  @click="getTask('tomorrow')"
                                    class="relative -ml-px inline-flex items-center  px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-10">Mañana</button>
                                <button type="button" :class="optionValue == 'week' ? 'bg-sky-500 text-white':'bg-white hover:bg-sky-200 text-gray-90'" @click="getTask('week')"
                                    class="relative -ml-px inline-flex items-center rounded-r-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300  focus:z-10">Semana</button>
                            </span>
                            <span class="p-float-label">
                                <calendar id="calendar" selectionMode="range" v-model="dates"
                                    v-on:date-select="getTask('range')"
                                    class="focus:ring-2 focus:ring-indigo-600 focus:ring-inset shadow-xl h-9"></calendar>
                                <label for="calendar">Rango de fechas</label>
                            </span>
                            <!-- <span class="p-float-label">
                                <Calendar id="calendar"
                                    class="block text-gray-900 border-0 rounded-md placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    :unstyled="false" v-on:date-select="getTask()" v-model="dates" selectionMode="range"
                                    :manualInput="false" />
                                <label for="calendar">Rango de fechas</label>
                            </span> -->
                        </div>
                        <div class="alturah8 flex h-8 space-x-4">
                            <Button icon="pi pi-filter-slash" @click="clearFilter()" type="button" text=""
                                severity="primary" class="hover:bg-primary ">
                            </Button>
                            <span class="p-float-label">
                                <InputText id="buscar" v-model="filters.global.value" :unstyled="true" type="search"
                                    class="block text-gray-900 border-0 rounded-md placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                <label for="buscar">Buscar...</label>
                            </span>
                            <!-- <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div> -->
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="name" header="Tarea"></Column>
                <Column field="project.name" header="Proyecto"></Column>
                <Column field="duration" header="Duracion">
                    <template #body="slotProps">
                        {{ redondear(slotProps.data.duration) }} {{ unidad[slotProps.data.durationUnit] }}
                    </template>
                </Column>
                <Column field="startDate" header="Fecha inicio"></Column>
                <Column field="endDate" header="Fecha fin"></Column>
                <Column field="" header="Recursos"></Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
