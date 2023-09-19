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
        <div class="mt-4 px-4 sm:px-6 py-2">
            <div>
                <h3 class="font-medium text-primary text-xl">Proyectos</h3>
                <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="item in projects" :key="item.id" class="relative overflow-hidden rounded-lg bg-white px-2 pb-8 pt-1 shadow">
                        <dt>
                            <div class="absolute rounded-md">
                            <!-- <Image  alt="Image">
                                <template #image>
                                    <img class="h-20 w-20 rounded-full" :src="item.file" />
                                </template>
                            </Image> -->
                            </div>
                            <p class="text-sm text-center font-medium text-gray-500">{{ item.name }}</p>
                        </dt>
                        <dd class="text-center mt-4 flex items-baseline pb-6 sm:pb-7">
                            <p class="text-2xl text-center font-semibold text-gray-900">{{ item.avance }} %</p>

                            <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4">
                                <div class="text-sm">
                                    <Link :href="route('createSchedule.create', item.project_id)" class="font-medium text-indigo-600 hover:text-indigo-500">Cronograma</Link>
                                </div>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-8">
            <div class="m-4">
                <div class="bg-gradient-to-b from-gray-400 to-slate-50 text-black font-extrabold  w-full p-4 text-center">
                    <h2 class="text-xl font-extrabold">Personal</h2>
                </div>
                <!-- <div class="flex justify-between">
                    <h2 class="font-medium text-primary text-xl">Personal</h2>
                    <h2 class="font-medium text-primary text-xl">Total: {{totalMembers}}</h2>
                </div> -->

                <div role="list" class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-2 lg:grid-cols-2 h-64 overflow-y-auto custom-scroll snap-y snap-proximity px-2 my-6">
                    <div  v-for="project in personal" :key="project.title" class="relative col-span-1 flex rounded-md snap-center shadow-sm">
                        <div :class="[project.bgColorClass, 'flex w-16 flex-shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white']">{{ project.initials }}</div>
                        <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
                            <div class="flex-1 truncate px-4 py-2 text-sm">
                                <Link :href="route('dashboard.gerencias', project.title)" class="font-medium text-gray-900 hover:text-gray-600" v-if="project.title != null">{{ project.title }}</Link><p>Personas</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="m-4">
                <div class="bg-gradient-to-b from-blue-400 to-slate-50 text-black font-extrabold w-full p-4 text-center">
                    <h2 class="text-xl font-extrabold ">Actividades de Hoy</h2>
                </div>
                <div class="mt-4 h-64 overflow-y-auto custom-scroll snap-y snap-proximity">
                <ul class="space-y-2 mt-2">
                    <li v-for="task in tasks" :key="task.id" class="shadow-lg p-2 flex justify-between rounded-md w-full">
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
