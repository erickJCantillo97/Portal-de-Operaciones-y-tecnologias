<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { Container, Draggable } from "vue-dndrop";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert';
import Knob from 'primevue/knob';
import FullCalendar from '@/Components/FullCalendar.vue'
import OverlayPanel from 'primevue/overlaypanel';
import Dropdown from 'primevue/dropdown';
import Loading from '@/Components/Loading.vue';
import CustomInput from '@/Components/CustomInput.vue';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Listbox from 'primevue/listbox';
import CustomModal from '@/Components/CustomModal.vue';
import RadioButton from 'primevue/radiobutton';
import Empty from '@/Components/Empty.vue';
import Calendar from 'primevue/calendar';

const { hasRole, hasPermission } = usePermissions()
const { toast } = useSweetalert();

const props = defineProps({
    hours: Object
})

//#region Draggable
const listaDatos = ref({})
const date = ref(new Date().toISOString().split("T")[0])
const personal = ref()
const op = ref()
const tasks = ref([]);
const loadingProgram = ref(true);
const loadingPerson = ref(true);
const personalHours = ref({});
const loadingTasks = ref(true)
const loadingTask = ref({})
const optionValue = ref('today')

// El código anterior define una función `onDrop` en Vue. Esta función se activa cuando un elemento se
// coloca en una colección.
const onDrop = async (collection, dropResult) => {
    const { addedIndex, payload } = dropResult;
    if (addedIndex !== null) {
        loadingTask.value[collection] = true
        await axios.post(route('programming.store'), { task_id: collection, employee_id: payload.Num_SAP, name: payload.Nombres_Apellidos, fecha: date.value }).then((res) => {
            listaDatos.value[collection] = res.data.task
            personalHours.value[(payload.Num_SAP)] = res.data.hours
            loadingTask.value[collection] = false
        })
    }
}

const getAssignmentHours = (employee_id) => {
    axios.get(route('get.assignment.hours', [date.value, employee_id])).then((res) => {
        personalHours.value[(employee_id)] = res.data;
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
})

// El código anterior es una función de Vue.js que recupera tareas según la opción seleccionada.
const getTask = async (option) => {
    loadingPerson.value = true
    loadingProgram.value = true
    optionValue.value = option
    switch (option) {
        case 'today':
            date.value = new Date().toISOString().split("T")[0];
            break;
        case 'tomorrow':
            var tomorrow = new Date()
            date.value = new Date(tomorrow.setDate(tomorrow.getDate() + 1)).toISOString().split("T")[0];
            break;
        default:
            break;
    }
    tasks.value = [];
    await axios.get(route('actividadesDeultimonivel', { week: date.value })).then((res) => {
        tasks.value = res.data
        loadingProgram.value = false;
    })
    tasks.value.forEach(element => {
        loadingTask.value[element.id] = true
        axios.get(route('get.schedule.task', { task_id: element.id, date: date.value })).then((res) => {
            listaDatos.value[element.id] = res.data.schedule
            loadingTask.value[element.id] = false
            loadingTasks.value = false
        })
    });
    axios.get(route('get.personal.user')).then((res) => {
        personal.value = Object.values(res.data.personal)
        personal.value.forEach(
            async element => {
                await axios.get(route('get.assignment.hours', [date.value, (element.Num_SAP)])).then((res) => {
                    personalHours.value[element.Num_SAP] = res.data;
                });
            })
        loadingPerson.value = false
    })

}

// El código anterior es una función llamada `format24h` que toma un parámetro `hora` (que representa
// una hora en formato de 24 horas) y devuelve una cadena de hora formateada en formato de 12 horas con
// AM/PM.
function format24h(hora) {
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}
function formatdatetime24h(date) {
    return new Date(date).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}
//#region

// El código anterior define una función llamada "quitar" que toma tres parámetros: "tarea", "índice" y
// "persona". Dentro de la función realiza una solicitud POST asíncrona usando axios a una ruta llamada
// "programming.delete" con los datos { task_id: tarea, empleado_id: persona.Num_SAP, fecha: fecha}. Si
// la solicitud tiene éxito, registra la respuesta en la consola, elimina un elemento de la matriz
// "listaDatos.value[task.id]" en el índice especificado y muestra un mensaje de notificación de éxito.
// También hay definida una función "editar" vacía.

const deleteSchedule = async (task, index, schedule) => {

    await axios.post(route('programming.delete', schedule.id)).then((res) => {
        listaDatos.value[task.id] = res.data.task
        getAssignmentHours((schedule.employee_id))
        toast('Se ha eliminado a ' + schedule.name + ' de la tarea ' + task.name, 'success');
    })
}

//#endregion

//#region Modal Persona
const employee = ref([])
const open = ref(false)
const events = ref([])
const turnSelect = ref()
const rendersCalendars = ref(0)
const showHours = ref('Horas')

// El código anterior define una función llamada `employeeDialog` que toma un parámetro `item`. Dentro
// de la función, establece el valor de "open" en "verdadero" y el valor de "employee" en el
// parámetro "elemento".
const employeeDialog = (item) => {
    open.value = true
    employee.value = item
    axios.get(route('get.times.employees', { date: date.value, employee_id: item.Num_SAP }))
        .then((res) => {
            events.value = res.data.times
            rendersCalendars.value++;
        })
        .catch(error => {
            console.log(error);
        })
}

const submit = () => {
    console.log('Hello!');
}

const editHorario = ref()
const nuevoHorario = ref({})
const optionSelectHours = ref('select')
const modhours = ref(false)
const toggle = (horario, data) => {
    editHorario.value = horario
    editHorario.value.data=data
    nuevoHorario.value = {}
    modhours.value = true
}
//#endregion

const filter = ref('')

</script>
<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    object-position: 50% 30%;
    border-radius: 10% 25%;
    object-fit: cover;
    /* Opciones: 'cover', 'contain', 'fill', etc. */
}

