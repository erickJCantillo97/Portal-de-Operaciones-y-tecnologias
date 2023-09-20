<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { usePermissions } from '@/composable/permission';
import { Link } from '@inertiajs/vue3';
const { hasRole } = usePermissions();
import Image from 'primevue/image';
import UserHeader from '@/Components/sections/UserHeader.vue';

const props = defineProps({
    projects: Array,
    costoMes: Number,
})
const colors = { GEDIN: 'bg-blue-500', VPEXE: 'bg-gray-500', GEMAM: 'bg-teal-500', 'VPT&O': 'bg-yellow-500', GEBOC: 'bg-cyan-500', GECTI: 'bg-indigo-500', GETHU: 'bg-red-500', PCTMAR: 'bg-purple-500', GEFAD: 'bg-sky-500', GECON: 'bg-pink-500' }


const personal = ref([])
const totalMembers = ref(0)
const tasks = ref([]);

onMounted(() => {
    axios.get(route('get.empleados.gerencia')).then((res) => {
        for (var gerencia of Object.values(res.data)) {
            personal.value.push({
                title: hasRole('Super Admin') ? gerencia[0].Gerencia : gerencia[0].Oficina,
                initials: gerencia.length,
                totalMembers: gerencia.length,
                bgColorClass: gerencia[0].Gerencia != 'GECON' ? colors[gerencia[0].Gerencia] : 'bg-' + gerencia[0].Gerencia,
            }, )
            totalMembers.value += gerencia.length
        }
    })
    getTask();
})

const getTask = () => {
    axios.get(route('actividadesDeultimonivel')).then((res) => {
        tasks.value = res.data
    })
}
</script>

<template>
    <AppLayout>
        <UserHeader></UserHeader>
        <div class="px-4 py-2 mt-4 sm:px-6">
            <div>
                <h3 class="text-xl font-medium text-primary">Proyectos</h3>
                <dl class="grid grid-cols-1 gap-5 mt-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="item in projects" :key="item.id" class="relative px-2 pt-1 pb-8 overflow-hidden bg-white rounded-lg shadow">
                        <dt>
                            <div class="absolute rounded-md">
                            <!-- <Image  alt="Image">
                                <template #image>
                                    <img class="w-20 h-20 rounded-full" :src="item.file" />
                                </template>
                            </Image> -->
                            </div>
                            <p class="text-sm font-medium text-center text-gray-500">{{ item.name }}</p>
                        </dt>
                        <dd class="flex items-baseline pb-6 mt-4 text-center sm:pb-7">
                            <p class="text-2xl font-semibold text-center text-gray-900">{{ item.avance }} %</p>

                            <div class="absolute inset-x-0 bottom-0 px-4 py-4 bg-gray-50">
                                <div class="text-sm">
                                    <Link :href="route('createSchedule.create', item.project_id)" class="font-medium text-indigo-600 hover:text-indigo-500">Cronograma</Link>
                                </div>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-2 mb-8 md:grid-cols-2">
            <div class="m-4">
                <div class="w-full p-4 font-extrabold text-center text-black bg-gradient-to-b from-gray-400 to-slate-50">
                    <h2 class="text-xl font-extrabold">Personal</h2>
                </div>
                <!-- <div class="flex justify-between">
                    <h2 class="text-xl font-medium text-primary">Personal</h2>
                    <h2 class="text-xl font-medium text-primary">Total: {{totalMembers}}</h2>
                </div> -->

                <div role="list" class="grid h-64 grid-cols-1 gap-2 px-2 my-6 mt-3 overflow-y-auto sm:grid-cols-2 sm:gap-2 lg:grid-cols-2 custom-scroll snap-y snap-proximity">
                    <div  v-for="project in personal" :key="project.title" class="relative flex col-span-1 rounded-md shadow-sm snap-center">
                        <div :class="[project.bgColorClass, 'flex w-16 flex-shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white']">{{ project.initials }}</div>
                        <div class="flex items-center justify-between flex-1 truncate bg-white border-t border-b border-r border-gray-200 rounded-r-md">
                            <div class="flex-1 px-4 py-2 text-sm truncate">
                                <Link :href="route('dashboard.gerencias', project.title)" class="font-medium text-gray-900 hover:text-gray-600" v-if="project.title != null">{{ project.title }}</Link><p>Personas</p>
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
                    <li v-for="task in tasks" :key="task.id" class="flex justify-between w-full p-2 rounded-md shadow-lg">
                        <div class="block">
                            <p>{{ task.name }}</p>
                            <p class="text-xs italic">{{task.project.name}} - <b>  {{task.executor}}</b></p>
                        </div>
                        <p>{{ task.percentDone }} %</p>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </AppLayout>
</template>
