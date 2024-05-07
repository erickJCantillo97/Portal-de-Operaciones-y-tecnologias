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
import { useToast } from "primevue/usetoast";
import TaskProgramming from './Components/TaskProgramming.vue';
import Knob from 'primevue/knob';
import Dropdown from 'primevue/dropdown';
import Listbox from 'primevue/listbox';
import UserStatusProgramming from '@/Components/sections/UserStatusProgramming.vue';
import ContextMenu from 'primevue/contextmenu';
import { useConfirm } from "primevue/useconfirm";
// import CustomOverlayConfig from '@/Components/CustomOverlayConfig.vue';
const confirm = useConfirm();
const toast = useToast();
// const { hasRole, hasPermission } = usePermissions()

defineProps({
    projects: Array
})

// #region funciones basicas
function format24h(hora) {

    try {
        if (hora instanceof Date) {
            return hora.toLocaleString('es-CO',
                { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
        } else if (hora.length > 6) {
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
        fechasSemana.push({ key: Math.random().toFixed(5), day: new Date(fechaSeleccionada) });
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
const conflicts = ref({})
const mode = ref('date')
const dates = ref({ key: Math.random().toFixed(5), day: new Date(date.value.getFullYear(), date.value.getMonth(), date.value.getDate() + 1) })
const diasSemana = ref(obtenerFechasSemana(dates.value.day))
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
        if (dates.value.day instanceof Date) {
            dates.value.day = obtenerFormatoSemana(new Date())
        }
        diasSemana.value = obtenerFechasSemana(obtenerDiaSemana(dates.value.day))
    } else if (option == 'date') {
        mode.value = option
        if (!(dates.value.day instanceof Date)) {
            dates.value == {}
            dates.value.key = Math.random().toFixed(5)
            dates.value.day = new Date()
        }
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

function loadingPrograming(fecha) {
    if (fecha != undefined) {
        if (fecha instanceof Array) {
            fecha.forEach((dia) => {
                if (mode.value == 'week') diasSemana.value.find(data => data.day.toISOString().split("T")[0] === dia.toISOString().split("T")[0]).key = Math.random().toFixed(5);
            })
        } else {
            if (mode.value == 'week') diasSemana.value.find(data => data.day.toISOString().split("T")[0] === fecha.toISOString().split("T")[0]).key = Math.random().toFixed(5);
        }
    }
    if (mode.value == 'date') dates.value.key = Math.random().toFixed(5);
    arrayPersonFilter.value.data.programados = []
    arrayPersonFilter.value.data.noProgramados = []
}

async function onDrop(task, fecha) {
    if (new Date(fecha) >= new Date(date.value.toISOString().split("T")[0])) {
        task.loading == undefined ? task.loading = 1 : task.loading++
        if (personDrag.value.option == 'move') {
            if (personDrag.value.day.toDateString() == fecha.toDateString()) {
                if (personDrag.value.task.id != task.id) {
                    personDrag.value.task.loading == undefined ? personDrag.value.task.loading = 1 : personDrag.value.task.loading++
                    await axios.post(route('programming.move'), { task: task.id, date: fecha, schedule: personDrag.value.person.schedule })
                        .then((res) => {
                            if (res.data.status) {
                                task.employees = res.data.task
                                personDrag.value.task.employees = personDrag.value.task.employees.filter(person => person.Num_SAP !== personDrag.value.person.Num_SAP);
                                toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                            } else {
                                if (Object.values(res.data.conflict).length > 0) {
                                    conflicts.value.data = Object.values(res.data.conflict)
                                    conflicts.value.task = task
                                    openConflict.value = true;
                                    toast.add({ severity: 'error', group: "customToast", text: 'Existe sobreasignación', life: 2000 })
                                } else {
                                    toast.add({ severity: 'error', group: "customToast", text: res.data?.mensaje ?? 'Hubo un error al programar', life: 2000 })
                                }
                            }
                            loadingPrograming(fecha)
                            task.loading--
                            personDrag.value.task.loading--
                        })
                        .catch((error) => {
                            console.log(error)
                            task.employees = res.data.task
                            task.loading--
                        })
                } else {
                    task.loading--
                    toast.add({ severity: 'success', group: "customToast", text: 'Persona esta programada aqui', life: 2000 })
                }
            } else {
                task.loading--
                toast.add({ severity: 'error', group: "customToast", text: 'No se puede mover a otra fecha, use copiar', life: 2000 })
            }
        } else {
            await axios.post(route('programming.store'), { task_id: task.id, employee_id: personDrag.value.Num_SAP, name: personDrag.value.Nombres_Apellidos, fecha })
                .then((res) => {
                    if (res.data.status) {
                        toast.add({ severity: 'success', group: "customToast", text: 'Persona programada', life: 2000 })
                    } else {
                        if (Object.values(res.data.conflict).length > 0) {
                            conflicts.value.data = Object.values(res.data.conflict)
                            conflicts.value.task = task
                            openConflict.value = true;
                            toast.add({ severity: 'error', group: "customToast", text: 'Existe sobreasignación', life: 2000 })
                        } else {
                            toast.add({ severity: 'error', group: "customToast", text: res.data?.mensaje ?? 'Hubo un error al programar', life: 2000 })
                        }
                    }
                    loadingPrograming(fecha)
                    task.employees = res.data.task
                    task.loading--
                })
                .catch((error) => {
                    console.log(error)
                    task.employees = res.data.task
                    task.loading--
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
    //control formulario
    loading: false
})

const editHour = (schedule_time) => {
    scheduleTime.value = schedule_time
    modhours.value = true
}

const saveCustomizedSchedule = async () => {
    await axios.post(route('programming.saveCustomizedSchedule'), {
        startShift: format24h(formEditShift.value.startShift),
        endShift: format24h(formEditShift.value.endShift),
        schedule_time:scheduleTime.value.id
    })
        .then((res) => {
            if (res.data.status) {
                taskEdit.value.employees = res.data.task
                toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                modhours.value = false
            }
            else {
                if (Object.values(res.data.conflict).length > 0) {
                    conflicts.value.data = Object.values(res.data.conflict)
                    conflicts.value.task = taskEdit.value
                    openConflict.value = true;
                    toast.add({ severity: 'error', group: "customToast", text: 'Existe sobreasignación', life: 2000 })
                } else {
                    toast.add({ severity: 'error', group: "customToast", text: res.data?.mensaje ?? 'Hubo un error al programar', life: 2000 })
                }
            }
            loadingPrograming(dateSelect.value)
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
            taskEdit.value.loading == undefined ? taskEdit.value.loading = 1 : taskEdit.value.loading++
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
                loadingPrograming(dateSelect.value)
            }).catch((error) => {
                console.log(error)
                toast.add({ severity: 'error', group: "customToast", text: 'Error no controlado', life: 2000 })
            })
            taskEdit.value.loading--
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
        itemsMenuContext.value[3].visible = true
    } else {
        itemsMenuContext.value[3].visible = false
    }
    if (task.employees.length == 0) {
        itemsMenuContext.value[0].disabled = true
        itemsMenuContext.value[1].disabled = true
        itemsMenuContext.value[2].disabled = true
        itemsMenuContext.value[4].disabled = true
    } else {
        itemsMenuContext.value[0].disabled = false
        itemsMenuContext.value[1].disabled = false
        itemsMenuContext.value[2].disabled = false
        itemsMenuContext.value[4].disabled = false
    }
    menu.value.show(event)
};

var dataRightClick = {}

const itemsMenuContext = ref([
    {
        label: 'Copiar',
        icon: 'fa-regular fa-copy text-success',
        tooltip: 'Copia las personas programadas',
        command: () => {
            dataRightClick.taskData = tempRightClick.taskData
            dataRightClick.task = tempRightClick.task
            dataRightClick.date = tempRightClick.day
            dataRightClick.cut = false
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
            tempRightClick.taskData.loading == undefined ? tempRightClick.taskData.loading = 1 : tempRightClick.taskData.loading++
            dataRightClick.taskData.loading == undefined ? dataRightClick.taskData.loading = 1 : dataRightClick.taskData.loading++
            await axios.post(route('programming.copy'), dataRightClick)
                .then((res) => {
                    if (res.data.status) {
                        if (res.data.task) {
                            dataRightClick.newTaskData.employees = res.data.task
                        } else {
                            toast.add({ severity: 'error', group: "customToast", text: 'Error al cargar la tarea', life: 4000 })
                        }
                        if (dataRightClick.cut) {
                            dataRightClick.taskData.employees = []
                        }
                        toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                    } else {
                        if (Object.values(res.data.conflict)?.length > 0) {
                            conflicts.value.data = Object.values(res.data.conflict)
                            conflicts.value.task = dataRightClick.newTaskData
                            openConflict.value = true;
                            toast.add({ severity: 'error', group: "customToast", text: 'Existe sobreasignación', life: 2000 })
                        } else {
                            toast.add({ severity: 'error', group: "customToast", text: res.data?.mensaje ?? 'Hubo un error al programar', life: 2000 })
                        }
                    }
                    loadingPrograming([dataRightClick.date, dataRightClick.newDate])
                    tempRightClick.taskData.loading--
                    dataRightClick.taskData.loading--
                })
                .catch((error) => {
                    tempRightClick.taskData.loading--
                    dataRightClick.taskData.loading--
                    console.log(error)
                })
        },
    },
    {
        label: 'Quitar todos',
        icon: 'fa-solid fa-trash text-danger',
        tooltip: 'Elimina todas las personas programadas de la tarea',
        command: async () => {
            const deleteSchedule = tempRightClick.taskData.employees.map(item => item.schedule)
            tempRightClick.taskData.loading == undefined ? tempRightClick.taskData.loading = 1 : tempRightClick.taskData.loading++
            await axios.post(route('programming.removeAll'), { schedules: deleteSchedule })
                .then((res) => {
                    if (res.data.status) {
                        tempRightClick.taskData.employees = []
                        toast.add({ severity: 'success', group: "customToast", text: res.data.mensaje, life: 2000 })
                    } else {
                        toast.add({ severity: 'error', group: "customToast", text: res.data.mensaje, life: 2000 })
                    }
                    tempRightClick.taskData.loading--
                    loadingPrograming(tempRightClick.day)
                })
                .catch((error) => {
                    tempRightClick.taskData.loading--
                    console.log(error)
                })
        }
    },
]);

//#endregion

const optionsConfig = ref([
    {
        field: 'progressProgramming',
        label: 'Ver avance',
        type: 'boolean',
        default:true
    },
    {
        field: 'daysLateProgramming',
        label: 'Ver retraso',
        type: 'boolean',
        default:true
    },
    {
        field: 'dateEndProgramming',
        label: 'Ver fecha final',
        type: 'boolean',
        default:true
    },
    {
        field: 'shiftProgramming',
        label: 'Ver horario',
        type: 'boolean',
        default:true
    },
    {
        field: 'showPersonProgramming',
        label: 'Mostrar personas',
        type: 'boolean',
        default:true
    },
    {
        field: 'showProjectProgramming',
        label: 'Mostrar proyectos',
        type: 'boolean',
        default:true
    },
    {
        field: 'colummnsProgramming',
        label: 'Cantidad de columnas',
        type: 'options',
        options: ['1', '2', '3', '4', '5', '6'],
        default:'1'
    },
    {
        field: 'typeProgramming',
        label: 'Tipo de programacion',
        type: 'options',
        options: ['Diario', 'Semanal', 'Fin de actividad'],
        default:'Diario'
    },

])
const optionsData = ref({})
</script>

<template>
    <AppLayout :optionsConfig v-model:optionsData="optionsData">
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
                            <CustomInput v-model:input="dates.day" :type="mode" :manualInput="false"
                                @valueChange="getTask()" />
                        </div>
                    </div>
                    <!-- <CustomOverlayConfig :options="optionsConfig" v-model:optionsData="optionsData" /> -->
                </div>
                <!-- region calendario -->
                <div class="sm:cursor-default h-full overflow-y-auto">
                    <div v-if="mode == 'week'" class="h-full flex flex-col justify-between border rounded-md">
                        <div class="grid-cols-10 h-6 text-lg leading-6 grid pr-3 pl-2 border-b shadow-md mb-1 ">
                            <div v-if="optionsData.showProjectProgramming" class="flex flex-col items-center">
                                <p class="flex w-full justify-center items-baseline font-bold">Proyecto</p>
                            </div>
                            <span class="grid grid-cols-7 pr-4"
                                :class="optionsData.showProjectProgramming ? 'col-span-9' : 'col-span-10'">
                                <div v-for="data, index in diasSemana" class="flex w-full flex-col items-center"
                                    :class="[data.day.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary rounded-t-md font-bold' : '']">
                                    <p class="capitalize border-b w-full truncate text-center" :key="data.key">{{
                                data.day.toLocaleDateString('es-CO', {
                                    weekday: 'long', year: 'numeric', month: 'numeric', day:
                                        'numeric'
                                }) }}
                                    </p>
                                </div>
                            </span>
                        </div>
                        <div v-if="projectsSelected.length > 0" class="h-full space-y-2 overflow-y-scroll pl-1 snap-mandatory snap-y">
                            <div v-for="project in projectsSelected"
                                class="snap-start grid-cols-10 ml-0.5 ursor-default h-full  border-indigo-200 rounded-l-md text-lg leading-6 grid">
                                <div v-if="optionsData.showProjectProgramming" class="flex flex-col items-center px-2">
                                    <div class="flex h-full w-full items-center justify-center flex-col font-bold">
                                        <p>
                                            {{ project.name }}
                                        </p>
                                        <Knob v-if="project.name != 'ANRP'" v-tooltip="'Avance'"
                                            :model-value="parseInt(project.avance)" readonly :size="40"
                                            valueTemplate="{value}%" />
                                        <div v-if="project.name != 'ANRP'" class="text-xs">
                                            <p v-tooltip="'Fecha de inicio'" class="px-2">{{ project.fechaI }}</p>
                                            <p v-tooltip="'Fecha de fin'" class="px-2">{{ project.fechaF }}</p>
                                        </div>
                                    </div>
                                </div>
                                <span class="grid grid-cols-7 overflow-y-scroll overflow-x-hidden"
                                    :class="optionsData.showProjectProgramming ? 'col-span-9' : 'col-span-10'">
                                    <div v-for="data, index in diasSemana" class="flex flex-col h-full items-center"
                                        :class="[index > 5 ? 'bg-warning-light' : '', data.day.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary' : '']">
                                        <TaskProgramming :project="project.id" :day="data.day" @menu="taskRightClick"
                                            :key="dates.day + project.id + mode" type="week" @drop="onDrop"
                                            v-model:itemDrag="personDrag" @togglePerson="togglePerson" :dataRightClick
                                            :optionsData />
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="" v-else>
                            <NoContentToShow subject="Seleccione uno o mas proyectos" />
                        </div>
                        <div class="grid-cols-10 h-8 text-sm grid pr-3 items-center pl-2 border-t shadow-lg mt-1 ">
                            <div v-if="optionsData.showProjectProgramming">
                                <p class="w-full h-full truncate font-bold">
                                    Total personas
                                </p>
                            </div>
                            <div class=" grid grid-cols-7 pr-4 z-10"
                                :class="optionsData.showProjectProgramming ? 'col-span-9' : 'col-span-10'">
                                <span v-for="data in diasSemana">
                                    <UserStatusProgramming :date="data.day" :key="data.key + data.day"
                                        v-model:statusSelect="arrayPersonFilter" />
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-if="mode == 'date'"
                        class="h-full border overflow-hidden rounded-md flex flex-col justify-between">
                        <p class="sm:block hidden w-full h-6 text-center bg-primary-light font-bold" :key="dates.key">
                            Programacion del dia {{ dates.day.toLocaleDateString() }}
                        </p>
                        <div class="h-full sm:p-1 overflow-hidden sm:overflow-y-auto space-y-1">
                            <div v-if="projectsSelected.length > 0" v-for="project in projectsSelected"
                                class="border h-full w-full flex flex-col sm:flex-row sm:flex sm:p-1 divide-y-2 sm:divide-y-0 rounded-md hover:shadow-md ">
                                <div v-if="optionsData.showProjectProgramming"
                                    class="sm:w-40 h-16 sm:h-full sm:max-h-full sm:shadow-none flex items-center flex-col justify-center">
                                    <p class="font-bold">
                                        {{ project.name }}
                                    </p>
                                    <div v-if="project.name != 'ANRP'" class="text-xs sm:block flex">
                                        <p v-tooltip="'Fecha de inicio'" class="px-2">{{ project.fechaI }}</p>
                                        <p v-tooltip="'Fecha de fin'" class="px-2">{{ project.fechaF }}</p>
                                    </div>
                                    <Knob v-if="project.name != 'ANRP'" v-tooltip="'Avance'"
                                        :model-value="parseInt(project.avance)" readonly :size="40"
                                        valueTemplate="{value}%" class="sm:flex hidden" />
                                    <div class="h-6 w-1/2 sm:hidden">
                                        <ProgressBar v-if="project.name != 'ANRP'" :value="parseInt(project.avance)"
                                            class="" v-tooltip="'Avance'" :pt="{ label: 'text-xs font-thin' }">
                                        </ProgressBar>
                                    </div>
                                </div>
                                <div class="h-full sm:h-full p-1 w-full overflow-y-auto">
                                    <TaskProgramming type="day" @addPerson="addPerson" :movil="esMovil()"
                                        @menu="taskRightClick" :project="project.id" :day="dates.day"
                                        :key="dates.day.toDateString() + project.id" @drop="onDrop" :optionsData
                                        v-model:itemDrag="personDrag" @togglePerson="togglePerson" :dataRightClick />
                                </div>
                            </div>
                            <div v-else>
                                <NoContentToShow subject="Seleccione uno o mas proyectos" />
                            </div>
                        </div>
                        <div class="w-full flex justify-center h-min z-10" oncontextmenu="return false">
                            <UserStatusProgramming :letters="true" :date="dates.day" :key="dates.key + dates.day"
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

    <CustomModal v-if="modhours" v-model:visible="modhours" icon="fa-regular fa-clock" width="30vw"
        :closeOnEscape="false" titulo="Modificar horario">
        <template #body>
            <div class="flex flex-col gap-1">
                <p class="font-bold text-center">{{ scheduleTime?.nombre }}</p>
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
                <form @submit.prevent="" class="border w-full flex flex-col justify-between p-2 rounded-md space-y-3">
                    <label class="text-center w-full font-bold text-gray-900">
                        Personalizar turno</label>
                    <span class="grid grid-cols-2 gap-2">
                        <CustomInput label="Hora inicio" type="time" v-model:input="formEditShift.startShift"
                            :stepMinute="30" id="start" placeholder="Hora de inicio" :required="true" />
                        <CustomInput label="Hora fin" type="time" id="end" v-model:input="formEditShift.endShift"
                            :stepMinute="30" placeholder="Hora de fin" :required="true" />
                        <CustomInput label="Descanso" type="number" v-model:input="formEditShift.timeBreak"
                            suffix=" Hora" id="break" placeholder="Descanso en horas" :required="true" />
                    </span>
                    <div class="flex justify-end w-full">
                    </div>
                </form>
            </div>
        </template>
        <template #footer>
            <Button @click="saveCustomizedSchedule('custom')"
                :label="formEditShift.personalized ? 'Guardar turno y aplicar' : 'Aplicar'"
                icon="fa-solid fa-floppy-disk" severity="success" :loading="formEditShift.loading" />
        </template>
    </CustomModal>

    <ModalColisions v-model:visible="openConflict" v-model:conflicts="conflicts">
    </ModalColisions>

    <!--#endregion-->
    <ContextMenu ref="menu" :model="itemsMenuContext">
        <template #item="{ item, props }">
            <!-- {{ console.log(props) }} -->
            <a v-ripple v-tooltip="item.tooltip" class="flex items-center" v-bind="props.action">
                <span :class="item.icon" />
                <span class="ml-2">{{ item.label }}</span>
            </a>
        </template>
    </ContextMenu>
</template>