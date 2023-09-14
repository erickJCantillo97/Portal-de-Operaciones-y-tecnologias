<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { FilterMatchMode } from 'primevue/api';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon, MagnifyingGlassPlusIcon, SparklesIcon, EyeIcon, PhotoIcon, TableCellsIcon, ViewColumnsIcon } from '@heroicons/vue/24/outline';
import Button from '../Components/Button.vue';
import Calendar from 'primevue/calendar';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Combobox from '@/Components/Combobox.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InputText from 'primevue/inputtext';
import OverlayPanel from 'primevue/overlaypanel';
import '../../sass/dataTableCustomized.scss';

const open = ref(false)
const assignments = ref()

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


const redondear = (value) => {
    return new Intl.NumberFormat().format(Number(value).toFixed(2))
}

//#region Obtener API de Recursos
const getAssignmentsTask = (id) => {
    axios.get(route('get.assignments.task', id))
        .then((res) => {
            assignments.value = res.data.assignments
            open.value = true
        })
}
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
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="tasks" v-model:filters="filters" dataKey="id"
                filterDisplay="menu" :loading="loading"
                :globalFilterFields="['name', 'project.name', 'duration', 'startDate', 'endDate']"
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
                    <div class="w-full mb-2">
                        <div class="flex justify-end h-8 mb-2 space-x-4">
                            <span class="shadow-xl">
                                <button type="button"
                                    :class="optionValue == 'today' ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                                    @click="getTask('today')"
                                    class="relative inline-flex items-center px-3 py-2 text-sm font-semibold rounded-l-md 0 ring-1 ring-inset ring-gray-300 focus:z-10">Hoy</button>
                                <button type="button"
                                    :class="optionValue == 'tomorrow' ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                                    @click="getTask('tomorrow')"
                                    class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-10">Mañana</button>
                                <button type="button"
                                    :class="optionValue == 'week' ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                                    @click="getTask('week')"
                                    class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-semibold rounded-r-md ring-1 ring-inset ring-gray-300 focus:z-10">Semana</button>
                            </span>
                            <span class="p-float-label">
                                <calendar id="calendar" selectionMode="range" v-model="dates"
                                    v-on:date-select="getTask('range')"
                                    class="shadow-xl focus:ring-2 focus:ring-indigo-600 focus:ring-inset h-9"></calendar>
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
                        <div class="flex justify-between w-full h-8 mb-2">
                            <div class="flex space-x-4">
                                <div class="w-8" title="Filtrar Proyectos">
                                    <Button @click="clearFilter()" type="button" severity="primary"
                                        class="hover:bg-primary ">
                                        <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                                    </Button>
                                </div>

                                <div class="relative flex rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                    </div>
                                    <input type="search" title="Buscar Proyectos"
                                        class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        v-model="filters.global.value" placeholder="Buscar..." />
                                </div>
                            </div>
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
                <Column field="" header="Acciones">
                    <template #body="slotProps">
                        <!--BOTÓN VER RECURSOS-->
                        <div
                            class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">

                            <!--BOTÓN VER RECURSOS-->
                            <div title="Ver Recursos">
                                <Button severity="success" @click="getAssignmentsTask(slotProps.data.id)"
                                    class="hover:bg-primary">
                                    <EyeIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>

                            <!--BOTÓN EDITAR-->
                            <!-- <div title="Editar Proyecto">
                                <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                    <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div> -->
                            <!--BOTÓN ELIMINAR-->
                            <!-- <div title="Eliminar Proyecto">
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Proyecto', 'projects')"
                                    class="hover:bg-danger">
                                    <TrashIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div> -->
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!--MODAL DE VER RECURSOS-->
        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-30" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                    leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 z-30 w-screen h-screen transition-opacity bg-gray-500 bg-opacity-75" />
                </TransitionChild>

                <div class="fixed inset-0 z-50 h-screen overflow-y-auto">
                    <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel :class="props.heigthDialog"
                                class="relative px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg ">
                                <div>
                                    <div class="px-2 mt-2 text-center">
                                        <DialogTitle as="h3" class="text-xl font-semibold text-primary ">
                                            Ver Recursos
                                        </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                        <div class="p-2 mt-2 space-y-4 border border-gray-200 rounded-lg">
                                            <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                                <!-- Contenedor de datos -->
                                                <div class="p-4 mt-4 border border-gray-300 rounded-lg">
                                                    <h2 class="text-xl font-semibold">
                                                        Recursos Obtenidos
                                                    </h2>
                                                    <ul>
                                                        <li v-for="assignment in assignments" :key="assignment.id">
                                                            {{ assignment.name }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="flex px-2 mt-2 space-x-4">
                                    <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                        @click="open = false">Cancelar</Button>
                                    <Button severity="success" :loading="false"
                                        class="text-success hover:bg-success border-success" @click="submit()">
                                        {{ formData.id != 0 ? 'Actualizar ' : 'Guardar' }}
                                    </Button>
                                </div> -->
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <OverlayPanel ref="op">
            <div>
                <h2 class="text-base font-semibold leading-6 text-gray-900">Creación o edición de cronograma</h2>
                <p class="mt-1 text-sm text-gray-500">Aquí podrá escoger como desea crear el cronograma del proyecto.</p>

                <ul role="list" class="grid grid-cols-1 gap-6 py-6 mt-6 border-t border-b border-gray-200 sm:grid-cols-2">
                    <li v-for="(item, itemIdx) in items" :key="itemIdx" class="flow-root">
                        <div @click="router.get(route(item.page, projectSelect.id))"
                            class="relative flex items-center p-2 -m-2 space-x-4 rounded-xl focus-within:ring-2 focus-within:ring-indigo-500 hover:bg-gray-50">
                            <div
                                :class="[item.background, 'flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-lg']">
                                <component :is="item.icon" class="w-6 h-6 text-white" aria-hidden="true" />
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">
                                    <a href="#" class="focus:outline-none">
                                        <span class="absolute inset-0" aria-hidden="true" />
                                        <span>{{ item.title }}</span>
                                        <span aria-hidden="true"> &rarr;</span>
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ item.description }}</p>
                            </div>
                        </div>
                    </li>
                </ul>
                <!-- <div class="flex mt-4">
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Or start from an empty project
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div> -->
            </div>
        </OverlayPanel>
    </AppLayout>
</template>
