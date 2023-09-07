<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { CheckIcon } from '@heroicons/vue/24/solid'
import Button from '@/Components/Button.vue';
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

const systems = ref([])
const grupo = ref();
const system = ref();
const subSystems = ref([])
const props = defineProps({
    groups: Array,
})

const getSystems = (g) => {
    grupo.value = g.name
    systems.value = []
    subSystems.value = [];
    axios.get(route('system.index', { grupo: g.name })).then((res) => {
        systems.value = res.data[0];
    });
}

const getSubSystems = (s) => {
    system.value = s;
    subSystems.value = [];
    axios.get(route('subsystem.index', { system: s.id })).then((res) => {
        subSystems.value = res.data[0];
    });
}

</script>

<template>
    <AppLayout>
        <div class="w-full flex justify-between p-4">
            <div></div>
            <h2 class="text-xl font-bold  text-center text-primary">Crear un Cronograma</h2>
            <div class="justify-end">
                <Button severity="success">
                    Vista previa
                </Button>
            </div>
        </div>
        <div class="p-2">
            <h2 class="text-sm font-medium text-gray-500">Seleccione un Grupo Constructivo</h2>
            <ul role="list" class="mt-3 grid grid-cols-2 gap-5 sm:grid-cols-4 sm:gap-4 lg:grid-cols-10">
                <li v-for="g in groups" :key="g.name" class="col-span-1 flex rounded-md shadow-sm">
                    <div class="flex flex-1 items-center justify-between shadow-md hover:scale-110 hover:bg-sky-100 cursor-pointer rounded-md  "
                        :class="g.name == grupo ? 'bg-sky-100' : 'bg-white'" @click="getSystems(g)">
                        <div class="flex-1 truncate p-2 text-sm">
                            <a class="font-medium  hover:text-gray-600 text-primary">{{ g.name }}</a>
                        </div>
                    </div>
                </li>
            </ul>

        </div>
        <div class="p-2" v-if="systems.length > 0">
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300" />
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-base font-semibold leading-6 text-gray-900">Sistema</span>
                </div>
            </div>
            <div class="mt-3  grid grid-cols-2 gap-5 sm:grid-cols-4 sm:gap-4 lg:grid-cols-4">
                <div v-for="s of systems"
                    class=" p-2 flex flex-1 items-center justify-between shadow-md hover:scale-105 hover:bg-sky-100 cursor-pointer rounded-md text-xs bg-white"
                    :class="s.name == grupo ? 'bg-sky-100' : ''" @click="getSubSystems(s)">
                    <div>{{ s.code }}. {{ s.name }}</div>
                </div>
            </div>
        </div>
        <div class="p-2" v-if="subSystems.length > 0">
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300" />
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-base font-semibold leading-6 text-gray-900">Sub Sistema</span>
                </div>
            </div>
            <div class="mt-3  grid grid-cols-2 gap-5 sm:grid-cols-4 sm:gap-4 lg:grid-cols-4">
                <div v-for="s of subSystems"
                    class="text-xs p-2 flex flex-1 items-center justify-between shadow-md hover:scale-105 hover:bg-sky-100 cursor-pointer rounded-md  bg-white"
                    :class="s.name == grupo ? 'bg-sky-100' : ''">
                    <div>{{ s.code }}. {{ s.name }}</div>
                </div>
            </div>
    </div>
</AppLayout></template>
