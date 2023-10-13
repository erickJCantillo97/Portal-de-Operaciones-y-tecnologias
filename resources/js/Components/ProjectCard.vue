<script setup>
import Avatar from 'primevue/avatar';
import ProgressBar from 'primevue/progressbar';
import Skeleton from 'primevue/skeleton';
import MinimalMenu from '@/Components/MinimalMenu.vue';

import { ref } from 'vue'

const props = defineProps({
    projectId: {
        type: Number
    },
    project: {
        default: null,
    },
    clases: {
        type: String,
        default: '',
    },
    avance: {
        type: Boolean,
        default: true,
    },
    activo: {
        type: Boolean,
        default: false,
    },
    menu: {
        type: Boolean,
        default: true,
    }



});
const project = ref({});
const cagando = ref(true)
if (props.project) {
    console.log(props.project)
    project.value = props.project
    cagando.value = false;
} else {
    axios.get(route('projects.show', props.projectId)).then(response => {
        project.value = response.data[0];
        cagando.value = false;
    })
}


const items = ref([
    {
        label: 'Ver cronograma',
        icon: 'fa-solid fa-chart-gantt',
        url: {
            name: 'createSchedule.create',
            parametter: (props.projectId ? props.projectId: props.project.id )
        }
    },
    {
        label: 'Ver programacion',
        icon: 'fa-solid fa-list-check',
        url: {
            name: 'programming',
            parametter: (props.projectId ? props.projectId: props.project.id)
        }
    },
    {
        label: 'Ver detalles',
        icon: 'fa-solid fa-diagram-project',
        url: {
            name: 'createSchedule.create',
            parametter: (props.projectId ? props.projectId: props.project.id)
        }
    },
]);

</script>

<template>
    <div v-if="cagando" :class="activo ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
        class="flex rounded-md w-60 hover:z-20 hover:scale-110">
        <Skeleton shape="circle" size="2rem" class="mr-2"></Skeleton>
        <div class="align-self-center" style="flex: 1">
            <Skeleton width="100%" class="mb-1"></Skeleton>
            <Skeleton width="100%"></Skeleton>
        </div>
    </div>
    <div v-else :class="activo ? 'bg-sky-500 text-white' : 'bg-white text-gray-90'"
        class="flex items-center border rounded-md h-14 w-60 hover:z-20 hover:scale-110">
        <img :src="project.file" onerror="this.src='/images/generic-boat.png'" class="h-0 mr-1 rounded-lg sm:h-12 sm:w-16" />
        <div class="w-3/5 h-full m-1 text-center">
            <p class="m-1 text-xs font-bold">{{ project.name }}</p>
            <!-- <div class="flex space-x-1">
                <p class="text-xs italic">Inicio:</p>
                <p class="text-xs italic"> {{ project.fechaI }}</p>
            </div> -->
            <ProgressBar v-if="avance" class="justify-end h-full" :value="parseInt(project.avance)"></ProgressBar>
        </div>
        <div v-if="menu" class="flex justify-center m-1 w-1/8">
            <MinimalMenu :items="items" :header="true">
                <template #header>
                    <div class="relative flex text-center text-black">
                        <p class="w-4/5 text-xs font-bold">{{ project.name }}</p>
                        <p class="w-1/5 text-xs font-bold">{{ parseInt(project.avance) }}%</p>
                    </div>
                </template>
            </MinimalMenu>
        </div>
    </div>
</template>

<style></style>

