<script setup>

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

</script>

<template>
    <div :class="clases"
        class="flex items-center rounded-md text-gray-90 h-14 min-w-max">
        <img :src="project.file" onerror="this.src='/images/generic-boat.png'" class="h-0 mr-1 rounded-lg sm:h-12 sm:w-16" />
        <div class="flex-col">
            <p class="text-xs font-bold">{{ project.name }}</p>
            <div class="flex space-x-1">
                <p class="text-xs italic">Inicio:</p>
                <p class="text-xs italic"> {{ project.fechaI }}</p>
            </div>
        </div>
    </div>
</template>

