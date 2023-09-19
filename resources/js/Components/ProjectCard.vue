<script setup>
import { ref } from 'vue'

const props = defineProps({
    projectId: {
        type: Number
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
        default: 'hover:z-20 hover:scale-110',
    }
});
const project = ref([]);

axios.get(route('projects.show', props.projectId)).then(response => {
    project.value = response.data;
    console.log(project.value)
})

</script>

<template>
    <div class="duration-500 w-max hover:scale-105">
        <div class="flex items-center space-x-3 bg-white rounded-lg shadow-md shadow-indigo-50">
            <img :src=project.file
                class=" w-16 h-16  rounded-full shadow-2xl shadow-[#304FFE] border-white  border-dashed border-2  flex justify-center items-center ">
            <div>
                <h2 class="text-sm font-bold text-gray-900">{{ project.name }}</h2>
                <h3 class="mt-2 text-sm font-bold text-left text-indigo-500">{{project.avance}}</h3>
                <p class="text-sm font-semibold text-gray-400">Last Transaction</p>
                <!-- <button
                    class="px-4 py-2 mt-6 text-sm tracking-wider text-white bg-indigo-400 rounded-lg outline-none hover:bg-indigo-500">Add
                    to cart</button> -->
            </div>
        </div>

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

