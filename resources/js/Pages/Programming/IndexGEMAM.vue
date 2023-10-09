<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import '../../../sass/dataTableCustomized.scss';
import { Container, Draggable } from "vue-dndrop";
import { applyDrag } from "@/composable/helpers.js";
import { XMarkIcon, PencilIcon, Bars3Icon } from "@heroicons/vue/20/solid";
import { useSweetalert } from '@/composable/sweetAlert';
import Knob from 'primevue/knob';
const { toast } = useSweetalert();
//#region Draggable
const listaDatos = ref({})
const fecha = ref(new Date().toISOString().split("T")[0])
const personal = ref()
const dates = ref([]);
const tasks = ref([]);
const loading = ref(false);
const optionValue = ref('today')
const projects = ref()

const onDrop = async (collection, dropResult) => {
    listaDatos.value[collection] = await applyDrag(listaDatos.value[collection], dropResult, fecha.value, collection);
}

const getChildPayload = (index) => {
    return personal.value[index];
}

//#endregion

onMounted(() => {
    getTask('today')
    axios.get(route('get.personal')).then((res) => {
        personal.value = Object.values(res.data.personal)
    })
})



const getTask = async (option) => {

    const today = new Date();
    optionValue.value = option
    switch (option) {
        case 'today':
            dates.value[0] = today;
            dates.value[1] = today;
            break;
        case 'tomorrow':
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dates.value[0] = tomorrow;
            dates.value[1] = tomorrow;
            break;

        case 'date':
            dates.value[0] = fecha.value;
            dates.value[1] = fecha.value;
            break;
        default:
            break;
    }

    if (dates.value[1] != null) {
        tasks.value = [];
        loading.value = true;

        await axios.get(route('actividadesDeultimonivel', { dates: dates.value })).then((res) => {
            loading.value = false;
            tasks.value = res.data
            projects.value = [...new Set(res.data.map(obj => obj.project.id))]
        })
        tasks.value.forEach(element => {
            listaDatos.value[element.id] = []
        });
    }

}



//#region
const quitar = async (task, index, person) => {
    await axios.post(route('programming.delete'), { task_id: task, employee_id: person.Num_SAP, fecha: fecha }).then((res) => {
        console.log(res)
        listaDatos.value[task.id].splice(index, 1);
        toast('Se ha eliminado a ' + person.Nombres_Apellidos + ' de la tarea ' + task.name, 'success');
    })
}
const editar = () => {

}
//#endregion
</script>

