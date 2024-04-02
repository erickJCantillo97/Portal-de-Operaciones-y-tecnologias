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

// const { hasRole, hasPermission } = usePermissions()

defineProps({
    projects: Array
})

// #region funciones basicas
function format24h(hora) {
    if (hora.length > 5) {
        return new Date(hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    } else {
        return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
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
    for (let i = 0; i < 6; i++) {
        fechasSemana.push(new Date(fechaSeleccionada));
        fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1);
    }

    return fechasSemana;
}

function fechaEnRango(fechaInicio, fechaFin, fechaSeleccionada) {
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    const seleccionada = new Date(fechaSeleccionada);

    return seleccionada >= inicio && seleccionada <= fin;
}
//#endregion

//#region variables
const openConflict = ref(false)
const date = ref(new Date())
const personal = ref()
const projectData = ref([]);
const loadingProgram = ref(true);
const loadingPerson = ref(true);
const personalHours = ref({});
const conflicts = ref()
const task = ref([])
const mode = ref('date')
const filter = ref('');
const dates = ref(new Date(date.value.getFullYear(), date.value.getMonth(), date.value.getDate() + 1))
const diasSemana = ref(obtenerFechasSemana(dates.value))
const projectsSelected = ref([])
const overlayPerson = ref()

//#endregion

//#region Consultas
const getPersonalData = () => {
    loadingPerson.value = true
    axios.get(route('get.personal.user')).then((res) => {
        personal.value = Object.values(res.data.personal)
        loadingPerson.value = false
    })
}

const getTask = async (option) => {
    loadingProgram.value = true
    if (option == null) {
        option = mode.value
    }
    if (option == 'week') {
        dates.value = obtenerFormatoSemana(new Date())
        mode.value = option
        let date_start = diasSemana.value[0].toISOString()
        let date_end = diasSemana.value[5].toISOString()
        if (projectsSelected.value.length > 0) {
            projectsSelected.value.forEach(async (element) => {
                await axios.get(route('actividadesDeultimonivelPorProyectos', { idProject: element.id, date_start, date_end })).then((res) => {
                    let project = element
                    project.tasks = res.data
                    projectData.value.push(project)
                    console.log(projectData.value)
                    loadingProgram.value = false;
                })
            })
        }
    } else if (option == 'date') {
        mode.value = 'date'
        dates.value = new Date()
        console.log(mode.value)
        let date_start = dates.value
        let date_end = dates.value
        if (projectsSelected.value.length > 0) {
            projectsSelected.value.forEach(async (element) => {
                await axios.get(route('actividadesDeultimonivelPorProyectos', { idProject: element.id, date_start, date_end })).then((res) => {
                    let project = element
                    project.tasks = res.data
                    projectData.value.push(project)
                    console.log(projectData.value)
                    loadingProgram.value = false;
                })
            })
        }

    } else if (mode.value == 'month') {

    } else {

    }
};

//#endregion

//#region funciones
const toggle = (event) => {
    overlayPerson.value.toggle(event);
}

onMounted(() => {
    getPersonalData()
})
//#endregion

</script>

<template>
    <AppLayout>
        <div class="h-full w-full grid grid-cols-8">
            <div class="col-span-7 h-full space-y-1 pt-1 px-1 flex flex-col">
                <div class="flex justify-between h-10 items-center pr-1">
                    <span class="flex space-x-4">
                        <p class="text-xl font-bold text-primary h-full items-center flex">
                            Programación de actividades
                        </p>
                        <p class="border px-2 bg-primary rounded-lg text-white flex items-center">
                            {{ $page.props.auth.user.oficina }}
                        </p>
                    </span>
                    <div class="flex items-center space-x-2">
                        <MultiSelect v-model="projectsSelected" display="chip" :options="projects" optionLabel="name"
                            class="w-56" placeholder="Seleccione un proyecto" @change="getTask()" />
                        <ButtonGroup>
                            <Button label="Mes" disabled
                                @click="mode = 'month'; dates = (new Date()).getFullYear() + '-' + ((new Date()).getMonth().toString().length < 2 ? '0' + (new Date()).getMonth() : (new Date()).getMonth()); getTask()"
                                :outlined="mode != 'month'" />
                            <Button label="Semana" @click="getTask('week')" :outlined="mode != 'week'" />
                            <Button label="dia" @click="getTask('date')" :outlined="mode != 'date'" />
                        </ButtonGroup>
                        <div class="w-52 flex justify-end">
                            <CustomInput v-model:input="dates" :type="mode"></CustomInput>
                        </div>
                    </div>
                </div>
                <!-- region calendario -->
                <span v-if="projectsSelected.length > 0">
                    <div v-if="mode == 'week'" class="h-[80vh] flex flex-col justify-between ">
                        <!-- region Cabezeras -->
                        <div class="grid-cols-7 h-6 text-lg leading-6 grid mr-4 shadow-md">
                            <div class="flex flex-col items-center">
                                <p class="flex border-b w-full justify-center items-baseline font-bold">Proyecto</p>
                            </div>
                            <div v-for="dia, index in diasSemana" class="flex flex-col items-center"
                                :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary rounded-t-md font-bold' : '']">
                                <p class="flex capitalize border-b w-full justify-center items-baseline">{{
                                dia.toLocaleDateString('es-CO', {
                                    weekday: 'long', year: 'numeric', month: 'numeric', day:
                                        'numeric'
                                }) }}
                                </p>
                            </div>
                            <!-- endregion -->
                        </div>
                        <!-- region actividades -->
                        <div class="h-full overflow-y-auto space-y-2">
                            <div v-for="data in projectData"
                                class="grid-cols-7 divide-x divide-y divide-gray-100 border border-indigo-200 rounded-md text-lg leading-6 grid mr-1">
                                <div class="flex flex-col items-center px-2 h-full">
                                    <div class="flex h-full w-full items-center justify-center font-bold">
                                        <p>
                                            {{ data.name }}
                                        </p>
                                        {{ data.percentDone }}
                                    </div>
                                </div>
                                <div v-for="dia, index in diasSemana" class="flex flex-col items-center"
                                    :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary' : '']">
                                    <span v-for="task in data.tasks" class="w-full p-0.5"
                                        :class="fechaEnRango(task.startDate, task.endDate, dia.toISOString().split('T')[0]) ? '' : 'hidden'">
                                        <div
                                            class="border border-primary h-32 rounded-md flex flex-col justify-between">
                                            <div class="flex flex-col justify-between h-full">
                                                <p
                                                    class="border-b font-bold border-primary h-10 flex items-center justify-center text-xs px-0.5 w-full text-center">
                                                    {{ task.name }}
                                                </p>
                                                <p class="text-xs px-1 text-center w-full">{{ task.task }}</p>
                                                <div class="grid grid-cols-4 items-center px-1">
                                                    <div
                                                        class="px-1 flex cursor-default flex-col justify-center border rounded-md h-full">
                                                        <p v-tooltip.left="'Hora inicio'" class="text-sm text-center">
                                                            {{ format24h(task.shift.startShift) }}
                                                        </p>
                                                        <p v-tooltip.left="'Hora Fin'" class="text-sm text-center">
                                                            {{ format24h(task.shift.endShift) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-span-3 flex justify-end">
                                                        <AvatarGroup @click="toggle">
                                                            <Avatar v-tooltip.top="'Nombre Apellido'"
                                                                v-for="person in [0, 1, 2, 3]"
                                                                image="/images/person-default.png" shape="circle" />
                                                            <Avatar v-tooltip.top="{
                                escape: false,
                                value:
                                    `<div class='flex flex-col'>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                </div>`
                            }" @click="toggle" label="+2" shape="circle" />
                                                        </AvatarGroup>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-1">
                                                <!-- {{ task.percentDone }} -->
                                                <ProgressBar :value="parseFloat(task.percentDone)" class=""
                                                    v-tooltip="'Avance'" :pt="{ label: 'text-xs font-thin' }">
                                                </ProgressBar>
                                            </div>
                                        </div>
                                    </span>

                                </div>
                                <!-- endregion -->
                            </div>
                        </div>
                        <!-- endregion -->
                        <div class="grid-cols-7 h-8 text-sm grid mr-4 shadow-md">
                            <div>
                                <p class="w-full h-full flex items-center justify-center font-bold">
                                    Total personas
                                </p>
                            </div>
                            <div v-for="dia, index in diasSemana" class="flex h-full items-center space-x-3 px-2"
                                :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary rounded-b-md font-bold' : '']">
                                <p v-tooltip.top="'Programados'"
                                    class="w-full text-center bg-success text-white rounded-md">20</p>
                                <p v-tooltip.top="'Sin programar'"
                                    class="w-full bg-danger rounded-md text-white text-center">1</p>
                                <!-- <p v-tooltip.top="'No programables'"
                                    class="w-full text-center bg-warning text-white rounded-md ">4</p> -->
                            </div>
                        </div>
                    </div>
                    <div v-if="mode == 'date'" class="h-[80vh] border rounded-md flex flex-col justify-between">
                        <p class="w-full text-center font-bold">Programacion del dia {{ dates.toLocaleDateString() }}
                        </p>
                        <div class="h-full p-1 overflow-y-auto space-y-1">
                            <div v-for="project in projectData" class="border w-full flex p-1 rounded-md">
                                <div class="w-40 flex items-center justify-center">
                                    <p>
                                        {{ project.name }}
                                    </p>
                                </div>
                                <div class="w-full overflow-x-auto grid grid-cols-5">
                                    <span v-for="task in project.tasks" class="w-full p-0.5"
                                        :class="fechaEnRango(task.startDate, task.endDate, new Date(dates).toISOString().split('T')[0]) ? '' : 'hidden'">
                                        <div
                                            class="border border-primary h-32 rounded-md flex flex-col justify-between">
                                            <div class="flex flex-col justify-between h-full">
                                                <p
                                                    class="border-b font-bold border-primary h-10 flex justify-center text-xs px-0.5 w-full items-center text-center">
                                                    {{ task.name }}
                                                </p>
                                                <p class="text-xs px-1 text-center w-full">{{ task.task }}</p>
                                                <div class="grid grid-cols-4 items-center px-1">
                                                    <div
                                                        class="px-1 flex cursor-default flex-col justify-center border rounded-md h-full">
                                                        <p v-tooltip.left="'Hora inicio'" class="text-sm text-center">
                                                            {{ format24h(task.shift.startShift) }}
                                                        </p>
                                                        <p v-tooltip.left="'Hora Fin'" class="text-sm text-center">
                                                            {{ format24h(task.shift.endShift) }}
                                                        </p>
                                                    </div>
                                                    <div class="col-span-3 flex justify-end">
                                                        <AvatarGroup @click="toggle">
                                                            <Avatar v-tooltip.top="'Nombre Apellido'"
                                                                v-for="person in [0, 1, 2, 3]"
                                                                image="/images/person-default.png" shape="circle" />
                                                            <Avatar v-tooltip.top="{
                                escape: false,
                                value:
                                    `<div class='flex flex-col'>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                    <p>Nombre Apellido</p>
                                                                </div>`
                            }" @click="toggle" label="+2" shape="circle" />
                                                        </AvatarGroup>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-1">
                                                <!-- {{ task.percentDone }} -->
                                                <ProgressBar :value="parseFloat(task.percentDone)" class=""
                                                    v-tooltip="'Avance'" :pt="{ label: 'text-xs font-thin' }">
                                                </ProgressBar>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div>

                        </div>
                        <div class="w-full justify-center flex gap-6">
                            <p class="rounded bg-primary px-2 text-white">Programados: 20</p>
                            <p class="rounded bg-danger px-2 text-white">No programados 2</p>
                            <!-- <p class="rounded bg-warning px-2 text-white">No programables 3</p> -->
                        </div>
                    </div>
                    <div v-if="mode == 'month'" class="h-[80vh] flex flex-col justify-between"></div>
                </span>
                <div class="h-full" v-else>
                    <NoContentToShow subject="Seleccione uno o mas proyectos" />
                </div>
            </div>
            <!--#region LISTA PERSONAL-->
            <div class="row-span-2 rounded-lg border p-1">
                <CustomInput v-model:input="filter" type="search" icon="fa-solid fa-magnifying-glass" />
                <Loading v-if="loadingPerson" class="mt-10" message="Cargando personas" />
                <Container v-else oncontextmenu="return false" onkeydown="return false" behaviour="copy" group-name="1"
                    class="h-[74vh] flex flex-col space-y-1 mt-1 p-1 snap-y snap-mandatory overflow-y-auto">
                    <!-- <span v-for="item in personal"> -->
                    <Draggable v-for="item in personal"
                        v-tooltip.top="{ value: 'Arrastra hasta la tarea donde asignaras la persona', showDelay: 1000, hideDelay: 300, pt: { text: 'text-center' } }"
                        :class="(item.Nombres_Apellidos.toUpperCase().includes(filter.toUpperCase()) || item.Cargo.toUpperCase().includes(filter.toUpperCase())) ? '' : '!hidden'"
                        :drag-not-allowed="personalHours[item.Num_SAP] < 9.5 ? false : true"
                        class="snap-start rounded-xl shadow-md cursor-pointer hover:bg-primary-light hover:ring-1 hover:ring-primary">
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
    </AppLayout>

    <OverlayPanel ref="overlayPerson">
        <div class="flex flex-col space-y-1 w-72">
            <div v-for="person in [0, 1, 2, 3, 4, 5]" class="flex justify-between space-x-1 items-center">
                <p class="font-bold">Persona con apellido</p>
                <span class="space-x-1">
                    <Button severity="warning" text raised icon="fa-solid fa-pencil" />
                    <Button severity="danger" text raised icon="fa-solid fa-trash-can" />
                </span>
            </div>
        </div>
    </OverlayPanel>

    <!--#region MODALES -->

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