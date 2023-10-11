<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import '../../../sass/dataTableCustomized.scss';
import { Container, Draggable } from "vue-dndrop";
import { XMarkIcon, PencilIcon, Bars3Icon } from "@heroicons/vue/20/solid";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import Button from '../../Components/Button.vue';
import { useSweetalert } from '@/composable/sweetAlert';
import Knob from 'primevue/knob';
import Skeleton from 'primevue/skeleton';
import ProgressSpinner from 'primevue/progressspinner';
const { toast } = useSweetalert();
//#region Draggable
const listaDatos = ref({})
const fecha = ref(new Date().toISOString().split("T")[0])
const personal = ref()
const dates = ref([]);
const tasks = ref([]);
const loadingProgram = ref(true);
const loadingPerson = ref(true);
const loadingTasks = ref(true)
const loadingTask = ref({})
const optionValue = ref('today')

const onDrop = async (collection, dropResult) => {
    const { addedIndex, payload } = dropResult;
    if (addedIndex !== null) {
        loadingTask.value[collection] = true
        await axios.post(route('programming.store'),{task_id:collection,employee_id:payload.Num_SAP,fecha:fecha.value}).then((res) => {
            listaDatos.value[collection] = res.data.task[0].people
            loadingTask.value[collection] = false
        })
    }

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
    getTask('tomorrow')
    axios.get(route('get.personal')).then((res) => {
        personal.value = Object.values(res.data.personal)
        loadingPerson.value = false
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
        await axios.get(route('actividadesDeultimonivel', { dates: dates.value })).then((res) => {
            tasks.value = res.data
            loadingProgram.value = false;
        })
        tasks.value.forEach(element => {
            loadingTask.value[element.id] = true
            axios.get(route('get.schedule.task',{ task_id: element.id, date: dates.value[0] })).then((res) => {
                listaDatos.value[element.id] = res.data.schedule
                loadingTask.value[element.id] = false
                loadingTasks.value = false
            })
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

//#region Modal Persona
const employee = ref([])
const open = ref(false)

const employeeDialog = (item) => {
    open.value = true
    employee.value = item
}
//#endregion
</script>
<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    object-position: 50% 30%;
    border-radius: 10% 25%;
    ;
    object-fit: cover;
    /* Opciones: 'cover', 'contain', 'fill', etc. */
}
</style>

<template>
    <AppLayout>
        <div class="h-[90%] px-2">
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
            <div class="h-full grid grid-rows-auto sm:grid-rows-1 sm:grid-cols-3 sm:gap-2">
                <!--#region LISTA PROGRAMACIÓN DE ACTIVIDADES-->
                <div v-if="loadingProgram"
                    class="h-full row-start-2 row-span-6 sm:row-start-1 sm:col-start-1 sm:col-span-2 rounded-xl">
                    <Skeleton width="100%" height="100%" class="rounded-xl" />
                </div>
                <div v-if="!loadingProgram"
                    class="h-full row-start-2 row-span-6 p-1 sm:row-start-1 sm:col-start-1 sm:col-span-2 sm:space-y-1 overflow-y-auto shadow-lg custom-scroll snap-y snap-proximity ring-1 ring-gray-900/5 rounded-xl">
                    <div v-for="task in tasks"
                        class="h-1/2 flex flex-col justify-between p-2 border rounded-md shadow-md sm:h-1/2 snap-start">
                        <div class="grid grid-rows-2">
                            <div class="">
                                <p class="block overflow-hidden">{{ task.name }}
                                </p>
                                <p class="text-xs text-primary italic uppercase">{{task.project}}</p>
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
                                <!-- <div class="hidden text-center sm:justify-center sm:block">
                                    <p class="font-bold">Valor dia</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div> -->
                                <div class="hidden text-center sm:justify-center sm:block">
                                    <p class="font-bold">Diferencia</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="loadingTasks ? true : loadingTask[task.id]? true:false " class="h-full p-2 flex-col flex justify-center items-center">
                            <ProgressSpinner />
                            <p class="animate-pulse text-primary font-bold">{{loadingTasks? 'Cargando personas asignadas':'Guardando cambios'}}</p>
                        </div>
                        <Container v-if="!loadingTask[task.id]"
                            class="h-full p-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 shadow-primary custom-scroll"
                            @drop="onDrop(task.id, $event)" group-name="1">
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
                                <h2 class="mt-4 text-xl font-medium tracking-wide text-gray-700">No hay personas
                                    asignadas
                                </h2>

                                <p class="mt-2 tracking-wide text-gray-500">Arrastre una persona de la lista de la
                                    izquierda
                                    para agregarla a la actividad </p>
                            </div>
                        </Container>
                    </div>
                </div>
                <!--#endregion -->

                <!--#region LISTA PERSONAL-->
                <div v-if="loadingPerson" class="row-start-1 sm:col-start-3 h-full shadow-lg sm:blocks rounded-xl">
                    <Skeleton width="100%" height="100%" class="rounded-xl" />
                </div>
                <div v-if="!loadingPerson"
                    class="row-start-1 sm:col-start-3 h-full overflow-y-hidden sm:overflow-y-auto divide-y divide-gray-100 shadow-lg sm:block custom-scroll ring-1 ring-gray-900/5 rounded-xl">
                    <h2 class="font-semibold text-center capitalize text-primary">Personal</h2>
                    <Container
                        class="flex h-[87%] sm:space-x-0 w-full overflow-x-auto sm:overflow-x-hidden sm:overflow-y-auto sm:block sm:py-1 sm:px-1"
                        behaviour="copy" group-name="1" :get-child-payload="getChildPayload">
                        <Draggable v-for="item in personal" :drag-not-allowed="false"
                            class="py-2 pl-2 shadow-md cursor-pointer sm:rounded-xl hover:bg-blue-200 hover:scale-[102%] hover:border hover:border-primary ">
                            <div class="grid grid-cols-6">
                                <div class="flex items-center w-full">
                                    <img class="custom-image" :src="item.photo" alt="profile-photo" />
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
                                        v-tooltip.top="'Ver programacion'" @click="employeeDialog(item)">
                                        <p> 1.0H </p>
                                    </button>
                                </div>
                            </div>
                        </Draggable>
                    </Container>
                </div>
                <!--#endregion -->
            </div>
        </div>
        <!-- MODAL DE PERSONAS -->
        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-30" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                    leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed h-screen w-screen inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-30" />
                </TransitionChild>
                <div class="fixed inset-0 z-50 overflow-y-auto h-screen">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel
                                class="grid grid-cols-1 relative transform overflow-hidden rounded-lg bg-white px-2 pb-4 pt-2 text-left shadow-xl transition-all sm:my-8 w-xl">
                                <div>
                                    <div class="px-2 mt-2 text-center">
                                        <DialogTitle as="h3"
                                            class="text-5xl font-semibold text-primary text-center capitalize">
                                            Ver Detalle de Horario
                                        </DialogTitle>
                                    </div>
                                    <div class="bg-white py-8 md:py-8">
                                        <div
                                            class="mx-auto grid max-w-7xl grid-cols-2 gap-x-8 gap-y-20 px-6 lg:px-8 xl:grid-cols-2">
                                            <div class="flex flex-col gap-10 pt-12 sm:flex-column">
                                                <img class="aspect-[4/5] w-52 flex-none rounded-3xl object-cover shadow-lg"
                                                    :src="employee.photo" alt="Foto" />
                                                <div class="max-w-xl flex-auto">
                                                    <h3
                                                        class="text-lg font-semibold leading-8 tracking-tight text-gray-900">
                                                        {{ employee.Nombres_Apellidos }}
                                                    </h3>
                                                    <p class="text-base leading-7 text-gray-600">{{ employee.Cargo }}</p>
                                                    <p class="mt-6 text-base leading-7 text-gray-600">{{ employee.bio }}</p>
                                                    <ul role="list" class="mt-6 flex gap-x-6">
                                                        <li>
                                                            <a :href="employee.twitterUrl"
                                                                class="text-gray-400 hover:text-gray-500">
                                                                <span class="sr-only">Twitter</span>
                                                                <svg class="h-5 w-5" aria-hidden="true" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <!-- Icono de Twitter -->
                                                                </svg>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a :href="employee.linkedinUrl"
                                                                class="text-gray-400 hover:text-gray-500">
                                                                <span class="sr-only">LinkedIn</span>
                                                                <svg class="h-5 w-5" aria-hidden="true" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <!-- Icono de LinkedIn -->
                                                                </svg>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="max-w-2xl border border-solid border-blue-500 shadow-md rounded-lg">
                                                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                                                    About the team
                                                </h2>
                                                <p class="mt-6 text-lg leading-8 text-gray-600">
                                                    We’re a dynamic group of individuals who are passionate about what we do
                                                    and dedicated to
                                                    delivering the best results for our clients.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </DialogPanel>

                        </TransitionChild>
                    </div>
            </div>
        </Dialog>
    </TransitionRoot>

</AppLayout></template>
