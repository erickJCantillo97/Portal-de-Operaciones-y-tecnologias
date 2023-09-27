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
            parametter: (props.project.id ? props.project.id : props.projectId)
        }
    },
    {
        label: 'Ver programacion',
        icon: 'fa-solid fa-list-check',
        url: {
            name: 'programming',
            parametter: (props.project.id ? props.project.id : props.projectId)
        }
    },
    {
        label: 'Ver detalles',
        icon: 'fa-solid fa-diagram-project',
        url: {
            name: 'createSchedule.create',
            parametter: (props.project.id ? props.project.id : props.projectId)
        }
    },
]);

</script>

<template>
    <!-- <div v-if="cagando" :class="activo ? 'bg-sky-500 text-white' : 'bg-white hover:bg-sky-200 text-gray-90' + clases"
        class="flex w-60 rounded-md hover:z-20 hover:scale-110">
        <Skeleton shape="circle" size="2rem" class="mr-2"></Skeleton>
        <div class="align-self-center" style="flex: 1">
            <Skeleton width="100%" class="mb-1"></Skeleton>
            <Skeleton width="100%"></Skeleton>
        </div>
    </div> -->
    <div :class="activo ? 'bg-sky-500 text-white' : 'bg-white text-gray-90' + clases"
        class="flex items-center h-14 min-w-max rounded-md">
        <img :src=project.file class="rounded-lg h-0 sm:h-12 sm:w-16 mr-1" />
        <div class="flex-col">
            <p class="text-xs font-bold">{{ project.name }}</p>
            <div class="flex space-x-1">
                <p class="text-xs italic">Inicio:</p>
                <p class="text-xs italic"> {{ project.fechaI }}</p>
            </div>
            <!-- <ProgressBar v-if="avance" class="items-end" :value="parseInt(project.avance)"></ProgressBar> -->
        </div>
        <!-- <div v-if="menu" class="flex w-1/8 justify-center align-middle">
            <MinimalMenu :items="items" :header="true">
                <template #header>
                    <div class="flex relative text-center text-black">
                        <p class="text-xs w-4/5 font-bold">{{ project.name }}</p>
                        <p class="text-xs w-1/5 font-bold">{{ parseInt(project.avance) }}%</p>
                    </div>
                </template>
            </MinimalMenu>
        </div> -->
    </div>
</template>

<style></style>

