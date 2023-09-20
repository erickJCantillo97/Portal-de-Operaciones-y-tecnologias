<script setup>
import ProgressBar from 'primevue/progressbar';
import Skeleton from 'primevue/skeleton';
import SpeedDial from 'primevue/speeddial';
import { ref } from 'vue'

const props = defineProps({
    projectId: {
        type: Number
    },
    class: {
        type: String,
        default: 'hover:z-20 hover:scale-125',
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
    <div v-if="cagando" class="flex w-40">
        <Skeleton shape="circle" size="2rem" class="mr-2"></Skeleton>
        <div class="align-self-center" style="flex: 1">
            <Skeleton width="100%" class="mb-1"></Skeleton>
            <Skeleton width="100%"></Skeleton>
        </div>
    </div>
    <div v-else class="w-40 duration-500 hover:scale-105">
        <div class="flex space-x-3 bg-white rounded-lg shadow-md shadow-indigo-50 cursor-pointer">
            <img :src=project.file
                class="flex items-center justify-center w-8 h-8 border-2 border-white border-dashed rounded-full ">
            <div class="items-center w-full text-center">
                <h2 class="text-xs font-bold text-gray-900">{{ project.name }}</h2>
                <ProgressBar

                    v-tooltip.top="{ value: `<h4 class='text-center text-blue-800 bg-blue-200'> Avance: ` + (parseInt(project.avance)) + `%</h4>`, escape: true, class: 'custom-error' }"
                    :value="parseInt(project.avance)"></ProgressBar>
            </div>
        </div>

    </div>
</template>

<style></style>

