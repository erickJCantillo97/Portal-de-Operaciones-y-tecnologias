<template>
    <div class="grid grid-cols-3 gap-6  p-2 ">
        <div class="border-red-500 border h-full shadow-md shadow-danger rounded-lg p-4">
            <div class="w-full flex justify-between">
                <h1 class="font-extrabold text-danger">Tareas Pendientes </h1>
                <h1 class="font-extrabold text-danger">{{ pending.length }} / {{ (pending.length + inProcess.length +
                    done.length)
                    }} </h1>
            </div>
            <draggable v-model="pending" :key="pending" group="people" class="w-full h-full ">
                <template v-slot:item="{ item }">
                    <!-- example -->
                    <div
                        class="border border-danger p-4 my-2 rounded-md flex justify-between items-center space-x-6 cursor-grab">
                        <div>
                            <p class="font-bold text-danger">{{ item.project }}</p>
                            {{ item.title }}
                            <p class="flex text-sm italic">
                                {{ item.start }} - {{ item.end }}
                            </p>
                        </div>
                        <div class=" text-danger text-center items-center flex rounded-full">
                            <i class="pi pi-angle-right"></i>
                        </div>
                    </div>
                    <!-- or your own template -->
                </template>
            </draggable>
        </div>
        <div class="border-primary border h-full shadow-md shadow-primary rounded-lg p-4">
            <h1 class="font-extrabold text-primary">Tareas en Proceso ({{ inProcess.length }})</h1>
            <draggable v-model="inProcess" group="people" class="w-full h-full">
                <template v-slot:item="{ item }">
                    <!-- example -->
                    <div class="border border-primary p-4 my-2 rounded-md flex justify-between items-center space-x-6">
                        <div>
                            <p class="font-bold text-primary">{{ item.project }}</p>
                            {{ item.title }}
                            <p class="flex text-sm italic">
                                {{ item.start }} - {{ item.end }}
                            </p>
                        </div>
                        <div
                            class="border-primary text-primary border p-3 hover:text-white hover:bg-primary text-center items-center flex rounded-full">
                            <i class="pi pi-angle-right"></i>
                        </div>
                    </div>

                    <!-- or your own template -->
                </template>
            </draggable>
        </div>
        <div class="border-success border h-full shadow-md shadow-success rounded-lg p-4">
            <h1 class="font-extrabold text-success">Tareas en Terminadas ({{ done.length }})</h1>
            <draggable v-model="done" group="people" class="w-full h-full">
                <template v-slot:item="{ item }">
                    <!-- example -->
                    <div class="border border-success p-4 my-2 rounded-md flex justify-between items-center space-x-6">
                        <div>
                            <p class="font-bold text-success">{{ item.project }}</p>
                            {{ item.title }}
                            <p class="flex text-sm italic">
                                {{ item.start }} - {{ item.end }}
                            </p>
                        </div>
                        <div
                            class="border-success text-success border p-3 hover:text-white hover:bg-success text-center items-center flex rounded-full">
                            <i class="pi pi-angle-right"></i>
                        </div>
                    </div>

                    <!-- or your own template -->
                </template>
            </draggable>
        </div>
    </div>

</template>
<script setup>
import axios from "axios";
import { ref } from "vue";
import draggable from "vue3-draggable";

const getTaskPendientes = () => {
    axios.get(route('get.times.employees')).then((res) => {
        pending.value = res.data.times
    })
}

getTaskPendientes()

const pending = ref([
])
const inProcess = ref([
])
const done = ref([
])


</script>