<template>
    <AppLayout>
        <div class="w-full h-screen md:p-4 px-auto">
            <div class="grid justify-center mb-2 grid-col-1 sm:flex sm:justify-between sm:items-center">
                <p class="text-xl font-semibold leading-6 text-center capitalize text-primary">
                    Programación de Actividades
                </p>
                <div class="flex items-center space-x-2">
                    <span class="shadow-md md:block">
                        <button type="button"
                            :class="optionValue == 'today' ? 'bg-primary text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                            @click="getTask('today')"
                            class="relative inline-flex items-center px-3 py-2 text-sm font-semibold alturah8 rounded-l-md 0 ring-1 ring-inset ring-gray-300 focus:z-10">Hoy</button>
                        <button type="button"
                            :class="optionValue == 'tomorrow' ? 'bg-primary text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                            @click="getTask('tomorrow')"
                            class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-semibold alturah8 rounded-r-md ring-1 ring-inset ring-gray-300 focus:z-10">Mañana</button>
                    </span>
                    <p class="font-bold text-primary">Fecha:</p>
                    <input
                        :class="optionValue == 'date' ? 'bg-primary text-white' : 'bg-white hover:bg-sky-200 text-gray-90'"
                        class="inline-flex items-center px-3 py-2 -ml-px text-xs font-semibold rounded-md shadow-md alturah8 text-primary border-primary"
                        type="date" name="date" id="date" v-model="fecha" @change="getTask('date')">
                </div>
            </div>

            <div class="h-[90%] block sm:grid-cols-3 sm:gap-1 sm:grid">
                <!--LISTA PROGRAMACIÓN DE ACTIVIDADES-->
                <div
                    class="h-full col-span-2 space-y-1 overflow-y-auto shadow-lg custom-scroll snap-y snap-proximity ring-1 ring-gray-900/5 rounded-xl">
                    <div v-for="task in tasks"
                        class="flex flex-col justify-between p-2 border rounded-md shadow-md h-1/2 snap-start">
                        <div class="flex flex-col justify-between h-auto">
                            <div class="flex items-start justify-between">
                                <p class="block overflow-hidden">{{ task.name }}
                                </p>
                                <button v-tooltip.top="'Quitar'" @click="" class="block ml-1 sm:hidden">
                                    <Bars3Icon
                                        class="h-6 p-0.5 border rounded-md text-white bg-primary border-primary hover:animate-pulse hover:scale-125" />
                                </button>
                            </div>
                            <div class="grid items-center grid-cols-2 text-xs sm:grid-cols-6">
                                <div class="">
                                    <div class="flex justify-between">
                                        <p class="font-bold ">I:</p>
                                        <p class="font-mono">{{ task.startDate }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between">
                                        <p class="font-bold">F:</p>
                                        <p class="font-mono">{{ task.endDate }}
                                        </p>
                                    </div>
                                </div>
                                <div class="hidden sm:flex sm:justify-center">
                                    <Knob v-tooltip.top="'Avance: ' + parseInt(task.percentDone) + '%'"
                                        :model-value=parseInt(task.percentDone) :size=50 valueTemplate="{value}%"
                                        readonly />
                                </div>
                                <div class="hidden text-center sm:justify-center sm:block">
                                    <p class="font-bold">Valor estimado</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                                <div class="hidden text-center sm:justify-center sm:block">
                                    <p class="font-bold">Valor programado</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                                <div class="hidden text-center sm:justify-center sm:block">
                                    <p class="font-bold">Valor dia</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                                <div class="hidden text-center sm:justify-center sm:block">
                                    <p class="font-bold">Diferencia</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                            </div>
                        </div>
                        <Container group-name="1"
                            class="h-full p-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 shadow-primary custom-scroll"
                            @drop="onDrop(task.id, $event)">
                            <div class="grid grid-cols-2 gap-1"
                                v-if="listaDatos[task.id] !== undefined && listaDatos[task.id].length != 0">
                                <div v-for="(item, index) in listaDatos[task.id]" class="p-1 mt-1 border-2 rounded-md">
                                    <div class="flex items-center justify-between w-full">
                                        <p class="text-sm font-semibold ">{{ item.Nombres_Apellidos }}</p>
                                        <button v-tooltip.top="'En desarrollo'" @click="quitar(task, index, item)">
                                            <XMarkIcon
                                                class="h-4 p-0.5 border rounded-md text-white bg-danger border-danger hover:animate-pulse hover:scale-125" />
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between w-full font-mono align-middle">
                                        <div class="grid grid-cols-3 gap-1">
                                            <span
                                                class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">

                                                7:00 - 12:30
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">

                                                13:30 - 16:30
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">

                                                13:30 - 16:30
                                            </span>
                                        </div>
                                        <button v-tooltip.bottom="'En desarrollo'" @click="console.log('En desarrollo')">
                                            <PencilIcon
                                                class="h-4 p-0.5 border rounded-md bg-primary text-white border-primary hover:animate-pulse hover:scale-125" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="items-center justify-center text-center align-middle opacity-50">
                                <h2 class="mt-4 text-xl font-medium tracking-wide text-gray-700">No hay personas asignadas
                                </h2>

                                <p class="mt-2 tracking-wide text-gray-500">Arrastre una persona de la lista de la izquierda
                                    para agregarla a la actividad </p>
                            </div>
                        </Container>
                    </div>
                </div>

                <!--LISTA PERSONAL-->
                <div
                    class="hidden w-full h-full overflow-y-auto divide-y divide-gray-100 shadow-lg sm:block custom-scroll ring-1 ring-gray-900/5 rounded-xl">
                    <h2 class="font-semibold leading-6 text-center capitalize text-primary">Personal</h2>
                    <Container class="relative flex flex-col h-full px-1 py-1 overflow-y-auto gap custom-scroll sm:px-1"
                        behaviour="copy" group-name="1" :get-child-payload="getChildPayload">
                        <Draggable v-for="item in personal" :drag-not-allowed="false"
                            class="flex justify-between h-auto py-2 pl-2 mt-2 shadow-md cursor-pointer sm:rounded-xl hover:bg-blue-200">
                            <div class="flex items-center align-middle">
                                <img class="w-12 h-12 rounded-full"
                                    :src="'https://ui-avatars.com/api/?name=' + item.Nombres_Apellidos"
                                    alt="profile-photo" />
                                <div class="flex-auto mx-1">
                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                        {{ item.Nombres_Apellidos }}
                                    </p>
                                    <p class="flex mt-1 text-xs leading-5 text-gray-500">
                                        {{ item.Cargo }}
                                    </p>
                                </div>
                                <button
                                    class="flex items-center justify-center h-6 p-1 m-1 font-mono text-sm text-white align-middle rounded-md w-9 bg-primary"
                                    v-tooltip.top="'Ver programacion'" @click="console.log('Hola mundo')">
                                    <p> 1.0H </p>
                                </button>
                            </div>
                        </Draggable>
                    </Container>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
