<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

const props = defineProps({
    subject: {
        type: String,
        default: ''
    },
    showNoProjects: {
        type: Boolean,
        default: false
    },
    showDescription: {
        type: Boolean,
        default: false
    },
    description: {
        type: String,
        default:
            ''
    },
})
</script>

<template>
    <section v-if="!props.showNoProjects"
        class="h-[30vh] w-full flex flex-col mt-12 justify-center items-center col-span-6 transition duration-300 ease-in">
        <ApplicationLogo class="mb-4" :width-logo="130" :height-logo="130" />
        <h3 class="text-xl font-semibold italic text-gray-500 text-center">
            {{ props.subject }}
        </h3>
        <span v-if="props.description" class="text-sm italic text-gray-500 text-center">
            {{ props.description }}
        </span>
    </section>
</template>
