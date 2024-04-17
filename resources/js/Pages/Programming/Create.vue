<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import CustomInput from '@/Components/CustomInput.vue';
import ModalColisions from './Components/ModalColisions.vue'
import ListPersonDrag from './Components/ListPersonDrag.vue'
import axios from 'axios';
import MultiSelect from 'primevue/multiselect';
import ButtonGroup from 'primevue/buttongroup';
import ProgressBar from 'primevue/progressbar';
import OverlayPanel from 'primevue/overlaypanel';
import NoContentToShow from '@/Components/NoContentToShow.vue';
import CustomModal from '@/Components/CustomModal.vue';
import Calendar from 'primevue/calendar';
import RadioButton from 'primevue/radiobutton';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import InputSwitch from 'primevue/inputswitch';
import CustomShiftSelector from '@/Components/CustomShiftSelector.vue';
import { useToast } from "primevue/usetoast";
import TaskProgramming from './Components/TaskProgramming.vue';
import Knob from 'primevue/knob';
import Dropdown from 'primevue/dropdown';
import Listbox from 'primevue/listbox';
import UserStatusProgramming from '@/Components/sections/UserStatusProgramming.vue';
const toast = useToast();
// const { hasRole, hasPermission } = usePermissions()

defineProps({
    projects: Array
})

