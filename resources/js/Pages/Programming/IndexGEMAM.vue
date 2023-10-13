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
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
const { toast } = useSweetalert();

//#region Draggable
const listaDatos = ref({})
const date = ref(new Date().toISOString().split("T")[0])
const personal = ref()
const dates = ref([]);
const tasks = ref([]);
const loadingProgram = ref(true);
const loadingPerson = ref(true);
const loadingTasks = ref(true)
const loadingTask = ref({})
const optionValue = ref('today')

// El código anterior define una función `onDrop` en Vue. Esta función se activa cuando un elemento se
// coloca en una colección.
const onDrop = async (collection, dropResult) => {
    const { addedIndex, payload } = dropResult;
    if (addedIndex !== null) {
        loadingTask.value[collection] = true
        await axios.post(route('programming.store'), { task_id: collection, employee_id: payload.Num_SAP, name: payload.Nombres_Apellidos, fecha: dates.value[0] }).then((res) => {
            listaDatos.value[collection] = res.data.task
            loadingTask.value[collection] = false
        })
    }

}

// El código anterior define una función llamada `getFoto` que toma un parámetro `correo`. Dentro de la
// función, realiza una solicitud POST a una ruta llamada `'get.foto'` con el parámetro `correo` como
// carga útil. Luego recupera los datos de la "foto" de la respuesta y los devuelve.
const getFoto = (correo) => {
    axios.post(route('get.foto', correo)).then((res) => {
        return res.data.photo
    })
}

// El código anterior define una función llamada "getChildPayload" que toma un parámetro "índice". Esta
// función devuelve el valor en el índice especificado en la variable "personal".
const getChildPayload = (index) => {
    return personal.value[index];
}

//#endregion

// El código anterior utiliza el gancho de ciclo de vida `onMounted` de Vue para ejecutar algún código
// cuando el componente está montado.
onMounted(() => {
    getTask('tomorrow')
    axios.get(route('get.personal')).then((res) => {
        personal.value = Object.values(res.data.personal)
        loadingPerson.value = false
    })
})


// El código anterior es una función de Vue.js que recupera tareas según la opción seleccionada.
const getTask = async (option) => {
<<<<<<< HEAD

=======
    const today = new Date();
>>>>>>> 4a53ab51f227ef38084b7d445df9c6f149ba8703
    optionValue.value = option
    switch (option) {
        case 'today':
            dates.value[0] = new Date();
            dates.value[1] = new Date();
            break;
        case 'tomorrow':
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            date.value = tomorrow.toISOString().split("T")[0];
            dates.value[0] = tomorrow;
            dates.value[1] = tomorrow;
            break;
        case 'date':
            dates.value[0] = date.value;
            dates.value[1] = date.value;
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
            axios.get(route('get.schedule.task', { task_id: element.id, date: dates.value[0] })).then((res) => {
                listaDatos.value[element.id] = res.data.schedule
                loadingTask.value[element.id] = false
                loadingTasks.value = false
            })
        });
    }
}

// El código anterior es una función llamada `format24h` que toma un parámetro `hora` (que representa
// una hora en formato de 24 horas) y devuelve una cadena de hora formateada en formato de 12 horas con
// AM/PM.
function format24h(hora) {
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}
//#region

// El código anterior define una función llamada "quitar" que toma tres parámetros: "tarea", "índice" y
// "persona". Dentro de la función realiza una solicitud POST asíncrona usando axios a una ruta llamada
// "programming.delete" con los datos { task_id: tarea, empleado_id: persona.Num_SAP, fecha: fecha}. Si
// la solicitud tiene éxito, registra la respuesta en la consola, elimina un elemento de la matriz
// "listaDatos.value[task.id]" en el índice especificado y muestra un mensaje de notificación de éxito.
// También hay definida una función "editar" vacía.
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

// El código anterior define una función llamada `employeeDialog` que toma un parámetro `item`. Dentro
// de la función, establece el valor de "open" en "verdadero" y el valor de "employee" en el
// parámetro "elemento".
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

.loader {
    position: relative;
    width: 100px;
    height: 100px;
}

.loader:before,
.loader:after {
    content: '';
    border-radius: 50%;
    position: absolute;
    inset: 0;
    box-shadow: 0 0 10px 2px rgba(0, 0, 0, 0.3) inset;
}

.loader:after {
    box-shadow: 0 2px 0 rgb(46 48 146) inset;
    animation: rotate 2s linear infinite;
}

