<script setup>
import { ref } from 'vue'

const props = defineProps({
    taskId: {
        type: Number,
        default: 0,
    },
    color: {
        type: String,
        default: '7F9CF5',
    },
    background: {
        type: String,
        default: 'EBF4FF',
    },
    class: {
        type: String,
        default: 'inline-block h-8 w-8 rounded-full ring-1 ring-blue-800 hover:z-20 hover:scale-125',
    }
});
const assignments = ref([]);

axios.get(route('get.assignments.task', props.taskId)).then(response => {
    assignments.value = response.data.assignments;
})

</script>

<template>
    <div class="flex -space-x-2 overflow-hidden p-1 " v-if="assignments.length > 0">
        <img v-for="assignment in assignments"
            v-tooltip.top="{ value: `<h4 class='text-blue-800 text-center bg-blue-200'>` + (assignment.name) + ` : `+(assignment.units/100)+ ` UND</h4>`, escape: true, class: 'custom-error' }"
            :class="class" class=""
            :src="'https://ui-avatars.com/api/?name=' + assignment.name + '&color=' + props.color + '&background=' + props.background"
            alt="imagen">
    </div>
    <div v-else class="text-red-600 font-bold text-sm text-center uppercase italic animate-pulse">
        Sin asignaciones
    </div>
</template>

<style>
.custom-error .p-tooltip-text {
    --tw-bg-opacity: 1;
    background-color: rgb(191 219 254 / var(--tw-bg-opacity));
    color: rgb(255, 255, 255);
}

.custom-error .p-tooltip-right .p-tooltip-arrow {
    --tw-bg-opacity: 1;
    border-right-color: rgb(191 219 254 / var(--tw-bg-opacity));
}
</style>

