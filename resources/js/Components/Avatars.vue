<script setup>
import Avatar from 'primevue/avatar';
import AvatarGroup from 'primevue/avatargroup';
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
        default: 'hover:z-20 hover:scale-110 duration-500',
    }
});
const assignments = ref([]);

axios.get(route('get.assignments.task', props.taskId)).then(response => {
    assignments.value = response.data.assignments;
})

const letraAvatar = (cadena) => {
    var palabras = cadena.split(" ");
    var resultado = "";
    if (palabras.length > 2) {
        for (var i = 0; i < 2; i++) {
            resultado += palabras[i].substring(0, 1);
        }
    }else{
    for (var i = 0; i < palabras.length; i++) {
        resultado += palabras[i].substring(0, 1);
    }}
    return resultado.toUpperCase();
}

</script>

<template>
    <AvatarGroup v-if="assignments.length > 0">
        <!-- <Avatar v-for="assignment in assignments"
            v-tooltip.top="{ value: `<h4 class='text-center text-blue-800 bg-blue-200'>` + (assignment.name) + ` : `+(assignment.units/100)+ ` UND</h4>`, escape: true, class: 'custom-error' }"
            :class="class" size="large" shape="circle"
            :image="'https://ui-avatars.com/api/?name=' + assignment.name + '&color=' + props.color + '&background=' + props.background"
            alt="imagen"></Avatar> -->
        <Avatar v-for="assignment in assignments"
            v-tooltip.top="{ value: `<h4 class='text-center text-blue-800 bg-blue-200'>` + (assignment.name) + ` : ` + (assignment.units / 100) + ` UND</h4>`, escape: true, class: 'custom-error' }"
            :class="class" style="background-color:#2196F3; color: #ffffff" shape="circle" :label="letraAvatar(assignment.name)" alt="imagen"></Avatar>
    </AvatarGroup>
    <div v-else class="text-sm italic font-bold text-center text-red-600 uppercase animate-pulse">
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