.info-resto {
    font-size: 15px;
    text-wrap: balance;
    opacity: .5;
}
</style>

<template>
    <AppLayout>
        <div class="h-[89vh] grid grid-cols-3">
            <span class="col-span-2 space-y-1 pt-1 px-1">
                <span class="flex justify-between items-center">
                    <span class="text-xl font-bold text-primary h-full items-center flex">
                        Programación de actividades
                    </span>
                    <div class="flex items-center space-x-2">
                        <span class="p-buttonset">
                            <Button label="Hoy" :outlined="optionValue != 'today'" @click="getTask('today')" />
                            <!-- <Button label="Proxima Semana" :outlined="optionValue != 'today'" @click="getTask('today')" /> -->
                            <Button label="Mañana" :outlined="optionValue != 'tomorrow'" @click="getTask('tomorrow')" />
                        </span>
                        <CustomInput type="date" v-model:input="date" @change="getTask('date')" />
                        <!-- <CustomInput type="date" id="date" v-model:input="date" @change="getTask('date')" /> -->
                    </div>
                </span>
                <Listbox :options="tasks" :filterFields="['task', 'name', 'project']" class="col-span-2" filter :pt="{
                    list: '!h-[69vh] !px-1 !snap-y !snap-mandatory',
                    item: '!h-full !p-0 !rounded-md !snap-start !my-0.5',
                    filterInput: '!h-8',
                    header: '!p-1'
                }">
                    <template #option="slotProps">
                        <div class="flex flex-col justify-between h-full p-2 border rounded-md shadow-md snap-start">
                            <p><b>{{ slotProps.option.task }}</b> <i class="fa-solid fa-angle-right"></i> {{
                                slotProps.option.name }} </p>
                            <p class="text-xs italic uppercase text-primary">{{ slotProps.option.project }}</p>
                            <span class="grid items-center text-xs grid-cols-6">
                                <span class="grid grid-cols-3">
                                    <p class="font-bold ">I:</p>
                                    <p class="font-mono col-span-2 cursor-default" v-tooltip="'Fecha inicio'">{{
                                        slotProps.option.startDate
                                    }} </p>
                                    <p class="font-bold">F:</p>
                                    <p class="font-mono col-span-2 cursor-default" v-tooltip="'Fecha fin'">{{
                                        slotProps.option.endDate }}
                                    </p>
                                </span>
                                <span class="flex justify-center">
                                    <Knob v-tooltip.top="'Avance: ' + parseInt(slotProps.option.percentDone) + '%'"
                                        :model-value=parseInt(slotProps.option.percentDone) :size=50
                                        valueTemplate="{value}%" readonly />
                                </span>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Valor estimado</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Valor programado</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Diferencia</p>
                                    <p class="">$1.000.000
                                    </p>
                                </div>
                            </span>
                            <div v-if="loadingTasks ? true : loadingTask[slotProps.option.id] ? true : false"
                                class="flex flex-col items-center justify-center h-full p-2">
                                <Loading message="Cargando" />
                            </div>
                            <Container v-if="!loadingTask[slotProps.option.id]"
                                class="h-full p-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 shadow-primary custom-scroll"
                                @drop="onDrop(slotProps.option.id, $event)" group-name="1">
                                <div class="grid grid-cols-2 gap-1"
                                    v-if="listaDatos[slotProps.option.id] !== undefined && listaDatos[slotProps.option.id].length != 0">
                                    <div v-for="(item, index) in listaDatos[slotProps.option.id]"
                                        class="p-1 mt-1 border-2 rounded-md">
                                        <div class="flex items-center justify-between w-full ">
                                            <p class="text-sm font-semibold ">{{ item.name }}</p>
                                            <button v-tooltip.top="'Eliminar de la Actividad'"
                                                @click="deleteSchedule(slotProps.option, index, item)">
                                                <i
                                                    class="fa-solid fa-circle-xmark text-danger hover:animate-pulse hover:scale-125" />
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between w-full font-mono align-middle ">
                                            <div class="grid w-full grid-cols-3 gap-2">
                                                <div v-for="horario in item.schedule_times"
                                                    class="flex items-center justify-between px-1 py-1 text-green-900 bg-green-200 rounded-md cursor-default group">
                                                    <button v-tooltip.bottom="'En desarrollo'"
                                                        class="hidden group-hover:flex"
                                                        @click="console.log('En desarrollo')"
                                                        v-if="item.schedule_times.length > 1">
                                                        <i
                                                            class="fa-solid fa-trash-can text-danger text-xs hover:animate-pulse hover:scale-125"></i>
                                                    </button>
                                                    <button v-tooltip.bottom="'Eliminar'" class="hidden group-hover:flex"
                                                        @click="deleteSchedule(slotProps.option, index, item)" v-else>
                                                        <i
                                                            class="fa-solid fa-trash-can text-danger text-xs hover:animate-pulse hover:scale-125"></i>
                                                    </button>
                                                    <span class="w-full text-xs tracking-tighter text-center">
                                                        {{ format24h(horario.hora_inicio) }} {{ format24h(horario.hora_fin)
                                                        }}
                                                    </span>
                                                    <button v-tooltip.bottom="'Cambiar horario'"
                                                        class="hidden group-hover:flex"
                                                        @click="optionSelectHours = 'select'; toggle(horario, item.name )">
                                                        <i
                                                            class="fa-solid fa-pencil text-primary text-xs hover:animate-pulse hover:scale-125"></i>
                                                    </button>
                                                </div>

                                            </div>
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
                    </template>
                    <template #empty>
                        <Loading v-if="loadingProgram" message="Cargando actividades" />
                    </template>

                </Listbox>
            </span>
            <!--#region LISTA PERSONAL-->

            <div class="row-span-2 rounded-lg">
                <TabView class="tabview-custom" :scrollable="true" :pt="{
                    nav: '!flex !justify-between',
                    panelContainer: '!p-1'
                }">
                    <TabPanel header="Personas" :pt="{
                        root: 'w-full',
                        headerTitle: '!w-full !flex !justify-center',
                    }">
                        <CustomInput v-model:input="filter" type="search" icon="fa-solid fa-magnifying-glass" />
                        <Loading v-if="loadingPerson" message="Cargando personas" />
                        <Container v-else oncontextmenu="return false" onkeydown="return false" behaviour="copy"
                            group-name="1" :get-child-payload="getChildPayload"
                            class="!h-[75vh] space-y-1 mt-1 p-1 snap-y snap-mandatory overflow-y-auto">
                            <!-- <span v-for="item in personal"> -->
                            <Draggable v-for="item in personal"
                                :class="(item.Nombres_Apellidos.toUpperCase().includes(filter.toUpperCase()) || item.Cargo.toUpperCase().includes(filter.toUpperCase())) ? '' : '!hidden'"
                                :drag-not-allowed="personalHours[(item.Num_SAP)] < 9.5 ? false : true"
                                class="snap-start rounded-xl shadow-md cursor-pointer hover:bg-blue-200 hover:ring-1 hover:ring-primary">
                                <div class="grid grid-cols-5 gap-x-1 p-1">
                                    <img class="custom-image " :src="item.photo" onerror="this.src='/svg/cotecmar-logo.svg'"
                                        draggable="false" alt="profile-photo" />
                                    <span class="col-span-3">
                                        <p class="text-sm font-semibold truncate leading-6 text-gray-900">
                                            {{ item.Nombres_Apellidos }}
                                        </p>
                                        <p class="flex mt-1 text-xs truncate leading-5 text-gray-500">
                                            {{ item.Cargo }}
                                        </p>
                                    </span>
                                    <span class="flex items-center">
                                        <Button v-tooltip.left="'Horas programadas'"
                                            :label="personalHours[(item.Num_SAP)] + ' horas'"
                                            :severity="personalHours[(item.Num_SAP)] < 9.5 ? 'primary' : 'success'"
                                            @click="employeeDialog(item)" :pt="{
                                                label: '!text-xs'
                                            }" />
                                    </span>
                                </div>
                            </Draggable>
                            <!-- </span> -->
                        </Container>
                    </TabPanel>

                    <TabPanel header="Grupos" :pt="{
                        root: 'w-full',
                        headerTitle: '!w-full !flex !justify-center'
                    }">

                    </TabPanel>

                </TabView>
            </div>

        </div>
    </AppLayout>
    <CustomModal v-model:visible="modhours" icon="fa-regular fa-clock" width="60vw" :titulo="'Modificar horario de '+editHorario.data">
        <template #body>
            <!-- {{ editHorario }} -->
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between col-span-3 ">
                    <p>Horario actual:</p>
                    <p class="px-1 py-1 text-green-900 bg-green-200 rounded-md">
                        {{ format24h(editHorario.hora_inicio) }} {{ format24h(editHorario.hora_fin) }}
                    </p>
                </div>
                <TabView class="border rounded-md p-1">
                    <TabPanel header="Seleccionar">
                        <Listbox :options="props.hours" v-model="nuevoHorario" optionLabel="name" filter :pt="{
                            list: '!h-40 !p-1',
                            item: '!h-10 !p-0 !rounded-md hover:!bg-primary-light',
                            filterInput: '!h-8',
                            header: '!p-1'
                        }">
                            <template #option="slotProps">
                                <div class=" h-full grid grid-cols-4 border px-1 rounded-md ">
                                    <span class="flex justify-between items-center overflow-hidden">
                                        <p class="text-overflow h-full overflow-y-auto flex text-sm font-bold items-center">
                                            {{ slotProps.option.name }}</p>
                                        <i class="fa-regular fa-clock"></i>
                                    </span>
                                    <div class="text-xs items-center text-center col-span-3 grid grid-cols-4">
                                        <span>
                                            <p class="font-bold">Hora Inicio</p>
                                            <p>{{ formatdatetime24h(slotProps.option.startShift) }}
                                            </p>
                                        </span>
                                        <span>
                                            <p class="font-bold">Hora Fin</p>
                                            <p>{{ formatdatetime24h(slotProps.option.endShift) }}</p>
                                        </span>
                                        <span>
                                            <p class="font-bold">Descanso</p>
                                            <p>{{ slotProps.option.timeBreak }}h</p>
                                        </span>
                                        <span>
                                            <p class="font-bold">H. Laborales</p>
                                            <p> {{ parseFloat(slotProps.option.hours).toFixed(1) }}h</p>
                                        </span>
                                    </div>
                                </div>
                            </template>
                            <template #empty>
                                <Empty message="No hay turnos para mostrar"></Empty>
                            </template>
                            <template #emptyfilter>
                                <Empty message="No se encuentran turnos"></Empty>
                            </template>
                        </Listbox>
                    </TabPanel>
                    <TabPanel header="Nuevo">
                        <div class="h-48 m-1">
                            <CustomInput v-model:input="nuevoHorario.name" label="Nombre" type="text" id="name"
                                placeholder="Nombre del horario" />
                            <span class="grid grid-cols-2 gap-x-1">
                                <CustomInput v-model:input="nuevoHorario.startShift" label="Hora inicio" type="time"
                                    id="start" placeholder="Hora de inicio" />
                                <CustomInput v-model:input="nuevoHorario.endShift" label="Hora fin" type="time" id="end"
                                    placeholder="Hora de fin" />
                            </span>
                        </div>
                    </TabPanel>
                </TabView>
            </div>
            <p>Aplicar por:</p>
            <span class="flex items-center p-2 gap-4">
                <div v-for="category in [{ name: 'el '+date, key: 'dia' }, { name: 'Resto de la actividad', key: 'resto' }, { name: 'Rango de fechas', key: 'range' }, { name: 'Fechas específicos', key: 'multiple' }]"
                    :key="category.key" class="flex items-center">
                    <RadioButton v-model="nuevoHorario.type" :inputId="category.key" name="dynamic" :value="category.key"
                        @click="nuevoHorario.days = null" />
                    <label :for="category.key" class="ml-1 mb-0">{{ category.name }}</label>
                </div>
            </span>
            <Calendar v-if="nuevoHorario.type == 'multiple' || nuevoHorario.type == 'range'" show-icon
                v-model="nuevoHorario.days" :selectionMode="nuevoHorario.type" :manualInput="false" :pt="{
                    root: '!w-full',
                    input: '!h-8'
                }" />
        </template>
        <template #footer>
            <Button @click="console.log('hace algo'); nuevoHorario = null; op.hide()" icon="fa-solid fa-floppy-disk"
                label="Guardar" />
        </template>
    </CustomModal>
    <!--#region MODAL DE PERSONAS -->
    <CustomModal v-model:visible="open" titulo="Ver detalle de horario">
        <template #body>
            <div class="py-2 bg-white">
                <div class="grid grid-cols-4 mx-auto max-w-[100%] gap-x-2 gap-y-20">
                    <!--COLUMNA 1 (SECCIÓN INFORMACIÓN DEL EMPLEADO)-->
                    <div class="col-span-1">
                        <div
                            class="flex flex-col items-center justify-center gap-10 pt-12 font-bold border border-solid rounded-md shadow-md sm:flex-col">
                            <img class="aspect-[4/5] w-32 flex-none rounded-3xl object-cover shadow-md"
                                :src="employee.photo" alt="Foto" />
                            <div class="max-w-xl text-center">
                                <h3 class="text-lg font-semibold leading-8 tracking-tight text-gray-900 whitespace-nowrap">
                                    {{ employee.Nombres_Apellidos }}
                                </h3>
                                <p class="text-base leading-7 text-gray-600">{{ employee.Cargo }}
                                </p>
                                <p class="text-base leading-7 text-gray-600">{{ employee.Correo }}
                                </p>
                                <ul role="list" class="flex justify-center mt-6 gap-x-6">
                                    <li>
                                        <a :href="employee.twitterUrl" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Twitter</span>
                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                <!-- Icono de Twitter -->
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a :href="employee.linkedinUrl" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">LinkedIn</span>
                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                <!-- Icono de LinkedIn -->
                                            </svg>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--COLUMNA 2 - (FullCalendar)-->
                    <div class="flex col-span-3 flex-nowrap custom-scroll">
                        <FullCalendar :initialEvents="events" :tasks="tasks" :date="date" :employee="employee"
                            :project="project" :key="rendersCalendars" />

                    </div>
                </div>
            </div>
        </template>
    </CustomModal>

    <!-- <TransitionRoot as="template" :show="open">
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
                            class="p-6 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl max-w-screen-2xl sm:my-8">

                            <div class="px-2 mt-2 text-center">
                                <DialogTitle as="h3" class="text-3xl font-semibold text-center text-primary">
                                    Ver detalle de horario
                                </DialogTitle>
                            </div>
                            <div class="py-2 bg-white">
                                <div class="grid grid-cols-4 mx-auto max-w-[100%] gap-x-2 gap-y-20">
                                    
                                    <div class="col-span-1">
                                        <div
                                            class="flex flex-col items-center justify-center gap-10 pt-12 font-bold border border-solid rounded-md shadow-md sm:flex-col">
                                            <img class="aspect-[4/5] w-32 flex-none rounded-3xl object-cover shadow-md"
                                                :src="employee.photo" alt="Foto" />
                                            <div class="max-w-xl text-center">
                                                <h3
                                                    class="text-lg font-semibold leading-8 tracking-tight text-gray-900 whitespace-nowrap">
                                                    {{ employee.Nombres_Apellidos }}
                                                </h3>
                                                <p class="text-base leading-7 text-gray-600">{{ employee.Cargo }}
                                                </p>
                                                <p class="text-base leading-7 text-gray-600">{{ employee.Correo }}
                                                </p>
                                                <ul role="list" class="flex justify-center mt-6 gap-x-6">
                                                    <li>
                                                        <a :href="employee.twitterUrl"
                                                            class="text-gray-400 hover:text-gray-500">
                                                            <span class="sr-only">Twitter</span>
                                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                               
                                                            </svg>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a :href="employee.linkedinUrl"
                                                            class="text-gray-400 hover:text-gray-500">
                                                            <span class="sr-only">LinkedIn</span>
                                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                
                                                            </svg>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="flex col-span-3 flex-nowrap custom-scroll">
                                        <FullCalendar :initialEvents="events" :tasks="tasks" :date="date"
                                            :employee="employee" :project="project" :key="rendersCalendars" />

                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot> -->
    <!--#endregion-->
</template>
