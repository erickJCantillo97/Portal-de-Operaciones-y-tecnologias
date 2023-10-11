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

const onDrop = async (collection, dropResult) => {
    listaDatos.value[collection] = await applyDrag(listaDatos.value[collection], dropResult, fecha.value, collection);
}

const getFoto = (correo) => {
     axios.post(route('get.foto', correo)).then((res) => {
        return res.data.photo
    })

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
        })
        tasks.value.forEach(element => {
            listaDatos.value[element.id] = element.people
        });
    }

}

function format24h(hora) {
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
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
<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    object-position: 50% 30%;
    border-radius: 10% 25%;;
    object-fit: cover; /* Opciones: 'cover', 'contain', 'fill', etc. */
}
</style>

<template>
    <AppLayout>
        <div class="relative h-full w-full p-2">
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
            <div class="relative h-full grid grid-rows-auto sm:grid-rows-1 sm:grid-cols-3 sm:gap-1 ">
                <!--LISTA PROGRAMACIÓN DE ACTIVIDADES-->
                <div
                    class="relative h-[85%] row-start-2 row-span-6 sm:row-start-1 sm:col-start-1 sm:col-span-2 sm:space-y-1 overflow-y-auto shadow-lg custom-scroll snap-y snap-proximity ring-1 ring-gray-900/5 rounded-xl">
                    <div v-for="task in tasks"
                        class="h-1/2 flex flex-col justify-between p-2 border rounded-md shadow-md sm:h-1/2 snap-start">
                        <div class="grid grid-rows-2">
                            <div class="">
                                <p class="block overflow-hidden">{{ task.name }}
                                </p>
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
                        <Container
                            class="h-2/3 p-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 shadow-primary custom-scroll"
                            @drop="onDrop(task.id, $event)">
                            <div class="grid grid-cols-2 gap-1"
                                v-if="listaDatos[task.id] !== undefined && listaDatos[task.id].length != 0">
                                <div v-for="(item, index) in listaDatos[task.id]" class="p-1 mt-1 border-2 rounded-md">
                                    <div class="flex items-center justify-between w-full">
                                        <p class="text-sm font-semibold ">{{ item.employee != undefined ?
                                            item.employee.Nombres_Apellidos : item.Nombres_Apellidos }}</p>
                                        <button v-tooltip.top="'En desarrollo'" @click="quitar(task, index, item)">
                                            <XMarkIcon
                                                class="h-4 p-0.5 border rounded-md text-white bg-danger border-danger hover:animate-pulse hover:scale-125" />
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between w-full font-mono align-middle">
                                        <div class="grid grid-cols-3 gap-1">
                                            <span v-for="horario in item.schedule_times"
                                                class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                {{ format24h(horario.hora_inicio) }}-{{ format24h(horario.hora_fin) }}
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
                    class="row-start-1 sm:col-start-3 h-full overflow-y-hidden sm:overflow-y-auto divide-y divide-gray-100 shadow-lg sm:block custom-scroll ring-1 ring-gray-900/5 rounded-xl">
                    <!-- <h2 class="font-semibold text-center capitalize text-primary">Personal</h2> -->
                    <Container
                        class="flex h-[85%] sm:space-x-0 w-full overflow-x-auto sm:overflow-x-hidden sm:overflow-y-auto sm:block sm:py-1 sm:px-1"
                        behaviour="copy" group-name="1" :get-child-payload="getChildPayload">
                        <Draggable v-for="item in personal" :drag-not-allowed="false"
                            class="py-2 pl-2 shadow-md cursor-pointer sm:rounded-xl hover:bg-blue-200 hover:scale-[102%]">
                            <div class="grid grid-cols-6">
                                <div class="flex items-center w-full">
                                    <img class="custom-image"
                                        :src="item.photo"
                                        alt="profile-photo" />
                                </div>
                                <div class="col-span-4 mx-1">
                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                        {{ item.Nombres_Apellidos }}
                                    </p>
                                    <p class="flex mt-1 text-xs leading-5 text-gray-500">
                                        {{ item.Cargo }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-center w-full">
                                    <button
                                        class="flex items-center justify-center h-6 p-1 m-1 font-mono text-sm text-white align-middle rounded-md w-9 bg-primary"
                                        v-tooltip.top="'Ver programacion'" @click="console.log('Hola mundo')">
                                        <p> 1.0H </p>
                                    </button>
                                </div>
                            </div>
                        </Draggable>
                    </Container>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
