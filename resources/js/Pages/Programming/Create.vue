<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { Container, Draggable } from "vue-dndrop";
import Loading from '@/Components/Loading.vue';
import CustomInput from '@/Components/CustomInput.vue';
import ModalColisions from './Components/ModalColisions.vue'
import axios from 'axios';
import MultiSelect from 'primevue/multiselect';
import ButtonGroup from 'primevue/buttongroup';
import ProgressBar from 'primevue/progressbar';
import Avatar from 'primevue/avatar';
import AvatarGroup from 'primevue/avatargroup';
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
import Knob from 'primevue/knob';
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
//#endregion

//#region variables
const openConflict = ref(false)
const date = ref(new Date())
const personal = ref()
const projectData = ref([]);
const loadingPerson = ref(true);
const conflicts = ref()
const task = ref([])
const mode = ref('date')
const filter = ref('');
const dates = ref(new Date(date.value.getFullYear(), date.value.getMonth(), date.value.getDate() + 1))
const diasSemana = ref(obtenerFechasSemana(dates.value))
const projectsSelected = ref([])
const overlayPerson = ref()
const dragStart = ref()
const personsEdit = ref()
const tabActive = ref()
const statusPersonal = ref({})
const selectDays = ref([])

//#endregion

//#region Consultas
const getPersonalData = () => {
    loadingPerson.value = true
    axios.get(route('get.personal.user')).then((res) => {
        personal.value = Object.values(res.data.personal)
        loadingPerson.value = false
    })
}
getPersonalData()


async function getTaskDay(days, project) {
    project.tasks = {}
    await days.forEach(async (dia) => {
        project.tasks[dia.toISOString().split('T')[0]] = { loading: true, data: [] }
        await axios.post(route('actividadesDeultimonivelPorProyectos', project.id), { date: dia }).then((res) => {
            project.tasks[dia.toISOString().split('T')[0]].data = res.data
            project.tasks[dia.toISOString().split('T')[0]].loading = false
        })
    })
}

const getTask = async (option) => {
    projectData.value = []
    if (option == null) {
        option = mode.value
    }
    if (option == 'week') {
        mode.value = option
        if (projectsSelected.value.length > 0) {
            projectData.value = projectsSelected.value
            projectData.value.forEach(async (project) => {
                await getTaskDay(diasSemana.value, project)
            })
        }
        // getPersonalStatus(diasSemana.value)
    } else if (option == 'date') {
        mode.value = 'date'
        // dates.value = new Date()
        let date = []
        date[0] = dates.value
        projectData.value = projectsSelected.value
        if (projectData.value.length > 0) {
            projectData.value.forEach(async (project) => {
                await getTaskDay(date, project)
            })
        }
        // getPersonalStatus(date)

    } else if (mode.value == 'month') {

    } else {

    }
};

const getPersonalStatus = async (days) => {
    await days.forEach(async (dia) => {
        statusPersonal.value[dia.toISOString().split('T')[0]] = { loading: true, data: [] }
        await axios.post(route('get.personal.status.programming'), { date: dia }).then((res) => {
            statusPersonal.value[dia.toISOString().split('T')[0]].data = res.data
            statusPersonal.value[dia.toISOString().split('T')[0]].loading = false
        })
    })
}


getPersonalStatus([dates.value])


//

//#endregion

//#region funciones

//#endregion

//#region drag

const getChildPayload = (index) => {
    return personal.value[index];
}

