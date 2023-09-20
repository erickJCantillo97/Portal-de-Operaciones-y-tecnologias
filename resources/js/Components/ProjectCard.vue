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
        default: '',
    },
    avance: {
        type: Boolean,
        default: true,
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
    <div v-if="cagando" :class="class" class="flex w-40 hover:z-20 hover:scale-125">
        <Skeleton shape="circle" size="2rem" class="mr-2"></Skeleton>
        <div class="align-self-center" style="flex: 1">
            <Skeleton width="100%" class="mb-1"></Skeleton>
            <Skeleton width="100%"></Skeleton>
        </div>
    </div>
    <div v-else :class="class" class="flex w-40 h-10 space-x-3 duration-500 hover:z-20 hover:scale-125">
        <img :src=project.file
            class="w-8 h-8 rounded-full ">
        <div class="items-center w-full text-center align-middle">
            <h2 class="text-xs font-bold text-gray-900">{{ project.name }}</h2>
            <ProgressBar v-if="avance"
                v-tooltip.top="{ value: `<h4 class='text-center text-blue-800 bg-blue-200'> Avance: ` + (parseInt(project.avance)) + `%</h4>`, escape: true, class: 'custom-error' }"
                :value="parseInt(project.avance)"></ProgressBar>
        </div>
    </div>
</template>

<style></style>

