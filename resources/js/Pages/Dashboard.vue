<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { usePermissions } from "@/composable/permission";
import { Link, router } from "@inertiajs/vue3";
const { hasRole } = usePermissions();
import UserHeader from "@/Components/sections/UserHeader.vue";
import ProjectCardMinimal from "@/Components/ProjectCardMinimal.vue";
import DataTable from "primevue/datatable";
import { FilterMatchMode, FilterOperator } from "primevue/api";
import Column from "primevue/column";
import Tag from "primevue/tag";
import { MagnifyingGlassIcon, PencilIcon, TrashIcon } from "@heroicons/vue/20/solid";
import Button from "@/Components/Button.vue";
import ProgressBar from "primevue/progressbar";
import DataChart from "./DataChart.vue";
import "../../sass/dataTableCustomized.scss";
// import TimeLine from './TimeLine.vue';

const props = defineProps({
    projects: Array,
    contracts: Array,
    costoMes: Number,
});

const colors = {
    GEDIN: "bg-blue-500",
    VPEXE: "bg-gray-500",
    GEMAM: "bg-teal-500",
    "VPT&O": "bg-yellow-500",
    GEBOC: "bg-cyan-500",
    GECTI: "bg-indigo-500",
    GETHU: "bg-red-500",
    PCTMAR: "bg-purple-500",
    GEFAD: "bg-sky-500",
    GECON: "bg-pink-500",
};

const personal = ref([]);
const totalMembers = ref(0);
const loading = ref(false);
const tasks = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

onMounted(() => {
    initFilters();
    // axios.get(route('get.empleados.gerencia')).then((res) => {
    //     for (var gerencia of Object.values(res.data)) {
    //         personal.value.push({
    //             title: hasRole('Super Admin') ? gerencia[0].Gerencia : gerencia[0].Oficina,
    //             initials: gerencia.length,
    //             totalMembers: gerencia.length,
    //             bgColorClass: gerencia[0].Gerencia != 'GECON' ? colors[gerencia[0].Gerencia] : 'bg-' + gerencia[0].Gerencia,
    //         },)
    //         totalMembers.value += gerencia.length
    //     }
    // })
    // getTask();
});

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString("es-CO", {
        style: "currency",
        currency: "COP",
    });
};

const getTask = () => {
    axios.get(route("actividadesDeultimonivel")).then((res) => {
        tasks.value = res.data;
    });
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: {
            operator: FilterOperator.AND,
            constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }],
        },
    };
};

const clearFilter = () => {
    initFilters();
};

// const broadcastChannel = () => {
//     setTimeout(() => {
//         window.Echo.private('testing')
//             .listen('.MyWebSocket', (e) => {
//                 alert(e.data);
//             })
//     }, 200);
// }
</script>

<template>
    <AppLayout>
        <div class="max-h-screen space-y-5 overflow-y-scroll sm:p-6">
            <!-- <div class="max-w-full p-3 border-2 border-blue-100 rounded-xl">
                            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                                <ProjectCard v-for="project of projects" :project="project" :activo="false" />
                            </dl>
                        </div> -->
            <UserHeader></UserHeader>
            <!--DATATABLE PROYECTOS-->
            <div class="p-3 m-1 shadow-md rounded-xl">
                <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="projects" v-model:filters="filters"
                    dataKey="id" filterDisplay="menu" :loading="loading" :globalFilterFields="[
                        'name',
                        'gerencia',
                        'start_date',
                        'end_date',
                        'hoursPerDay',
                        'daysPerWeek',
                        'daysPerMonth',
                    ]" currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">
                    <!--COLUMNAS-->
                    <Column field="name" header="Proyecto">
                        <template #body="slotProps">
                            <ProjectCardMinimal :project="slotProps.data" :activo="false" :menu="false" :avance="false" />
                        </template>
                    </Column>
                    <Column field="avance" header="Ejecución">
                        <template #body="slotProps">
                            <ProgressBar class="m-1" :value="parseInt(slotProps.data.avance)">
                            </ProgressBar>
                            <p class="text-center">
                                Avance actual: {{ parseInt(slotProps.data.avance) }}%
                            </p>
                        </template>
                    </Column>
                    <Column field="contrato" header="Contrato"></Column>
                    <Column field="costo" header="Valor venta">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.costo) }}
                        </template>
                    </Column>
                    <Column field="fechaF" header="Fin producción"></Column>

                    <!--ACCIONES-->
                    <Column header="Acciones" class="space-x-3">
                        <template #body="slotProps">
                            <div
                                class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8">
                                <div title="Ver programacion">
                                    <Button severity="primary" @click="
                                        router.get(route('programming'), { id: slotProps.data.project_id })
                                        " class="hover:bg-primary">
                                        <i class="fa-solid fa-list-check" />
                                    </Button>
                                </div>
                                <div title="Ver cronograma">
                                    <Button severity="success" @click="
                                        router.get(
                                            route('createSchedule.create', slotProps.data.project_id)
                                        )
                                        " class="hover:bg-danger">
                                        <i class="fa-solid fa-chart-gantt" />
                                    </Button>
                                </div>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
            <div class="p-8 m-1 shadow-md rounded-xl">
                <DataChart></DataChart>
            </div>
        </div>
        <!-- <div class="max-w-full p-3 m-3 border-2 border-blue-100 rounded-xl">
            <TimeLine :projects="props.projects"></TimeLine>
        </div>  -->

        <!-- <div class="grid grid-cols-1 gap-2 mb-8 md:grid-cols-2">
            <div class="m-4">
                <div
                    class="w-full p-4 font-extrabold text-center text-black rounded-xl bg-gradient-to-b from-gray-400 to-slate-50">
                    <h2 class="text-xl font-extrabold">Personal</h2>
                </div>

                <div role="list"
                    class="grid h-64 grid-cols-1 gap-2 px-2 my-6 mt-3 overflow-y-auto sm:grid-cols-2 sm:gap-2 lg:grid-cols-2 custom-scroll snap-y snap-proximity">
                    <div v-for="project in personal" :key="project.title"
                        class="relative flex col-span-1 rounded-md shadow-sm snap-center">
                        <div
                            :class="[project.bgColorClass, 'flex w-16 flex-shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white']">
                            {{ project.initials }}</div>
                        <div
                            class="flex items-center justify-between flex-1 truncate bg-white border-t border-b border-r border-gray-200 rounded-r-md">
                            <div class="flex-1 px-4 py-2 text-sm truncate">
                                <Link :href="route('dashboard.gerencias', project.title)"
                                    class="font-medium text-gray-900 hover:text-gray-600" v-if="project.title != null">{{
                                        project.title }}</Link>
                                <p>Personas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-4">
                <div class="w-full p-4 font-extrabold text-center text-black bg-gradient-to-b from-blue-400 to-slate-50">
                    <h2 class="text-xl font-extrabold ">Actividades de Hoy</h2>
                </div>
                <div class="h-64 mt-4 overflow-y-auto custom-scroll snap-y snap-proximity">
                    <ul class="mt-2 space-y-2">
                        <li v-for="task in tasks" :key="task.id"
                            class="flex justify-between w-full p-2 rounded-md shadow-lg">
                            <div class="block">
                                <p>{{ task.name }}</p>
                                <p class="text-xs italic">{{ task.project.name }} - <b> {{ task.executor }}</b></p>
                            </div>
                            <p>{{ task.percentDone }} %</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div> -->
    </AppLayout>
</template>