// #region funciones basicas
function format24h(hora) {
    try {
        if (hora.length > 5) {
            return new Date(hora).toLocaleString('es-CO',
                { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        } else {
            return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
                { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        }
    } catch (error) {
        console.log(error)
        return 'error'
    }

}
function obtenerFormatoSemana(fecha) {
    const fechaActual = fecha;
    const año = fechaActual.getFullYear();
    const diferencia = fecha - (new Date(fecha.getFullYear(), 0, 1));
    const diasTranscurridos = Math.floor(diferencia / (24 * 60 * 60 * 1000));
    const semana = Math.ceil((diasTranscurridos + (new Date(fecha.getFullYear(), 0, 1)).getDay() + 1) / 7); // Función para obtener el número de la semana
    return `${año}-W${semana}`;
}
function obtenerFechasSemana(diaSeleccionado) {
    // const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const fechasSemana = [];

    // Asegurémonos de que el día seleccionado sea un objeto Date
    const fechaSeleccionada = new Date(diaSeleccionado);

    // Restamos los días necesarios para llegar al lunes
    fechaSeleccionada.setDate(fechaSeleccionada.getDate() - fechaSeleccionada.getDay() + 1);

    // Generamos las fechas para cada día de la semana
    for (let i = 0; i < 7; i++) {
        fechasSemana.push(new Date(fechaSeleccionada));
        fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1);
    }

    return fechasSemana;
}

function obtenerDiaSemana(dato) {
    const partes = dato.split('-W');
    const año = parseInt(partes[0]);
    const semana = parseInt(partes[1]);

    // Crear una fecha para el primer día de la semana
    const fecha = new Date(año, 0, 1 + (semana - 1) * 7);

    return fecha;
}

function esMovil() {
    const dispositivos = [
        /Android/i,
        /webOS/i,
        /iPhone/i,
        /iPad/i,
        /iPod/i,
        /BlackBerry/i,
        /Windows Phone/i
    ];

    return dispositivos.some((dispositivo) => {
        return navigator.userAgent.match(dispositivo);
    });
}

//#endregion

//#region variables
const openConflict = ref(false)
const date = ref(new Date())
const conflicts = ref()
const task = ref([])
const mode = ref('date')
const dates = ref(new Date(date.value.getFullYear(), date.value.getMonth(), date.value.getDate() + 1))
const diasSemana = ref(obtenerFechasSemana(dates.value))
const projectsSelected = ref([])
const overlayPerson = ref()
const overlayAddPerson = ref()
const personsEdit = ref()
const tabActive = ref()
const personal = ref()
const selectDays = ref([])
const personDrag = ref({})
const arrayPersonFilter = ref({
    loading: false,
    data: {
        programados: [],
        noProgramados: []
    }
})
const personsSelect = ref()

//#endregion

//#region Consultas

const getTask = async (option) => {
    if (option == null) {
        option = mode.value
    }
    if (option == 'week') {
        mode.value = option
        !(dates.value instanceof Array) ?? (dates.value = obtenerFormatoSemana(new Date()))
    } else if (option == 'date') {
        mode.value = option
        !(dates.value instanceof Date) ?? (dates.value = new Date())
    } else if (mode.value == 'month') {

    } else {

    }
};

const getPersonalData = () => {
    axios.get(route('get.personal.user')).then((res) => {
        personal.value = Object.values(res.data.personal)
    })
}
if (esMovil()) {
    getPersonalData()
}
//#endregion

//#region drag

async function onDrop(task, fecha, option) {
    if (new Date(fecha) >= new Date(date.value.toISOString().split("T")[0])) {
        task.loading = true
        if (option == 'move') {
            personDrag.value.task.loading=true
            await axios.post(route('programming.move'), { task: task.id, date: fecha, schedule: personDrag.value.times[0].idSchedule }).then((res) => {
                // console.log(res.data)
                if (res.data.status == false) {
                    task.loading = false
                    task.employees = res.data.task
                    toast.add({ severity: 'error', group: "customToast", text: res.data.mensaje, life: 2000 })
                } else {
                    personDrag.value.task.employees = personDrag.value.task.employees.filter(person => person.NumSAP !== personDrag.value.NumSAP);
                    task.employees = res.data.task
                    task.loading = false
                    personDrag.value.task.loading=false
                    toast.add({ severity: 'success', group: "customToast", text:res.data.mensaje, life: 2000 })
                }
            })

            task.loading = false
        } else {
            await axios.post(route('programming.store'), { task_id: task.id, employee_id: personDrag.value.Num_SAP, name: personDrag.value.Nombres_Apellidos, fecha }).then((res) => {
                if (Object.values(res.data.conflict).length > 0) {
                    conflicts.value = Object.values(res.data.conflict)
                    openConflict.value = true;
                    task.loading = false
                    task.value = task
                    task.employees = res.data.task
                } else if (res.data.status == false) {
                    task.loading = false
                    task.employees = res.data.task
                    toast.add({ severity: 'error', group: "customToast", text: 'Hubo un error al programar', life: 2000 })
                } else {
                    task.employees = res.data.task
                    task.loading = false
                    toast.add({ severity: 'success', group: "customToast", text: 'Persona programada', life: 2000 })
                }
            })
        }
    } else {
        toast.add({ severity: 'error', group: "customToast", text: 'No se puede programar en dias anteriores', life: 2000 })
    }
}

//#endregion


//#region edicion de horarios

const formEditHour = ref({
    userName: null, //nombre de la persona
    timeName: null, // nombre del horario personalizado
    startShift: '07:00',// hora inicio
    endShift: '16:30',// hora fin
    timeBreak: null,
    schedule_time: null,
    schedule: null, // id del schedule/cronograma (TABLA SCHEDULES)
    idUser: null, // Id de la persona seleccionada (COLUMNA EMPLOYEE_ID DE LA TABLA SCHEDULES)
    date: date, // fecha seleccionada en el calendario
    personalized: true, // Seleccionar turno => false, Seleccionar Horario Personalizado => false, Nuevo horario personalizado =>true
    type: 1,// Solo el => 1; Resto de la actividad => 2; Rango de fechas => 3; Fechas específicos => 4
    details: [],
    loading: false,
    /* la propiedad details depende de la propiedad type, es decir:
        si la opción type es 1, en details se debe enviar la fecha (Solo el =>) ej: ['2024-03-07']
        si la opcion type es 2, en details se debe enviar el id de la actividad seleccionada (EN BASE DE DATOS ES LA TABLA TASK) ej: [7683]
        si la opcion type es 3, en details se debe enviar la fecha inicial y la fecha final (EN LA PRIMERA POSICION SIEMPRE SE DEBE MANDAR LA FECHA INICIAL), ej: ['2024-03-01','2024-03-10']
        si la opcion type es 4, en details se debe enviar las fechas seleccionadas en el calendario, no importa el orden ej: ['2024-03-01', '2024-03-10', '2024-01-01','2024-02-10']
    */
});
const editHorario = ref({
    name: null
})
const nuevoHorario = ref({})
const modhours = ref(false)
const dateSelect = ref()

function addPerson(event) {
    overlayAddPerson.value.toggle(event)
}

const togglePerson = (event, persons, task, date) => {
    dateSelect.value = date
    editHorario.value.data = task
    personsEdit.value = persons
    overlayPerson.value.toggle(event);
}

const editHour = (horario, option) => {
    Object.assign(editHorario.value, horario)
    editHorario.value.option = option
    formEditHour.value.idUser = editHorario.value.data.employee_id
    formEditHour.value.schedule = horario.idSchedule
    formEditHour.value.schedule_time = horario.idScheduleTime
    // formEditHour.value.userName = editHorario.value.data.name
    nuevoHorario.value = {}
    modhours.value = true
}
const save = async () => {
    task.value = editHorario.value.data
    formEditHour.value.loading = true
    if (tabActive.value != 2) {
        formEditHour.value.personalized = false
        formEditHour.value.startShift = new Date(nuevoHorario.value.startShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.endShift = new Date(nuevoHorario.value.endShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.timeBreak = parseFloat(nuevoHorario.value.timeBreak)
    } else {
        formEditHour.value.startShift = new Date(formEditHour.value.startShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.endShift = new Date(formEditHour.value.endShift).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        formEditHour.value.timeBreak = parseFloat(formEditHour.value.timeBreak)
    }

    if (formEditHour.value.type == 1) {
        formEditHour.value.details[0] = date.value.toISOString().split("T")[0]
    } else if (formEditHour.value.type == 2) {
        formEditHour.value.details[0] = formEditHour.value.schedule
    }
    else {
        selectDays.value.forEach((day, index) => {
            formEditHour.value.details[index] = new Date(day).toISOString().split("T")[0]
        })
    }

    await axios.post(route(editHorario.value.option != 'delete' ? 'programming.saveCustomizedSchedule' : 'programming.removeSchedule'), formEditHour.value)
        .then((res) => {
            if (res.data.status) {
                modhours.value = false
                editHorario.value.data.employees = res.data.task
                editHorario.value.data.hora_inicio = formEditHour.value.startShift
                editHorario.value.data.hora_fin = formEditHour.value.endShift
                toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
            }
            else {
                conflicts.value = Object.values(res.data.conflict)
                if (conflicts.value.length > 0) {
                    openConflict.value = true;
                    // task.value.id = schedule
                }
                // toast.add({ severity: 'danger', group: "customToast", text: res.data.mensaje, life: 2000 })
            }
            formEditHour.value.loading = false
        }).catch((error) => {
            formEditHour.value.loading = false
            console.log(error)
            toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 2000 })
        });
}
//#endregion



</script>

<template>
    <AppLayout>T
        <div class="h-full w-full flex flex-col sm:flex-row ">
            <div class="sm:w-full h-full pt-1 px-1 flex flex-col">
                <div class="sm:flex gap-1 sm:justify-between h-20 sm:h-10 items-center sm:pr-1">
                    <div class="flex w-full justify-between items-center sm:w-fit space-x-4">
                        <p class="text-xl font-bold text-primary truncate">
                            Programación de actividades
                        </p>
                        <p class="border h-min px-2 bg-primary rounded-lg text-white flex items-center">
                            {{ $page.props.auth.user.oficina }}
                        </p>
                    </div>
                    <div class="sm:flex grid grid-cols-2 items-center gap-2 sm:space-x-2">
                        <MultiSelect v-model="projectsSelected" display="chip" :options="projects" optionLabel="name"
                            class="w-56 hidden sm:flex" placeholder="Seleccione un proyecto" @change="getTask()" />
                        <Dropdown v-model="projectsSelected[0]" placeholder="Seleccione un proyecto" :options="projects"
                            optionLabel="name" @change="getTask()" class="sm:hidden flex" />
                        <ButtonGroup class="hidden sm:block">
                            <Button label="Semana" @click="getTask('week')" :outlined="mode != 'week'" />
                            <Button label="dia" @click="getTask('date')" :outlined="mode != 'date'" />
                        </ButtonGroup>
                        <div class="sm:w-52 w-full">
                            <CustomInput v-model:input="dates" :type="mode" :manualInput="false"
                                @valueChange="mode == 'week' ? (diasSemana = obtenerFechasSemana(obtenerDiaSemana(dates))) : getTask()" />
                        </div>
                    </div>
                </div>
                <!-- region calendario -->
                <div class="sm:cursor-default h-full overflow-y-auto">
                    <div v-if="mode == 'week'" class="h-full flex flex-col justify-between border rounded-md">
                        <div class="grid-cols-10 h-6 text-lg leading-6 grid pr-3 pl-2 border-b shadow-md mb-1 ">
                            <div class="flex flex-col items-center">
                                <p class="flex w-full justify-center items-baseline font-bold">Proyecto</p>
                            </div>
                            <span class="grid grid-cols-7 col-span-9">
                                <div v-for="dia, index in diasSemana" class="flex w-full flex-col items-center"
                                    :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary rounded-t-md font-bold' : '']">
                                    <p class="capitalize border-b w-full truncate text-center">{{
                                dia.toLocaleDateString('es-CO', {
                                    weekday: 'long', year: 'numeric', month: 'numeric', day:
                                        'numeric'
                                }) }}
                                    </p>
                                </div>
                            </span>
                        </div>
                        <div class="h-full space-y-2 overflow-y-auto pl-1 ">
                            <div v-if="projectsSelected.length > 0" v-for="project in projectsSelected"
                                class="grid-cols-10 ml-0.5 divide-x divide-y cursor-default h-full divide-gray-100 border border-indigo-200 rounded-l-md text-lg leading-6 grid">
                                <div class="flex flex-col items-center px-2">
                                    <div class="flex h-full w-full items-center justify-center flex-col font-bold">
                                        <p>
                                            {{ project.name }}
                                        </p>
                                        <Knob v-tooltip="'Avance'" :model-value="parseInt(project.avance)" readonly
                                            :size="40" valueTemplate="{value}%" />
                                        <div class="text-xs">
                                            <p v-tooltip="'Fecha de inicio'" class="px-2">{{ project.fechaI }}</p>
                                            <p v-tooltip="'Fecha de fin'" class="px-2">{{ project.fechaF }}</p>
                                        </div>
                                    </div>
                                </div>
                                <span class="grid grid-cols-7 col-span-9 overflow-y-auto overflow-x-hidden">
                                    <div v-for="dia, index in diasSemana" class="flex flex-col h-full items-center"
                                        :class="[index > 4 ? 'bg-warning-light' : '', dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary' : '']">
                                        <TaskProgramming :project="project.id" :day="dia"
                                            :key="dates.toDateString() + project.id" type="week" @drop="onDrop"
                                            v-model:itemDrag="personDrag" @togglePerson="togglePerson" />
                                    </div>
                                </span>

                            </div>
                            <div class="h-full" v-else>
                                <NoContentToShow subject="Seleccione uno o mas proyectos" />
                            </div>
                        </div>

                        <div class="grid-cols-10 h-8 text-sm grid pr-3 items-center pl-2 border-t shadow-lg mt-1 ">
                            <div>
                                <p class="w-full h-full truncate font-bold">
                                    Total personas
                                </p>
                            </div>
                            <div class="col-span-9 grid grid-cols-7 z-10">
                                <span v-for="dia in diasSemana">
                                    <UserStatusProgramming :date="dia" :key="dia"
                                        v-model:statusSelect="arrayPersonFilter" />
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-if="mode == 'date'"
                        class="h-full border overflow-hidden rounded-md flex flex-col justify-between">
                        <p class="sm:block hidden w-full h-6 text-center bg-primary-light font-bold">
                            Programacion del dia {{ dates.toLocaleDateString() }}
                        </p>
                        <div class="h-full sm:p-1 overflow-hidden sm:overflow-y-auto space-y-1">
                            <div v-if="projectsSelected.length > 0" v-for="project in projectsSelected"
                                class="border h-full w-full flex flex-col sm:flex-row sm:flex sm:p-1 divide-y-2 sm:divide-y-0 rounded-md hover:shadow-md ">
                                <div
                                    class="sm:w-80 h-16 sm:h-full sm:max-h-full sm:shadow-none flex items-center flex-col justify-center">
                                    <p class="font-bold">
                                        {{ project.name }}
                                    </p>
                                    <div class="text-xs sm:block flex">
                                        <p v-tooltip="'Fecha de inicio'" class="px-2">{{ project.fechaI }}</p>
                                        <p v-tooltip="'Fecha de fin'" class="px-2">{{ project.fechaF }}</p>
                                    </div>
                                    <Knob v-tooltip="'Avance'" :model-value="parseInt(project.avance)" readonly
                                        :size="40" valueTemplate="{value}%" class="sm:flex hidden" />
                                    <div class="h-6 w-1/2 sm:hidden">
                                        <ProgressBar :value="parseInt(project.avance)" class="" v-tooltip="'Avance'"
                                            :pt="{ label: 'text-xs font-thin' }">
                                        </ProgressBar>
                                    </div>
                                </div>
                                <div class="h-full sm:h-full p-1 w-full overflow-y-auto">
                                    <TaskProgramming type="day" @addPerson="addPerson" :movil="esMovil()"
                                        :project="project.id" :day="dates" :key="dates.toDateString() + project.id"
                                        @drop="onDrop" v-model:itemDrag="personDrag" @togglePerson="togglePerson" />
                                </div>
                            </div>
                            <div v-else>
                                <NoContentToShow subject="Seleccione uno o mas proyectos" />
                            </div>
                        </div>
                        <div class="w-full flex justify-center h-min z-10" oncontextmenu="return false">
                            <UserStatusProgramming :letters="true" :date="dates" :key="dates.toISOString()"
                                v-model:statusSelect="arrayPersonFilter" />
                        </div>
                    </div>
                    <!-- <div v-if="mode == 'month'" class="h-[80vh] flex flex-col justify-between"></div> -->
                </div>
            </div>
            <!--#region LISTA PERSONAL-->
            <span v-if="!esMovil()">
                <ListPersonDrag class="sm:block hidden" v-model:itemDrag="personDrag"
                    :arrayPersonFilter="arrayPersonFilter" />
            </span>
        </div>
    </AppLayout>

    <OverlayPanel ref="overlayPerson">
        <div class="flex flex-col space-y-1">
            <div class="flex flex-col border p-1 rounded-md justify-between">
                <p class="text-sm font-bold">{{ personsEdit.Nombres_Apellidos }}</p>
                <div class="grid gap-2">
                    <span v-for="schedule_time in personsEdit.times"
                        class="text-xs flex bg-success-light justify-center hover:justify-between rounded-md p-1 hover:space-x-1 group">
                        <button
                            class="rounded shadow-2xl px-1 hover:ring-1 ring-warning  hover:bg-warning-light hidden group-hover:block"
                            @click="editHour(schedule_time, 'modify')">
                            <i class="fa-solid fa-pencil text-warning"></i>
                        </button>
                        <div class="grid grid-cols-2 space-x-1">
                            <p>{{ schedule_time.horaInicio.slice(0,
                                schedule_time.horaInicio.lastIndexOf(':')) }}</p>
                            <p>{{ schedule_time.horaFin.slice(0,
                                schedule_time.horaFin.lastIndexOf(':')) }}</p>
                        </div>
                        <button @click="editHour(schedule_time, 'delete')"
                            class="rounded shadow-2xl px-1 hover:ring-1 ring-danger  hover:bg-danger-light hidden group-hover:block">
                            <i class="fa-solid fa-trash-can text-danger"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </OverlayPanel>

    <!--#region MODALES -->

    <OverlayPanel ref="overlayAddPerson" :pt="{ root: 'w-96' }">
        <Listbox v-model="personsSelect" multiple filter :options="personal" optionLabel="Nombres_Apellidos"
            class="w-full" listStyle="max-height:250px">
            <template #option="slotProps">
                <div class="flex">
                    <div>{{ slotProps.option.Nombres_Apellidos }}</div>
                </div>
            </template>
        </Listbox>
        <div class="flex w-full pt-2 px-4 space-x-2 justify-center">
            <!-- <Button label="Cancelar" severity="danger"/> -->
            <Button label="Programar" severity="success" />
        </div>
    </OverlayPanel>

    <CustomModal v-if="modhours" v-model:visible="modhours" :footer="false" icon="fa-regular fa-clock" width="60vw"
        :closeOnEscape="false"
        :titulo="editHorario?.option != 'delete' ? 'Modificar horario de ' + editHorario?.nombre : 'Eliminando a ' + editHorario?.nombre + ' de la actividad ' + editHorario?.nombreTask">
        <template #body>
            <form @submit.prevent="save" class="pb-2">
                <div v-if="editHorario?.option != 'delete'" class="flex flex-col gap-1">
                    <div class="flex items-center justify-between col-span-3 ">
                        <p class="font-bold">Horario actual:</p>
                        <p class="px-1 py-1 text-green-900 bg-green-200 rounded-md">
                            {{ format24h(editHorario.horaInicio.slice(0,
                                editHorario.horaInicio.lastIndexOf(':'))) }}
                            {{ format24h(editHorario.horaFin.slice(0,
                                editHorario.horaFin.lastIndexOf(':'))) }}
                        </p>
                    </div>
                    <TabView v-model:activeIndex="tabActive" class="border rounded-md p-1" :pt="{
                                nav: '!flex !justify-between',
                                panelContainer: '!p-1'
                            }">
                        <TabPanel header="Seleccionar Turno" :pt="{
                                root: 'w-full',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                            <CustomShiftSelector v-model:shift="nuevoHorario" />
                        </TabPanel>
                        <TabPanel header="Seleccionar Horario Personalizado" :pt="{
                                root: 'w-full',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                            <CustomShiftSelector :shiftUser="parseInt($page.props.auth.user.id)"
                                v-model:shift="nuevoHorario" />
                        </TabPanel>
                        <TabPanel header="Personalizado" :pt="{
                                root: 'w-full',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                            <div class="h-52 space-y-2 m-1 mt-3">
                                <span class="flex gap-2 items-center">
                                    <label class="-mb-0.5" for="switch1">Guardar en personalizados?</label>
                                    <InputSwitch inputId="switch1" v-model="formEditHour.personalized" />
                                </span>
                                <CustomInput v-if="formEditHour.personalized" v-model:input="formEditHour.timeName"
                                    label="Nombre" type="text" id="name" placeholder="Nombre del horario"
                                    :required="tabActive == 2" />
                                <span class="grid grid-cols-3 gap-x-1">
                                    <CustomInput v-model:input="formEditHour.startShift" label="Hora inicio" type="time"
                                        :stepMinute="30" id="start" placeholder="Hora de inicio"
                                        :required="tabActive === 2" />
                                    <CustomInput v-model:input="formEditHour.endShift" label="Hora fin" type="time"
                                        id="end" :stepMinute="30" placeholder="Hora de fin"
                                        :required="tabActive === 2" />
                                    <CustomInput v-model:input="formEditHour.timeBreak" label="Descanso" type="number"
                                        suffix=" Hora" id="break" placeholder="Descanso en horas"
                                        :required="tabActive === 2" />
                                </span>
                            </div>
                        </TabPanel>
                    </TabView>
                </div>
                <p class="font-bold text-xl">Aplicar por:</p>
                <div class="flex items-center p-2 gap-4">
                    <div v-for="category in [
                                { name: 'Solo el ' + dateSelect.toISOString().split('T')[0], key: 1 },
                                { name: 'Resto de la actividad', key: 2 },
                                // { name: 'Rango de fechas', key: 3 },
                                { name: 'Fechas específicas', key: 4 }
                            ]" :key="category.key" class="flex items-center">
                        <RadioButton v-model="formEditHour.type" :inputId="category.key + category.name" name="dynamic"
                            :value="category.key" @click="formEditHour.details = []" />
                        <label :for="category.key" class="ml-1 mb-0">{{ category.name }}</label>
                    </div>
                    <Calendar v-if="formEditHour.type == 3 || formEditHour.type == 4" show-icon v-model="selectDays"
                        class="col-span-3" :selectionMode="'multiple'" :min-date="date" :manualInput="false" :pt="{
                                input: '!h-8'
                            }" />
                </div>
                <!-- {{editHorario?.option != 'delete'?tabActive != 2? nuevoHorario?.startShift!=null?false:true:false:false }} -->
                <span class="w-full grid grid-cols-4 justify-end gap-5 items-center">
                    <Button type="submit" class="col-start-4" :loading="formEditHour.loading"
                        :disabled="(editHorario?.option !== 'delete') && (tabActive !== 2) && (nuevoHorario?.startShift !== null)"
                        :severity="editHorario?.option != 'delete' ? 'success' : 'danger'"
                        :icon="editHorario?.option != 'delete' ? 'fa-solid fa-floppy-disk' : 'fa-solid fa-trash-can'"
                        :label="editHorario?.option != 'delete' ? 'Guardar' : 'Eliminar'" />
                </span>
            </form>
        </template>
    </CustomModal>

    <ModalColisions v-model:visible="openConflict" v-model:conflicts="conflicts" v-model:task="task">
    </ModalColisions>

    <!--#endregion-->
</template>