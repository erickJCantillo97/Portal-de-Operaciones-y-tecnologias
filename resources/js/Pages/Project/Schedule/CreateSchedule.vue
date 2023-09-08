<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { CheckIcon } from '@heroicons/vue/24/solid'
import Button from '@/Components/Button.vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'

const grupo = ref();
const system = ref();
const systems = ref([])

const subsystem = ref();
const subSystems = ref([])

const processes = ref([])
const visibleModal = ref(false)

const props = defineProps({
    groups: Array,
})

const getSystems = (g) => {
    grupo.value = g.name
    systems.value = []
    processes.value = [];
    subSystems.value = [];
    axios.get(route('system.index', { grupo: g.name })).then((res) => {
        systems.value = res.data[0];
    });
}

const getSubSystems = (s) => {
    system.value = s;
    subSystems.value = [];
    processes.value = [];
    axios.get(route('subsystem.index', { system: s.id })).then((res) => {
        subSystems.value = res.data[0];
    });
}

const getProcess = (s) => {
    subsystem.value = s;
    processes.value = [];
    axios.get(route('process.index', { subSystem: s.id })).then((res) => {
        processes.value = res.data[0];
    });
}

const openModal = () => {
    visibleModal.value = true;

}

</script>

<template>
    <AppLayout>
        <div class="w-full flex justify-between px-4 py-1">
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
                    class=" p-2 flex flex-1 items-center justify-between shadow-md hover:scale-105 hover:bg-sky-100 cursor-pointer rounded-md text-xs "
                    :class="(system != null && s.name == system.name) ? 'bg-sky-100' : 'bg-white'"
                    @click="getSubSystems(s)">
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
                    :class="s.name == grupo ? 'bg-sky-100' : ''" @click="getProcess(s)">
                    <div>{{ s.code }}. {{ s.name }}</div>
                </div>
            </div>
        </div>
        <div class="p-2" v-if="processes.length > 0">
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300" />
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-base font-semibold leading-6 text-gray-900">Proceso</span>
                </div>
            </div>
            <div class="mt-3  grid grid-cols-2 gap-5 sm:grid-cols-4 sm:gap-4 lg:grid-cols-4">
                <div v-for="s of processes"
                    class="text-xs p-2 flex flex-1 items-center justify-between shadow-md hover:scale-105 hover:bg-sky-100 cursor-pointer rounded-md  bg-white"
                    :class="s.name == grupo ? 'bg-sky-100' : ''" @click="openModal(s)">
                    <div>{{ s.name }}</div>
                </div>
            </div>
        </div>

        <!--MODAL DE FORMULARIO-->
        <TransitionRoot as="template" :visible="visibleModal = true">
            <Dialog as="div" class="relative z-30" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                    leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 z-30 w-screen h-screen transition-opacity bg-gray-500 bg-opacity-75" />
                </TransitionChild>

                <div class="fixed inset-0 z-50 h-screen overflow-y-auto">
                    <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel :class="props.heigthDialog"
                                class="relative px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg ">
                                <div>
                                    <div class="px-2 mt-2 text-center">
                                        <DialogTitle as="h3" class="text-xl font-semibold text-primary ">{{ formData.id !=
                                            0 ? 'Editar ' : 'Crear' }} Proyecto
                                        </DialogTitle>


                                        <TextInput v-for="s of processes" class="mt-2 text-left" label="Dias por Mes"
                                            :placeholder="'Escriba Numero de Horas por Dia'" v-model="formData.daysPerMonth"
                                            :error="router.page.props.errors.daysPerMonth"></TextInput>
                                    </div>
                                </div>
                                <div class="flex px-2 mt-2 space-x-4">
                                    <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                        @click="open = false">Cancelar</Button>
                                    <Button severity="success" :loading="false"
                                        class="text-success hover:bg-success border-success" @click="submit()">
                                        {{ formData.id != 0 ? 'Actualizar ' : 'Guardar' }}
                                    </Button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AppLayout>
</template>
