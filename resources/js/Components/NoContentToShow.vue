<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { ref } from 'vue'

const showNoProjects = ref(true)
const showDescription = ref(false)

const props = defineProps({
    subject: {
        type: String,
        default: 'Contenido'
    },

    showDescription: {
        type: Boolean,
        default: false
    },

    description: {
        type: String,
        default:
            'Sólo se mostrarán proyectos que existan o estén creados.'
    },
})
</script>

<template>
    <section v-if="showNoProjects"
        class="h-[30vh] w-full flex flex-col mt-12 justify-center items-center col-span-6 transition duration-300 ease-in">
        <ApplicationLogo class="mb-4" :width-logo="130" :height-logo="130" />
        <h3 class="text-xl font-semibold italic text-gray-500 text-center">
            No hay {{ props.subject }} para mostrar
        </h3>
        <span v-if="showDescription" class="text-sm italic text-gray-500 text-center">
            {{ props.description }}
        </span>
    </section>
</template>
