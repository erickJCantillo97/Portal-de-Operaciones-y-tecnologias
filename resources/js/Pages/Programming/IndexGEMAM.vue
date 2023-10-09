<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { FilterMatchMode } from 'primevue/api';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import '../../../sass/dataTableCustomized.scss';
import { Container, Draggable } from "vue-dndrop";
import { applyDrag } from "@/composable/helpers.js";
import { XMarkIcon, PencilIcon } from "@heroicons/vue/20/solid";
import { useSweetalert } from '@/composable/sweetAlert';
import Knob from 'primevue/knob';
const { toast } = useSweetalert();
//#region Draggable
const listaDatos = ref({})

const personal = ref()

const onDrop = (collection, dropResult) => {
    listaDatos.value[collection] = applyDrag(listaDatos.value[collection], dropResult);
}

const getChildPayload = (index) => {
    return personal.value[index];
}

//#endregion

const open = ref(true)

const unidad = {
    day: 'Dias',
    hour: 'Horas'
};

const projects = ref()

onMounted(() => {
    getTask('today')
    axios.get(route('get.personal')).then((res) => {
        personal.value = Object.values(res.data.personal)
    })
})

const dates = ref([]);
const tasks = ref([]);
const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'project.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
    'project.id': { value: null, matchMode: FilterMatchMode.EQUALS },
    'manager': { value: null, matchMode: FilterMatchMode.CONTAINS },
    'tasks': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const optionValue = ref('today')

function obtenerLunesYViernesSemanaActual() {
    const hoy = new Date();
    const diaSemana = hoy.getDay(); // 0 para domingo, 1 para lunes, ..., 6 para sábado

    // Calcula la fecha del lunes de la semana actual
    const lunes = new Date(hoy);
    lunes.setDate(hoy.getDate() - diaSemana + (diaSemana === 0 ? -6 : 1));

    // Calcula la fecha del viernes de la semana actual
    const viernes = new Date(lunes);
    viernes.setDate(lunes.getDate() + 4);


    return {
        lunes: lunes,
        viernes: viernes
    };
}

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

        case 'week':
            const week = new Date();
            const fechasSemanaActual = obtenerLunesYViernesSemanaActual();
            week.setDate(week.getDate() + 7);
            dates.value[0] = fechasSemanaActual.lunes;
            dates.value[1] = fechasSemanaActual.viernes;
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

const redondear = (value) => {
    return new Intl.NumberFormat().format(Number(value).toFixed(2))
}

const filterProject = (id) => {
    filters.value['project.id'].value = id;
}

const clearFilter = () => {
    filters();
};

//#region
const quitar = (task, index, person) => {
    listaDatos.value[task.id].splice(index, 1);
    toast('Se ha eliminado a ' + person.Nombres_Apellidos + ' de la tarea ' + task.name, 'success');
}
const editar = () => {

}
//#endregion
</script>

<template>
    <AppLayout>
        <div class="w-full h-screen md:p-4 px-auto">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Programación de Actividades
                    </h1>
                </div>
            </div>
            <!--LISTA PROGRAMACIÓN DE ACTIVIDADES-->
            <div class="flex h-full gap-2">
                <div class="space-y-1 overflow-auto rounded-md shadow-lg custom-scroll snap-y snap-proximity">
                    <div v-for="task in tasks"
                        class="flex flex-col justify-between p-2 border rounded-md shadow-md h-1/2 snap-start">
                        <div class="flex flex-col justify-between h-1/3">
                            <p class="block w-full overflow-hidden">{{ task.name }}
                            </p>
                            <div class="grid items-center grid-cols-2 text-xs sm:grid-cols-6">
                                <div class="">
                                    <div class="grid grid-cols-2">
                                        <p class="font-bold ">Inicio:</p>
                                        <p class="">{{ task.startDate }}
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-bold">Fin:</p>
                                        <p class="">{{ task.endDate }}
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
                            class="p-2 mt-2 mb-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 h-2/3 shadow-primary custom-scroll"
                            @drop="onDrop(task.id, $event)">
                            <div class="grid grid-cols-2 gap-1"
                                v-if="listaDatos[task.id] !== undefined && listaDatos[task.id].length != 0">
                                <div v-for="(item, index) in listaDatos[task.id]" class="p-1 mt-1 border-2 rounded-md">
                                    <div class="flex items-center justify-between w-full">
                                        <p class="text-sm font-semibold ">{{ item.Nombres_Apellidos }}</p>
                                        <button v-tooltip.top="'Quitar'" @click="quitar(task, index, item)">
                                            <XMarkIcon
                                                class="h-4 p-0.5 border rounded-md text-white bg-danger border-danger hover:animate-pulse hover:scale-125" />
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between w-full align-middle">
                                        <div class="flex space-x-2">
                                            <span
                                                class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                <svg class="h-1.5 w-1.5 fill-green-500" viewBox="0 0 6 6"
                                                    aria-hidden="true">
                                                    <circle cx="3" cy="3" r="3" />
                                                </svg>
                                                7:00 - 12:30
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                <svg class="h-1.5 w-1.5 fill-green-500" viewBox="0 0 6 6"
                                                    aria-hidden="true">
                                                    <circle cx="3" cy="3" r="3" />
                                                </svg>
                                                13:30 - 16:30
                                            </span>
                                        </div>
                                        <button v-tooltip.bottom="'En desarrollo'" @click="console.log()">
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
                    class="w-2/3 h-full overflow-y-auto bg-white divide-y divide-gray-100 shadow-lg custom-scroll ring-1 ring-gray-900/5 sm:rounded-xl">
                    <h2 class="font-semibold leading-6 text-center capitalize text-primary">Personal</h2>
                    <Container
                        class="relative flex flex-col h-full px-1 py-1 overflow-y-auto gap custom-scroll gap-x-2 sm:px-1"
                        behaviour="copy" group-name="1" :get-child-payload="getChildPayload">
                        <Draggable v-for="item in personal"
                            class="relative flex justify-between py-2 pl-2 mt-2 shadow-md cursor-pointer sm:rounded-xl hover:bg-blue-200">
                            <div class="flex min-w-0">
                                <!-- <img class="flex-none w-12 h-12 rounded-full bg-gray-50" :src="item.imageUrl"
                                        alt="profile-photo" /> -->
                                <div class="flex-auto min-w-0">
                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                        {{ item.Nombres_Apellidos }}
                                    </p>
                                    <p class="flex mt-1 text-xs leading-5 text-gray-500">
                                        {{ item.Cargo }}
                                    </p>
                                </div>
                            </div>
                            <!-- <img class="flex-none w-12 h-12 rounded-full bg-gray-50" alt="profile-photo" />
                                    <div class="flex-none w-full h-12 bg-gray-100 rounded-lg">
                                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ item.data }}</p>
                                    </div> -->
                        </Draggable>
                    </Container>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