@keyframes rotate {
    0% {
        transform: rotate(0)
    }

    100% {
        transform: rotate(360deg)
    }
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
                        type="date" name="date" id="date" v-model="date" @change="getTask('date')">
                </div>
            </div>
            <div class="grid h-full grid-rows-auto sm:grid-rows-1 sm:grid-cols-3 sm:gap-2">
                <!--LISTA PROGRAMACIÓN DE ACTIVIDADES-->
                <div v-if="loadingProgram"
                    class="h-full row-span-6 row-start-2 sm:flex sm:flex-col sm:justify-center sm:items-center sm:row-start-1 sm:col-start-1 sm:col-span-2 rounded-xl">
                    <span class="flex items-center justify-center w-full h-full loader">
                        <ApplicationLogo class="justify-center" :letras="true"></ApplicationLogo>
                    </span>
                    <p class="font-bold animate-pulse text-primary"> Cargando actividades</p>
                </div>
                <div v-if="!loadingProgram"
                    class="h-full row-span-6 row-start-2 p-1 overflow-y-auto sm:row-start-1 sm:col-start-1 sm:col-span-2 sm:space-y-1 custom-scroll snap-y snap-proximity rounded-xl">
                    <div v-for="task in tasks"
                        class="flex flex-col justify-between p-2 border rounded-md shadow-md h-1/2 sm:h-1/2 snap-start">
                        <div class="grid grid-rows-2">
                            <div class="">
                                <p class="block overflow-hidden">{{ task.name }}
                                </p>
                                <p class="text-xs italic uppercase text-primary">{{ task.project }}</p>
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

                        <div v-if="loadingTasks ? true : loadingTask[task.id] ? true : false"
                            class="flex flex-col items-center justify-center h-full p-2">
                            <span class="flex items-center justify-center w-full h-full loader">
                                <ApplicationLogo class="justify-center" :letras="true"></ApplicationLogo>
                            </span>
                            <p class="font-bold animate-pulse text-primary">{{ loadingTasks ? 'Cargando personas asignadas':'Guardando cambios'}}</p>
                        </div>
                        <Container v-if="!loadingTask[task.id]"
                            class="h-full p-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 shadow-primary custom-scroll"
                            @drop="onDrop(task.id, $event)" group-name="1">
                            <div class="grid grid-cols-2 gap-1"
                                v-if="listaDatos[task.id] !== undefined && listaDatos[task.id].length != 0">
                                <div v-for="(item, index) in listaDatos[task.id]" class="p-1 mt-1 border-2 rounded-md">
                                    <div class="flex items-center justify-between w-full">
                                        <p class="text-sm font-semibold ">{{ item.name }}</p>
                                        <button v-tooltip.top="'Eliminar de la Actividad'" @click="deleteSchedule(task, index, item)">
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
                <div v-if="loadingPerson"
                    class="h-full row-start-1 shadow-lg sm:col-start-3 sm:flex sm:flex-col sm:items-center sm:justify-center rounded-xl">
                    <span class="flex items-center justify-center w-full h-full loader">
                        <ApplicationLogo class="justify-center" :letras="true"></ApplicationLogo>
                    </span>
                    <p class="font-bold animate-pulse text-primary"> Cargando personas</p>
                </div>
                <div v-if="!loadingPerson"
                    class="h-full row-start-1 overflow-y-hidden divide-y divide-gray-100 shadow-lg sm:col-start-3 sm:overflow-y-auto sm:block custom-scroll ring-1 ring-gray-900/5 rounded-xl">
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
        <!--#region MODAL DE PERSONAS -->
        <TransitionRoot as="template" :show="open">
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
                            <DialogPanel
                                class="relative grid grid-cols-1 px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 w-xl">
                                <div>
                                    <div class="px-2 mt-2 text-center">
                                        <DialogTitle as="h3"
                                            class="text-3xl font-semibold text-center capitalize text-primary">
                                            Ver detalle de horario
                                        </DialogTitle>
                                    </div>
                                    <div class="py-8 bg-white md:py-8">
                                        <div
                                            class="grid grid-cols-2 px-6 mx-auto max-w-7xl gap-x-8 gap-y-20 lg:px-8 xl:grid-cols-2">
                                            <div class="flex flex-col items-center justify-center gap-10 pt-12 sm:flex-col">
                                                <img class="aspect-[4/5] w-52 flex-none rounded-3xl object-cover shadow-lg"
                                                    :src="employee.photo" alt="Foto" />
                                                <div class="max-w-xl text-center">
                                                    <h3
                                                        class="text-lg font-semibold leading-8 tracking-tight text-gray-900">
                                                        {{ employee.Nombres_Apellidos }}
                                                    </h3>
                                                    <p class="text-base leading-7 text-gray-600">{{ employee.Cargo }}</p>
                                                    <p class="text-base leading-7 text-gray-600">{{ employee.Correo }}</p>
                                                    <ul role="list" class="flex justify-center mt-6 gap-x-6">
                                                        <li>
                                                            <a :href="employee.twitterUrl"
                                                                class="text-gray-400 hover:text-gray-500">
                                                                <span class="sr-only">Twitter</span>
                                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <!-- Icono de Twitter -->
                                                                </svg>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a :href="employee.linkedinUrl"
                                                                class="text-gray-400 hover:text-gray-500">
                                                                <span class="sr-only">LinkedIn</span>
                                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <!-- Icono de LinkedIn -->
                                                                </svg>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="max-w-2xl border border-blue-500 border-solid rounded-lg shadow-md">
                                                <div class="grid grid-cols-3 gap-1">
                                                    <span v-for="horario in item.schedule_times"
                                                        class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                        <!-- {{ format24h(horario.hora_inicio) }}-{{ format24h(horario.hora_fin) }} -->
                                                    </span>
                                                </div>
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
        <!--#endregion-->
    </AppLayout>
</template>
