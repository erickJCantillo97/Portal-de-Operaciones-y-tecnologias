<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted } from "vue";
// import axios from "axios";
// import { usePermissions } from "@/composable/permission";
import { Link, router } from "@inertiajs/vue3";
// const { hasRole } = usePermissions();
import UserHeader from "@/Components/sections/UserHeader.vue";
// import ProjectCardMinimal from "@/Components/ProjectCardMinimal.vue";
// import DataTable from "primevue/datatable";
// import { FilterMatchMode, FilterOperator } from "primevue/api";
// import Tag from "primevue/tag";
// import { MagnifyingGlassIcon, PencilIcon, TrashIcon } from "@heroicons/vue/20/solid";

import DataChart from "./DataChart.vue";
// import OurTeam from "@/Components/OurTeam.vue";
import "../../sass/dataTableCustomized.scss";
import CustomDataTable from "@/Components/CustomDataTable.vue";
import Estimaciones from "./Dashboards/Estimaciones.vue";
import Proyectos from "./Dashboards/Proyectos.vue";

// import TimeLine from './TimeLine.vue';


const props = defineProps({
    projects: Array,
    contracts: Array,
    costoMes: Number,
});

// const colors = {
//     GEDIN: "bg-blue-500",
//     VPEXE: "bg-gray-500",
//     GEMAM: "bg-teal-500",
//     "VPT&O": "bg-yellow-500",
//     GEBOC: "bg-cyan-500",
//     GECTI: "bg-indigo-500",
//     GETHU: "bg-red-500",
//     PCTMAR: "bg-purple-500",
//     GEFAD: "bg-sky-500",
//     GECON: "bg-pink-500",
// };
// const personal = ref([]);
// const totalMembers = ref(0);
// const loading = ref(false);
// const tasks = ref([]);
// const filters = ref({
//     global: { value: null, matchMode: FilterMatchMode.CONTAINS },
// });
// const chartOptions = ref();
onMounted(() => {
    // getTask();
}
);

// const getTask = () => {
//     axios.get(route("actividadesDeultimonivel")).then((res) => {
//         tasks.value = res.data;
//     });
// };


// const broadcastChannel = () => {
//     setTimeout(() => {
//         window.Echo.private('testing')
//             .listen('.MyWebSocket', (e) => {
//                 alert(e.data);
//             })
//     }, 200);
// }

//#endregion
</script>

<template>
    <AppLayout>
        <div class="overflow-y-scroll">

            <div class="flex justify-between">
                <UserHeader />
            </div>
            <Estimaciones />
            <Proyectos />
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