async function onDrop(collection, dropResult, fecha) {
    const { addedIndex, payload } = dropResult;
    if (addedIndex !== null) {
        if (new Date(fecha.toISOString().split("T")[0]) >= new Date(date.value.toISOString().split("T")[0])) {
            collection.loading = true
            await axios.post(route('programming.store'), { task_id: collection.id, employee_id: payload.Num_SAP, name: payload.Nombres_Apellidos, fecha: fecha.toISOString().split("T")[0] }).then((res) => {
                if (Object.values(res.data.conflict).length > 0) {
                    conflicts.value = Object.values(res.data.conflict)
                    openConflict.value = true;
                    collection.loading = false
                    task.value = collection
                    collection.employees = res.data.task
                } else if (res.data.status == false) {
                    collection.loading = false
                    collection.employees = res.data.task
                    toast.add({ severity: 'error', group: "customToast", text: 'Hubo un error al programar', life: 2000 })
                } else {
                    console.log(payload)
                    getPersonalStatus([fecha])
                    collection.employees = res.data.task
                    collection.loading = false
                    toast.add({ severity: 'success', group: "customToast", text: 'Persona programada', life: 2000 })
                }
            })
        } else {
            toast.add({ severity: 'error', group: "customToast", text: 'No se puede programar en dias anteriores', life: 2000 })
        }
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

const togglePerson = (event, persons, task, date) => {
    dateSelect.value = date
    editHorario.value.data = task
    personsEdit.value = persons
    overlayPerson.value.toggle(event);
}

const editHour = (horario, option) => {
    Object.assign(editHorario.value, horario)
    console.log(editHorario.value)
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
    <AppLayout>
        <div class="h-full w-full sm:grid sm:grid-cols-8 flex-col overflow-hidden">
            <div class="sm:col-span-7 h-[88%] sm:h-full space-y-1 pt-1 px-1 flex flex-col">
                <div class="sm:flex grid grid-cols-1 gap-1 justify-between sm:h-10 items-center sm:pr-1">
                    <div class="flex w-full justify-between sm:w-fit space-x-4">
                        <p class="text-xl font-bold text-primary h-full items-center flex">
                            Programación de actividades
                        </p>
                        <p class="border px-2 bg-primary rounded-lg text-white flex items-center">
                            {{ $page.props.auth.user.oficina }}
                        </p>
                    </div>
                    <div class="sm:flex grid grid-cols-2 items-center gap-2 sm:space-x-2">
                        <MultiSelect v-model="projectsSelected" display="chip" :options="projects" optionLabel="name"
                            class="sm:w-56 w-full" placeholder="Seleccione un proyecto" @change="getTask()" />
                        <ButtonGroup class="hidden sm:block">
                            <Button label="Mes" disabled @click="
                                mode = 'month';
                            dates = (new Date()).getFullYear() + '-' + ((new Date()).getMonth().toString().length < 2 ? '0' + (new Date()).getMonth() : (new Date()).getMonth());
                            getTask()" :outlined="mode != 'month'" />
                            <Button label="Semana" @click="
                                dates = obtenerFormatoSemana(new Date());
                            getPersonalStatus(obtenerFechasSemana(obtenerDiaSemana(dates)));
                            getTask('week')" :outlined="mode != 'week'" />
                            <Button label="dia" @click="dates = new Date(); getPersonalStatus([dates]); getTask('date')"
                                :outlined="mode != 'date'" />
                        </ButtonGroup>
                        <div class="sm:w-52 w-full">
                            <CustomInput v-model:input="dates" :type="mode" :manualInput="false"
                                @valueChange="mode == 'week' ? (diasSemana = obtenerFechasSemana(obtenerDiaSemana(dates)), getPersonalStatus(diasSemana)) : getPersonalStatus([dates]); getTask()" />
                        </div>

                    </div>
                </div>
                <!-- region calendario -->
                <div class="cursor-default flex flex-col h-full">
                    <div v-if="mode == 'week'" class="h-[80vh] flex flex-col justify-between border rounded-md">
                        <!-- region Cabezeras -->
                        <div class="grid-cols-10 h-6 text-lg leading-6 grid pr-3 border-b shadow-md mb-1 ">
                            <div class="flex flex-col items-center">
                                <p class="flex w-full justify-center items-baseline font-bold">Proyecto</p>
                            </div>
                            <span class="grid grid-cols-7 col-span-9">
                                <div v-for="dia, index in diasSemana" class="flex flex-col items-center"
                                    :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary rounded-t-md font-bold' : '']">
                                    <p class="flex capitalize border-b w-full justify-center items-baseline">{{
                                dia.toLocaleDateString('es-CO', {
                                    weekday: 'long', year: 'numeric', month: 'numeric', day:
                                        'numeric'
                                }) }}
                                    </p>
                                </div>
                            </span>
                            <!-- endregion -->
                        </div>
                        <!-- region actividades -->
                        <div class="h-full space-y-2 overflow-y-clip pl-1 ">
                            <div v-if="projectData.length > 0" v-for="project in projectData"
                                class="grid-cols-10 divide-x divide-y cursor-default h-full divide-gray-100 border border-indigo-200 rounded-l-md text-lg leading-6 grid">
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
                                        :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary' : '']">
                                        <div v-if="project.tasks[dia.toISOString().split('T')[0]].loading"
                                            class="flex justify-center h-full items-center">
                                            <Loading />
                                        </div>
                                        <span v-else v-for="task in project.tasks[dia.toISOString().split('T')[0]].data"
                                            class="w-full p-0.5">
                                            <div
                                                class="border border-primary h-40 rounded-md flex flex-col justify-between">
                                                <div class="flex flex-col justify-between h-full">
                                                    <span>
                                                        <p
                                                            class="border-b font-bold border-primary h-10 flex items-center justify-center text-xs px-0.5 w-full text-center">
                                                            {{ task.name }}
                                                        </p>
                                                        <p class="text-xs px-1 text-center w-full">{{ task.task }}</p>
                                                        <div
                                                            class="flex cursor-default space-x-2 justify-center rounded-md">
                                                            <p v-tooltip.left="'Fecha inicio'"
                                                                class=" px-1 text-xs text-center">
                                                                {{ task.startDate }}
                                                            </p>
                                                            <p v-tooltip.left="'Fecha Fin'"
                                                                class="text-xs text-center px-1"
                                                                :class="new Date(task.endDate) < date ? 'bg-red-500 rounded-md' : ''">
                                                                {{ task.endDate }}
                                                            </p>
                                                        </div>
                                                    </span>
                                                    <div class="grid grid-cols-4 items-center px-1 h-full">
                                                        <div
                                                            class="flex cursor-default flex-col justify-center rounded-md h-full">
                                                            <p v-tooltip.left="'Hora inicio'"
                                                                class="text-sm text-center">
                                                                {{ format24h(task.shift.startShift) }}
                                                            </p>
                                                            <p v-tooltip.left="'Hora Fin'" class="text-sm text-center">
                                                                {{ format24h(task.shift.endShift) }}
                                                            </p>
                                                        </div>
                                                        <Container
                                                            class="col-span-3 flex flex-col justify-center h-14 cursor-default"
                                                            @drop="onDrop(task, $event, dia)" group-name="1"
                                                            @click="togglePerson($event, task.employees, task, dia)"
                                                            v-tooltip.top="{ value: task.employees?.length > 0 ? `<div>${task.employees.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                                                            <div v-if="!dragStart && !(task.loading == null ? false : task.loading)"
                                                                class="flex justify-center">
                                                                <AvatarGroup
                                                                    v-if="(task.employees ? true : false) && task.employees.length > 0 && task.employees.length <= 2">
                                                                    <Avatar v-for="person in task.employees"
                                                                        :image="person.photo ?? '/images/person-default.png'"
                                                                        shape="circle" size="large" />
                                                                </AvatarGroup>
                                                                <AvatarGroup
                                                                    v-else-if="(task.employees ? true : false) && task.employees.length > 2">
                                                                    <Avatar v-for="i in [0, 1]"
                                                                        :image="task.employees[i].photo ?? '/images/person-default.png'"
                                                                        shape="circle" size="large" />
                                                                    <Avatar
                                                                        :label="'+' + String(task.employees.length - 2)"
                                                                        shape="circle" size="large" />
                                                                </AvatarGroup>
                                                                <div v-else class="flex items-center p-1">
                                                                    <p
                                                                        class="border p-1 text-center text-sm font-bold text-danger rounded-md border-dashed bg-danger-light animate-pulse">
                                                                        Sin personal asignado

                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div v-else-if="!task.loading && ((new Date(new Date().toISOString().split('T')[0])) <= new Date(dia.toISOString().split('T')[0]))"
                                                                class="flex items-center p-1">
                                                                <p
                                                                    class="border w-full h-full p-1 text-center rounded-md border-dashed text-sm bg-primary-light animate-pulse">
                                                                    Arrastra aqui para programar
                                                                </p>
                                                            </div>
                                                            <div v-else-if="dragStart && !((new Date(new Date().toISOString().split('T')[0])) <= new Date(dia.toISOString().split('T')[0]))"
                                                                class="flex items-center p-1">
                                                                <p
                                                                    class="border w-full h-full p-1 text-center rounded-md border-dashed text-sm bg-primary-light animate-pulse">
                                                                    No se puede programar
                                                                </p>
                                                            </div>
                                                            <!-- {{ ((new Date(new Date().toISOString().split('T')[0])) <= new Date(dia.toISOString().split('T')[0])) }} -->
                                                            <!-- {{ (new Date(dia.toISOString().split('T')[0])) }} -->
                                                            <!-- {{ (new Date()) }} -->
                                                            <!-- {{ (new Date(dia).toISOString().split('T')[0] >= (new Date().toISOString().split('T')[0])) }} -->
                                                            <ProgressBar v-if="task.loading" mode="indeterminate"
                                                                style="height: 4px" />
                                                        </Container>
                                                    </div>
                                                </div>
                                                <div class="p-1">
                                                    <ProgressBar :value="parseFloat(task.percentDone)"
                                                        style="height: 12px" v-tooltip="'Avance'"
                                                        :pt="{ label: 'text-xs font-thin' }">
                                                    </ProgressBar>
                                                </div>
                                            </div>
                                        </span>
                                    </div>
                                </span>
                                <!-- endregion -->
                            </div>
                            <div class="h-full" v-else>
                                <NoContentToShow subject="Seleccione uno o mas proyectos" />
                            </div>
                        </div>
                        <!-- endregion -->
                        <div class="grid-cols-10 h-8 text-sm grid pr-3 border-t shadow-lg mt-1 ">
                            <div>
                                <p class="w-full h-full flex items-center justify-center font-bold">
                                    Total personas
                                </p>
                            </div>
                            <span class="col-span-9 grid grid-cols-7 z-10">
                                <div v-for="dia, index in diasSemana" class="flex h-full items-center space-x-3 px-2"
                                    :key="index + dia"
                                    :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary rounded-b-md font-bold' : '']">

                                    <div class="rounded bg-primary px-2 w-full text-center text-white"
                                        v-tooltip.top="{ value: statusPersonal[dia.toISOString().split('T')[0]].data.programados?.length > 0 ? `<div><p class='w-full text-center font-bold'>Programados</p>${statusPersonal[dia.toISOString().split('T')[0]].data.programados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                                        <i v-if="statusPersonal[dia.toISOString().split('T')[0]].loading"
                                            class="fa-solid fa-spinner animate-spin" />
                                        <p v-else>
                                            {{ statusPersonal[dia.toISOString().split('T')[0]].data.programados.length
                                            }}
                                        </p>
                                    </div>
                                    <div class="rounded bg-danger px-2 w-full text-center text-white"
                                        v-tooltip.top="{ value: statusPersonal[dia.toISOString().split('T')[0]].data.noProgramados?.length > 0 ? `<div><p class='w-full text-center font-bold'>No programados</p>${statusPersonal[dia.toISOString().split('T')[0]].data.noProgramados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                                        <i v-if="statusPersonal[dia.toISOString().split('T')[0]].loading"
                                            class="fa-solid fa-spinner animate-spin" />
                                        <p v-else>
                                            {{ statusPersonal[dia.toISOString().split('T')[0]].data.noProgramados.length
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>
                    <div v-if="mode == 'date'"
                        class=" h-full sm:h-[80vh] border rounded-md flex flex-col justify-between">
                        <p class="w-full h-6 text-center bg-primary-light font-bold">
                            Programacion del dia {{ dates.toLocaleDateString() }}
                        </p>
                        <div class="h-full p-1 sm:overflow-y-auto space-y-1">
                            <div v-if="projectData.length > 0" v-for="project in projectData"
                                class="border w-full sm:flex p-1 rounded-md">
                                <div class="sm:w-40 flex items-center flex-col justify-center">
                                    <p class="font-bold">
                                        {{ project.name }}
                                    </p>
                                    <Knob v-tooltip="'Avance'" :model-value="parseInt(project.avance)" readonly
                                        :size="40" valueTemplate="{value}%" />
                                    <div class="text-xs">
                                        <p v-tooltip="'Fecha de inicio'" class="px-2">{{ project.fechaI }}</p>
                                        <p v-tooltip="'Fecha de fin'" class="px-2">{{ project.fechaF }}</p>
                                    </div>
                                </div>
                                <div class="w-full h-[55vh] sm:h-full overflow-y-auto sm:overflow-x-auto grid grid-cols-2 sm:grid-cols-5">
                                    <div v-if="project.tasks[dates.toISOString().split('T')[0]].loading"
                                        class="flex col-span-5 justify-center h-full items-center">
                                        <Loading />
                                    </div>
                                    <span v-else v-for="task in project.tasks[dates.toISOString().split('T')[0]].data"
                                        class="w-full p-0.5 cursor-default">
                                        <div
                                            class="border border-primary h-40 rounded-md flex flex-col justify-between">
                                            <div class="flex flex-col justify-between h-full">
                                                <span>
                                                    <p
                                                        class="border-b font-bold border-primary h-10 flex items-center justify-center text-xs px-0.5 w-full text-center">
                                                        {{ task.name }}
                                                        <!-- {{ console.log(task) }} -->
                                                    </p>
                                                    <p class="text-xs px-1 text-center w-full">{{ task.task }}</p>
                                                    <div
                                                        class="flex cursor-default space-x-2 justify-center rounded-md">
                                                        <p v-tooltip.left="'Fecha inicio'"
                                                            class=" px-1 text-xs text-center">
                                                            {{ task.startDate }}
                                                        </p>
                                                        <p v-tooltip.left="'Fecha Fin'" class="text-xs text-center px-1"
                                                            :class="new Date(task.endDate) < date ? 'bg-red-500 rounded-md' : ''">
                                                            {{ task.endDate }}
                                                        </p>
                                                    </div>
                                                </span>
                                                <div class="grid grid-cols-4 items-center px-1 h-full">
                                                    <div
                                                        class="flex cursor-default flex-col justify-center rounded-md h-full">
                                                        <p v-tooltip.left="'Hora inicio'" class="text-sm text-center">
                                                            {{ format24h(task.shift?.startShift??null) }}
                                                        </p>
                                                        <p v-tooltip.left="'Hora Fin'" class="text-sm text-center">
                                                            {{ format24h(task.shift?.endShift??null) }}
                                                        </p>
                                                    </div>
                                                    <Container
                                                        class="col-span-3 flex flex-col justify-center h-14 cursor-default"
                                                        @drop="onDrop(task, $event, dates)" group-name="1"
                                                        @click="togglePerson($event, task.employees, task, dates)"
                                                        v-tooltip.top="{ value: task.employees?.length > 0 ? `<div>${task.employees.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                                                        <div v-if="!dragStart && !(task.loading == null ? false : task.loading)"
                                                            class="flex justify-center">
                                                            <AvatarGroup
                                                                v-if="(task.employees ? true : false) && task.employees.length > 0 && task.employees.length <= 2">
                                                                <Avatar v-for="person in task.employees"
                                                                    :image="person.photo ?? '/images/person-default.png'"
                                                                    shape="circle" size="large" />
                                                            </AvatarGroup>
                                                            <AvatarGroup
                                                                v-else-if="(task.employees ? true : false) && task.employees.length > 2">
                                                                <Avatar v-for="i in [0, 1]"
                                                                    :image="task.employees[i].photo ?? '/images/person-default.png'"
                                                                    shape="circle" size="large" />
                                                                <Avatar :label="'+' + String(task.employees.length - 2)"
                                                                    shape="circle" size="large" />
                                                            </AvatarGroup>
                                                            <div v-else class="flex items-center p-1">
                                                                <p
                                                                    class="border p-1 text-center text-danger rounded-md border-dashed bg-danger-light animate-pulse">
                                                                    Sin personal asignado
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div v-else-if="!task.loading && (new Date(dates.toISOString().split('T')[0]) >= (new Date()))"
                                                            class="flex items-center p-1">
                                                            <p
                                                                class="border w-full h-full p-1 text-center rounded-md border-dashed text-sm bg-primary-light animate-pulse">
                                                                Arrastra aqui para programar
                                                            </p>
                                                        </div>
                                                        <div v-else-if="dragStart && !(new Date(dates.toISOString().split('T')[0]) >= (new Date()))"
                                                            class="flex items-center p-1">
                                                            <p
                                                                class="border w-full h-full p-1 text-center rounded-md border-dashed text-sm bg-primary-light animate-pulse">
                                                                No se puede programar
                                                            </p>
                                                        </div>
                                                        <ProgressBar v-if="task.loading" mode="indeterminate"
                                                            style="height: 4px" />
                                                    </Container>
                                                </div>
                                            </div>
                                            <div class="p-1">
                                                <ProgressBar :value="parseFloat(task.percentDone)" class=""
                                                    v-tooltip="'Avance'" :pt="{ label: 'text-xs font-thin' }">
                                                </ProgressBar>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="" v-else>
                                <NoContentToShow subject="Seleccione uno o mas proyectos" />
                            </div>
                        </div>
                        <div class="w-full justify-center flex space-x-2 p-1 z-10">
                            <p class="rounded bg-primary px-2 text-white"
                                v-tooltip="{ value: statusPersonal[dates.toISOString().split('T')[0]].data.programados?.length > 0 ? `<div><p class='w-full text-center font-bold'>Programados</p>${statusPersonal[dates.toISOString().split('T')[0]].data.programados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                                Programados:
                                <i v-if="statusPersonal[dates.toISOString().split('T')[0]].loading"
                                    class="fa-solid fa-spinner animate-spin" />
                                <span v-else>{{
                                statusPersonal[dates.toISOString().split('T')[0]].data.programados.length }}
                                </span>
                            </p>
                            <span class="rounded bg-danger px-2 text-white" title="dasdads"
                                v-tooltip.click.top="{ value: statusPersonal[dates.toISOString().split('T')[0]].data.noProgramados?.length > 0 ? `<div><p class='w-full text-center font-bold'>No programados</p>${statusPersonal[dates.toISOString().split('T')[0]].data.noProgramados.map((employee) => `<p class='w-44 text-sm truncate'>${employee.name}</p>`).join('')}</div>` : null, escape: false, pt: { text: 'text-center w-52' } }">
                                No programados:
                                <i v-if="statusPersonal[dates.toISOString().split('T')[0]].loading"
                                    class="fa-solid fa-spinner animate-spin" />
                                <span v-else>{{
                                statusPersonal[dates.toISOString().split('T')[0]].data.noProgramados.length }}
                                </span>
                            </span>
                            <!-- <p class="rounded bg-warning px-2 text-white">No programables 3</p> -->
                        </div>
                    </div>
                    <div v-if="mode == 'month'" class="h-[80vh] flex flex-col justify-between"></div>
                </div>
            </div>
            <!--#region LISTA PERSONAL-->
            <div class="sm:row-span-2 block sm:rounded-lg border sm:h-full p-1">
                <CustomInput v-model:input="filter" type="search" icon="fa-solid fa-magnifying-glass" />
                <Loading v-if="loadingPerson" class="mt-10" message="Cargando personas" />
                <div v-else class="sm:overflow-y-auto sm:h-[81vh] p-1">
                    <Container oncontextmenu="return false" onkeydown="return false" behaviour="copy" group-name="1"
                        @drag-start="dragStart = true" @drag-end="dragStart = false"
                        :get-child-payload="getChildPayload" class="sm:space-y-1 flex sm:flex-col space-x-1 overflow-y-auto">
                        <Draggable v-for="item in personal" :key="item.id"
                            v-tooltip.top="{ value: 'Arrastra hasta la tarea donde asignaras la persona', showDelay: 1000, hideDelay: 300, pt: { text: 'text-center' } }"
                            :class="(item.Nombres_Apellidos.toUpperCase().includes(filter.toUpperCase()) || item.Cargo.toUpperCase().includes(filter.toUpperCase())) ? '' : '!hidden'"
                            :drag-not-allowed="false"
                            class="snap-start rounded-xl border border-primary h-full shadow-md cursor-pointer hover:bg-primary-light hover:ring-1 hover:ring-primary">
                            <div class="flex flex-col gap-x-1 p-1">
                                <span class=" flex flex-col justify-center">
                                    <p class="text-sm font-semibold truncate  text-gray-900">
                                        {{ item.Nombres_Apellidos }}
                                    </p>
                                    <p class="flex mt-1 text-xs truncate  text-gray-500">
                                        {{ item.Cargo }}
                                    </p>
                                    <p class="flex mt-1 text-xs truncate  text-gray-500">
                                        {{ item.Oficina }}
                                    </p>
                                </span>
                            </div>
                        </Draggable>
                    </Container>
                </div>
            </div>
        </div>
    </AppLayout>

    <OverlayPanel ref="overlayPerson">
        <div class="flex flex-col space-y-1 w-96">
            <!-- {{ personsEdit }} -->
            <div v-for="person in personsEdit" class="flex flex-col border p-1 rounded-md justify-between">
                <p class="text-sm font-bold">{{ person.name }}</p>
                <div class="grid grid-cols-3 gap-2">
                    <!-- {{ console.log(person) }} -->
                    <span v-for="schedule_time in person.times"
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

    <CustomModal v-if="modhours" v-model:visible="modhours" :footer="false" icon="fa-regular fa-clock" width="60vw"
        :closeOnEscape="false"
        :titulo="editHorario?.option != 'delete' ? 'Modificar horario de ' + editHorario?.nombre : 'Eliminando a ' + editHorario?.nombre + ' de la actividad ' + editHorario?.nombreTask">
        <template #body>
            <!-- {{ console.log(editHorario) }} -->
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

<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    /* object-position: 50% 30%; */
    border-radius: 5px 10px;
    object-fit: cover;
    /* Opciones: 'cover', 'contain', 'fill', etc. */
}

.info-resto {
    font-size: 15px;
    text-wrap: balance;
    opacity: .5;
}
</style>