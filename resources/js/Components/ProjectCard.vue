<script setup>
import ProgressBar from 'primevue/progressbar';
import Skeleton from 'primevue/skeleton';
import SpeedDial from 'primevue/speeddial';
import { ref } from 'vue'

const props = defineProps({
    projectId: {
        type: Number
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
    }


});
const project = ref([]);
const cagando = ref(true)
axios.get(route('projects.show', props.projectId)).then(response => {
    project.value = response.data[0];
    cagando.value = false;
})

</script>

<template>
    <div v-if="cagando" :class="activo ? 'bg-sky-500 text-white':'bg-white hover:bg-sky-200 text-gray-90' + clases"
    class="flex w-40 rounded-md hover:z-20 hover:scale-110">
        <Skeleton shape="circle" size="2rem" class="mr-2"></Skeleton>
        <div class="align-self-center" style="flex: 1">
            <Skeleton width="100%" class="mb-1"></Skeleton>
            <Skeleton width="100%"></Skeleton>
        </div>
    </div>
    <div v-else :class="activo ? 'bg-sky-500 text-white':'bg-white hover:bg-sky-200 text-gray-90' + clases"
    class="flex items-center h-12 p-1 space-x-3 duration-500 border rounded-md cursor-pointer w-52 hover:z-20 hover:scale-110"
    v-tooltip.top="{ value: `<h4 class='text-center text-blue-800 bg-blue-200'> Avance: ` + (parseInt(project.avance)) + `%</h4>`, escape: true, class: 'custom-error' }">
        <img :src=project.file
            class="w-10 h-10 rounded-full ">
        <div class="flex-col w-full text-center">
            <h2 class="text-xs font-bold">{{ project.name }}</h2>
            <ProgressBar v-if="avance" class="items-end"
                :value="parseInt(project.avance)"></ProgressBar>
        </div>
    </div>
</template>

<style></style>

