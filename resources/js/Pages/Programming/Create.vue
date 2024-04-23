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
import ContextMenu from 'primevue/contextmenu';
import { useConfirm } from "primevue/useconfirm";
const confirm = useConfirm();
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
const mode = ref('date')
const dates = ref(new Date(date.value.getFullYear(), date.value.getMonth(), date.value.getDate() + 1))
const diasSemana = ref(obtenerFechasSemana(dates.value))
const projectsSelected = ref([])
const overlayPerson = ref()
const overlayAddPerson = ref()
const personsEdit = ref()
const tabActive = ref(0)
const personal = ref()
const personDrag = ref([])
const arrayPersonFilter = ref({
    loading: false,
    data: {
        programados: [],
        noProgramados: []
    }
})
const personsSelect = ref()
const menu = ref();


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

async function onDrop(task, fecha) {
    if (new Date(fecha) >= new Date(date.value.toISOString().split("T")[0])) {
        task.loading = true
        if (personDrag.value.option == 'move') {
            personDrag.value.task.loading = true
            await axios.post(route('programming.move'), { task: task.id, date: fecha, schedule: personDrag.value.times[0].idSchedule }).then((res) => {
                if (res.data.status == false) {
                    task.loading = false
                    personDrag.value.task.loading = false
                    toast.add({ severity: 'error', group: "customToast", text: res.data.mensaje, life: 2000 })
                } else {
                    task.employees = res.data.task
                    task.loading = false
                    personDrag.value.task.loading = false
                    personDrag.value.task.employees = personDrag.value.task.employees.filter(person => person.Num_SAP !== personDrag.value.Num_SAP);
                    toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
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
                    toast.add({ severity: 'error', group: "customToast", text: 'Persona ya programada', life: 2000 })
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


const scheduleTime = ref({})

const modhours = ref(false)
const dateSelect = ref()
const taskEdit = ref()

function addPerson(event) {
    overlayAddPerson.value.toggle(event)
}

const togglePerson = (event, persons, task, date) => {
    dateSelect.value = date
    taskEdit.value = task
    personsEdit.value = persons
    overlayPerson.value.toggle(event);
}


const formEditShift = ref({
    startShift: '07:00',// hora inicio
    endShift: '16:30',// hora fin
    timeBreak: 0,
    personalized: false, // Seleccionar turno => false, Seleccionar Horario Personalizado => false, Nuevo horario personalizado =>true
    timeName: null, // nombre del horario personalizado
    schedule_time: null,
    //control formulario
    loading: false
})

const shiftSelect = ref({})
const editHour = (schedule_time) => {
    scheduleTime.value = schedule_time
    console.log(taskEdit.value)
    modhours.value = true
}

const save = async (mode) => {
    formEditShift.value.loading = true
    if (tabActive.value == 0) {
        formEditShift.value.personalized = false
        if (shiftSelect.value.startShift == undefined) {
            formEditShift.value.loading = false
            toast.add({ severity: 'error', group: "customToast", text: 'Seleccione un horario', life: 2000 })
            return
        }
        formEditShift.value.startShift = format24h(shiftSelect.value.startShift)
        formEditShift.value.endShift = format24h(shiftSelect.value.endShift)
        formEditShift.value.timeBreak = parseFloat(shiftSelect.value.timeBreak)
    } else if (tabActive.value == 1) {
        if (mode == 'custom') {
            formEditShift.value.startShift = format24h(formEditShift.value.startShift)
            formEditShift.value.endShift = format24h(formEditShift.value.endShift)
            formEditShift.value.timeBreak = parseFloat(formEditShift.value.timeBreak)
        } else if (shiftSelect.value.startShift == undefined) {
            formEditShift.value.loading = false
            toast.add({ severity: 'error', group: "customToast", text: 'Seleccione un horario', life: 2000 })
            return
        } else {
            formEditShift.value.startShift = format24h(shiftSelect.value.startShift)
            formEditShift.value.endShift = format24h(shiftSelect.value.endShift)
            formEditShift.value.timeBreak = parseFloat(shiftSelect.value.timeBreak)
        }
    }
    formEditShift.value.schedule_time = scheduleTime.value.idScheduleTime

    await axios.post(route('programming.saveCustomizedSchedule'), formEditShift.value)
        .then((res) => {
            if (res.data.status) {
                taskEdit.value.employees = res.data.task
                toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                modhours.value = false
            }
            else {
                conflicts.value = Object.values(res.data.conflict)
                if (conflicts.value.length > 0) {
                    openConflict.value = true;
                }
                toast.add({ severity: 'danger', group: "customToast", text: res.data.mensaje, life: 2000 })
            }
        }).catch((error) => {
            console.log(error)
            toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 2000 })
        });
    formEditShift.value.loading = false
}

const confirmDelete = (event, schedule_time) => {
    // console.log(personsEdit.value)
    confirm.require({
        target: event.currentTarget,
        message: {
            message: '¿Está totalmente seguro?',
            subMessage: 'No se puede deshacer'
        },
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-success',
        rejectLabel: 'No',
        acceptLabel: 'Sí',
        accept: async () => {
            taskEdit.value.loading = true
            await axios.post(route('programming.removeSchedule'), { schedule: schedule_time.idSchedule, type: 1 }).then((res) => {
                if (res.data.status) {
                    taskEdit.value.employees = res.data.task
                    if (!(taskEdit.value.employees.some(empleado => empleado.NumSAP === schedule_time.idUsuario))) {
                        overlayPerson.value.hide()
                    }
                    toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                }
                else {
                    conflicts.value = Object.values(res.data.conflict)
                    if (conflicts.value.length > 0) {
                        openConflict.value = true;
                    }
                }
                taskEdit.value.loading = false
            }).catch((error) => {
                console.log(error)
                toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 2000 })
            })
            taskEdit.value.loading = false
        }, reject: () => {

        }
    })
}

//#endregion

//#region click derecho
var tempRightClick = {}
const taskRightClick = (event, task, day) => {
    tempRightClick.taskData = task
    tempRightClick.task = task.id
    tempRightClick.day = day
    if (dataRightClick.task != undefined) {
        // console.log('datos')
        items.value[3].visible = true
    } else {
        items.value[3].visible = false
    }
    if (task.employees.length == 0) {
        items.value[0].disabled = true
        items.value[1].disabled = true
        items.value[2].disabled = true
        items.value[4].disabled = true
    } else {
        items.value[0].disabled = false
        items.value[1].disabled = false
        items.value[2].disabled = false
        items.value[4].disabled = false
    }
    menu.value.show(event)
};

var dataRightClick = {}

const items = ref([
    {
        label: 'Copiar',
        icon: 'fa-regular fa-copy text-success',
        tooltip: 'Copia las personas programadas',
        command: () => {
            dataRightClick.taskData = tempRightClick.taskData
            dataRightClick.task = tempRightClick.task
            dataRightClick.date = tempRightClick.day
            dataRightClick.cut = false
            console.log('Copia');
        }
    },
    {
        label: 'Copiar al dia siguiente',
        icon: 'fa-regular fa-copy text-primary',
        tooltip: 'Copia las personas programadas y las pega el dia siguiente',
        visible: mode == 'week',
        command: () => {
            console.log('Copia al dia siguiente');
        },
    },
    {
        label: 'Cortar',
        icon: 'fa-solid fa-scissors text-warning',
        tooltip: 'Corta las personas programadas',
        command: () => {
            dataRightClick.taskData = tempRightClick.taskData
            dataRightClick.task = tempRightClick.task
            dataRightClick.date = tempRightClick.day
            dataRightClick.cut = true
            console.log('Corta');
        }
    },
    {
        label: 'Pegar',
        icon: 'fa-regular fa-paste text-info',
        tooltip: 'Pega las personas copiadas o cortadas anteriormente',
        command: async () => {
            dataRightClick.newTask = tempRightClick.task
            dataRightClick.newDate = tempRightClick.day
            dataRightClick.newTaskData = tempRightClick.taskData
            await axios.post(route('programming.copy'), dataRightClick).then((res) => {
                console.log(res)
                if (res.data.status) {
                    if (res.data.task) {
                        dataRightClick.newTaskData.employees = res.data.task
                    } else {
                        toast.add({ severity: 'error', group: "customToast", text: 'Error al cargar la tarea', life: 4000 })
                    }
                    if (dataRightClick.cut) {
                        // console.log(dataRightClick.taskData)
                        dataRightClick.taskData.employees = []
                    }
                    toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                } else {
                    toast.add({ severity: 'error', group: "customToast", text: res.data.mensaje, life: 2000 })
                }
            })
            console.log('Pega');
        },
    },
    {
        label: 'Quitar todos',
        icon: 'fa-solid fa-trash text-danger',
        tooltip: 'Elimina todas las personas programadas de la tarea',
        command: async () => {
            const deleteSchedule = tempRightClick.taskData.employees.map(item => item.schedule)
            await axios.post(route('programming.removeAll'), deleteSchedule).then((res) => {
                console.log(res)
                if (res.data.status) {
                    dataRightClick.taskData.employees = []
                    toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                } else {
                    toast.add({ severity: 'error', group: "customToast", text: res.data.mensaje, life: 2000 })
                }
            })
            console.log('Desprograma');
        }
    },
]);

//#endregion
</script>

<template>
    <AppLayout>
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
                                        <TaskProgramming :project="project.id" :day="dia" @menu="taskRightClick"
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
                                        @menu="taskRightClick" :project="project.id" :day="dates"
                                        :key="dates.toDateString() + project.id" @drop="onDrop"
                                        v-model:itemDrag="personDrag" @togglePerson="togglePerson" />
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
                        class="text-xs flex bg-success-light justify-center space-x-3 rounded-md p-1">
                        <button class="rounded shadow-2xl px-1 hover:ring-1 ring-warning  hover:bg-warning-light"
                            @click="editHour(schedule_time)">
                            <i class="fa-solid fa-pencil text-warning"></i>
                        </button>
                        <div class="grid grid-cols-2 space-x-1">
                            <p>{{ schedule_time.horaInicio.slice(0,
                                schedule_time.horaInicio.lastIndexOf(':')) }}</p>
                            <p>{{ schedule_time.horaFin.slice(0,
                                schedule_time.horaFin.lastIndexOf(':')) }}</p>
                        </div>
                        <button @click="confirmDelete($event, schedule_time)"
                            class="rounded shadow-2xl px-1 hover:ring-1 ring-danger  hover:bg-danger-light">
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
        :closeOnEscape="false" :titulo="'Modificar horario de ' + scheduleTime?.nombre">
        <template #body>
            <!-- {{ tabActive }} -->
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between col-span-3 ">
                    <p class="font-bold">Horario actual:</p>
                    <p class="px-1 py-1 text-green-900 bg-green-200 rounded-md">
                        <!-- {{ scheduleTime }} -->
                        {{ format24h(scheduleTime.horaInicio.slice(0,
                                scheduleTime.horaInicio.lastIndexOf(':'))) }}
                        {{ format24h(scheduleTime.horaFin.slice(0,
                                scheduleTime.horaFin.lastIndexOf(':'))) }}
                    </p>
                </div>

                <TabView v-model:activeIndex="tabActive" class="border rounded-md p-1 mb-2" :pt="{
                                nav: '!flex !justify-between',
                                panelContainer: '!p-1'
                            }">
                    <TabPanel header="Seleccionar turno predefinido" :pt="{
                                root: 'w-full',
                                headerAction: tabActive == 0 ? 'bg-primary text-white' : '',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                        <div class="">
                            <CustomShiftSelector v-model:shift="shiftSelect" />
                        </div>
                        <span class="w-full flex justify-end pt-3 items-center">
                            <Button v-if="tabActive == 0" @click="save()" label="Guardar" icon="fa-solid fa-floppy-disk"
                                severity="success" :loading="formEditShift.loading" />
                        </span>
                    </TabPanel>
                    <TabPanel header="Seleccionar turno Personalizado" :pt="{
                                root: 'w-full',
                                headerAction: tabActive == 1 ? 'bg-primary text-white' : '',
                                headerTitle: '!w-full !flex !justify-center',
                            }">
                        <div class="h-80 grid grid-cols-2 gap-3">
                            <div class="border w-full flex flex-col justify-between p-2 rounded-md space-y-3">
                                <CustomShiftSelector :shiftUser="parseInt($page.props.auth.user.id)"
                                    v-model:shift="shiftSelect" />
                                <div class="flex justify-end">
                                    <Button @click="save()" label="Guardar" icon="fa-solid fa-floppy-disk"
                                        severity="success" :loading="formEditShift.loading" />
                                </div>
                            </div>
                            <form @submit.prevent="save('custom')"
                                class="border w-full flex flex-col justify-between p-2 rounded-md space-y-3">
                                <label class="text-center w-full font-bold text-gray-900">
                                    Personalizar turno</label>
                                <span class="grid grid-cols-2 gap-2">
                                    <CustomInput label="Hora inicio" type="time"
                                        v-model:input="formEditShift.startShift" :stepMinute="30" id="start"
                                        placeholder="Hora de inicio" :required="true" />
                                    <CustomInput label="Hora fin" type="time" id="end"
                                        v-model:input="formEditShift.endShift" :stepMinute="30"
                                        placeholder="Hora de fin" :required="true" />
                                    <CustomInput label="Descanso" type="number" v-model:input="formEditShift.timeBreak"
                                        suffix=" Hora" id="break" placeholder="Descanso en horas" :required="true" />
                                    <div class="flex flex-col items-center">
                                        <label class="w-full text-center" for="switch1">¿Guardar?</label>
                                        <InputSwitch inputId="switch1" v-model="formEditShift.personalized" />
                                    </div>
                                    <CustomInput v-if="formEditShift.personalized" class="col-span-2"
                                        v-model:input="formEditShift.timeName" label="Nombre" type="text" id="name"
                                        placeholder="Nombre del horario" :required="true" />
                                </span>
                                <div class="flex justify-end w-full">
                                    <Button type="submit"
                                        :label="formEditShift.personalized ? 'Guardar turno y aplicar' : 'Aplicar'"
                                        icon="fa-solid fa-floppy-disk" severity="success"
                                        :loading="formEditShift.loading" />
                                </div>
                            </form>
                        </div>
                    </TabPanel>
                </TabView>
            </div>
        </template>
    </CustomModal>

    <!-- <ModalColisions v-model:visible="openConflict" v-model:conflicts="conflicts" v-model:task="taskEdit">
    </ModalColisions> -->

    <!--#endregion-->
    <ContextMenu ref="menu" :model="items">
        <template #item="{ item, props }">
            <!-- {{ console.log(props) }} -->
            <a v-ripple v-tooltip="item.tooltip" class="flex items-center" v-bind="props.action">
                <span :class="item.icon" />
                <span class="ml-2">{{ item.label }}</span>
            </a>
        </template>
    </ContextMenu>
</template>