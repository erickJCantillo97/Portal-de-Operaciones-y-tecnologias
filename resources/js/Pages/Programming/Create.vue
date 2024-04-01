<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { Container, Draggable } from "vue-dndrop";
import Loading from '@/Components/Loading.vue';
import CustomInput from '@/Components/CustomInput.vue';
import ModalColisions from './Components/ModalColisions.vue'
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import MultiSelect from 'primevue/multiselect';
import ButtonGroup from 'primevue/buttongroup';
import ProgressBar from 'primevue/progressbar';
import Avatar from 'primevue/avatar';
import AvatarGroup from 'primevue/avatargroup';
// const { hasRole, hasPermission } = usePermissions()
const divisiones = ['GEMAM',
    'GEBOC',
    'JDEEST',
    'JDEGPM',
    'JDEPRO',
    'JDVPCP',
    'JDVARD',
    'JDVSOL',
    'JDVMEC',
    'JDVPIN',
    'JDVELC',
    'JDVHAB',
    'JDVAIR',
    'JDVEAT',
    'JDVMOT',
    'JDVADQ',
    'JDEINE',
    'JDEMTO',
    'OFTIC',
    'CLIENTE',
]

defineProps({
    projects: Array
})

const openConflict = ref(false)
function format24h(hora) {
    if (hora.length > 5) {
        return new Date(hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    } else {
        return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
            { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
    }
}

//#region Draggable
const date = ref(new Date())
const personal = ref()
const projectData = ref([]);
const loadingProgram = ref(true);
const loadingPerson = ref(true);
const personalHours = ref({});
const conflicts = ref()
const task = ref([])

// El código anterior define una función `onDrop` en Vue. Esta función se activa cuando un elemento se
// coloca en una colección.


const getPersonalData = () => {
    loadingPerson.value = true
    axios.get(route('get.personal.user')).then((res) => {
        personal.value = Object.values(res.data.personal)
        loadingPerson.value = false
    })
}

//#endregion

// El código anterior utiliza el gancho de ciclo de vida `onMounted` de Vue para ejecutar algún código
// cuando el componente está montado.
onMounted(() => {
    getPersonalData()
    // getTask('tomorrow')
})

// El código anterior es una función de Vue.js que recupera tareas según la opción seleccionada.
const getTask = async () => {
    loadingProgram.value = true
    console.log(projectsSelected.value)
    console.log(diasSemana.value[0].toISOString())
    let date_start = diasSemana.value[0].toISOString()
    let date_end = diasSemana.value[5].toISOString()
    await axios.get(route('actividadesDeultimonivelPorProyectos', { idProject: projectsSelected.value[0].id, date_start, date_end })).then((res) => {
        let project = projectsSelected.value[0]
        project.tasks = res.data
        projectData.value.push(project)
        console.log(projectData.value)
        loadingProgram.value = false;
    })
}
//#region

//#endregion
const filter = ref('');

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

const week = ref(obtenerFormatoSemana(date.value))
const diasSemana = ref(obtenerFechasSemana(date.value))
const projectsSelected = ref()
</script>

<template>
    <AppLayout>
        <div class="h-full w-full grid grid-cols-8">
            <div class="col-span-7 h-full space-y-1 pt-1 px-1 flex flex-col">
                <div class="flex justify-between h-10 items-center">
                    <span class="flex space-x-2">
                        <p class="text-xl font-bold text-primary h-full items-center flex">
                            Programación de actividades
                        </p>
                        <p class="border px-2 bg-primary rounded-lg text-white items-center">
                            {{ $page.props.auth.user.oficina }}
                        </p>
                    </span>
                    <div class="flex items-center space-x-2">
                        <MultiSelect v-model="projectsSelected" display="chip" :options="projects" optionLabel="name"
                            class="w-56" placeholder="Seleccione un proyecto" @change="getTask()" />
                        <ButtonGroup>
                            <Button label="Mes" disabled />
                            <Button label="Semana" />
                            <Button label="dia" disabled />
                        </ButtonGroup>
                        <CustomInput v-model:input="week" type="week"></CustomInput>
                    </div>
                </div>
                <!-- region calendario -->
                <div class="h-[75vh]">
                    <!-- region Cabezeras -->
                    <div class="grid-cols-7 h-6 text-lg leading-6 text-gray-500 grid mr-4 shadow-md">
                        <div class="flex flex-col items-center">
                            <p class="flex border-b w-full justify-center items-baseline font-bold">Proyecto</p>
                        </div>
                        <div v-for="dia, index in diasSemana" class="flex flex-col items-center"
                            :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary font-bold' : '']">
                            <p class="flex capitalize border-b w-full justify-center items-baseline">{{
                                dia.toLocaleDateString('es-CO', {
                                    weekday: 'long', year: 'numeric', month: 'numeric', day:
                                        'numeric'
                                }) }}
                            </p>
                        </div>
                        <!-- endregion -->
                    </div>
                    <!-- region Cabezeras -->
                    <div v-for="data in projectData"
                        class="grid-cols-7 h-full divide-x divide-y divide-gray-100 text-lg border-gray-100 leading-6 text-gray-500 grid overflow-y-scroll mr-1">
                        <div class="flex flex-col items-center px-2 h-full">
                            <div class="flex h-full w-full items-center justify-center font-bold">
                                <p>
                                    {{ data.name }}
                                </p>
                                {{ data.percentDone }}
                            </div>
                        </div>
                        <div v-for="dia, index in diasSemana" class="flex flex-col items-center justify-center"
                            :class="[dia.toISOString().split('T')[0] == date.toISOString().split('T')[0] ? 'bg-secondary' : '']">
                            <span v-for="task in data.tasks" class="w-full p-0.5">
                                <div class="border border-primary h-32 rounded-md flex flex-col justify-between"
                                    v-if="fechaEnRango(task.startDate, task.endDate, dia.toISOString().split('T')[0])">
                                    <div class="flex flex-col justify-between h-full">
                                        <p class="border-b font-bold border-primary  text-xs px-0.5 w-full text-center">
                                            {{ task.name }}
                                        </p>
                                        <p class="text-xs px-1 text-center w-full">{{ task.task }}</p>
                                        <div class="grid grid-cols-4 items-center px-1">
                                            <div
                                                class="px-1 flex cursor-default flex-col justify-center border rounded-md h-full">
                                                <p v-tooltip.left="'Hora inicio'" class="text-sm">
                                                    {{ format24h(task.shift.startShift) }}
                                                </p>
                                                <p v-tooltip.left="'Hora Fin'" class="text-sm">
                                                    {{ format24h(task.shift.endShift) }}
                                                </p>
                                            </div>
                                            <div class="col-span-3 flex justify-center">
                                                <AvatarGroup>
                                                    <Avatar image="/images/avatar/amyelsner.png" shape="circle" />
                                                    <Avatar image="/images/avatar/asiyajavayant.png" shape="circle" />
                                                    <Avatar image="/images/avatar/onyamalimba.png" shape="circle" />
                                                    <Avatar label="+2" shape="circle" />
                                                </AvatarGroup>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-1">
                                        <!-- {{ task.percentDone }} -->
                                        <ProgressBar :value="task.percentDone" class=""
                                            :pt="{ label: 'text-xs font-thin' }"></ProgressBar>
                                    </div>
                                </div>
                            </span>

                        </div>
                        <!-- endregion -->
                    </div>
                    <!-- endregion -->
                    <div class="grid-cols-7 divide-x-2 text-sm leading-6 text-gray-500 grid mr-4 shadow-md">
                        <div>
                            
                        </div>
                        <div class="grid grid-cols-4">
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                        </div>
                        <div class="grid grid-cols-4">
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                        </div>
                        <div class="grid grid-cols-4">
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                        </div>
                        <div class="grid grid-cols-4">
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                        </div>
                        <div class="grid grid-cols-4">
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                        </div>
                        <div class="grid grid-cols-4">
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                            <p class="w-full text-center">10</p>
                        </div>
                    </div>
                </div>

                <!-- <Listbox :options="tasks" :filterFields="['task', 'name', 'project']" class="col-span-2" filter :pt="{
                                list: '!h-[76vh] !px-1 !snap-y !snap-mandatory',
                                item: '!h-full !p-0 !rounded-md !snap-start !my-0.5',
                                filterInput: '!h-8',
                                header: '!p-1'
                            }">
                    <template #option="slotProps">
                        <div class="flex flex-col justify-between h-full p-2 border rounded-md shadow-md snap-start">
                            <p><b>{{ slotProps.option.task }}</b> <i class="fa-solid fa-angle-right"></i>
                                {{ slotProps.option.name }}
                            </p>
                            <p class="text-xs italic uppercase text-primary">{{ slotProps.option.project.name }}</p>
                            <span class="grid items-center text-xs grid-cols-6 space-x-2">
                                <span class="grid grid-cols-3">
                                    <p class="font-bold ">I:</p>
                                    <p class="font-mono col-span-2 cursor-default" v-tooltip="'Fecha inicio'">
                                        {{ slotProps.option.startDate }}
                                    </p>
                                    <p class="font-bold">F:</p>
                                    <p class="font-mono col-span-2 cursor-default" v-tooltip="'Fecha fin'">
                                        {{ slotProps.option.endDate }}
                                    </p>
                                </span>
                                <span class="flex justify-center">
                                    <Knob v-tooltip.top="'Avance: ' + parseInt(slotProps.option.percentDone) + '%'"
                                        :model-value=parseInt(slotProps.option.percentDone) :size=50
                                        valueTemplate="{value}%" readonly />
                                </span>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Horario predefinido</p>
                                    <span
                                        class="flex items-center justify-center space-x-2 text-green-900 bg-green-200 rounded-md p-1">
                                        <p class="">
                                            {{ format24h(slotProps.option.shift.startShift) }}
                                        </p>
                                        <p class="">
                                            {{ format24h(slotProps.option.shift.endShift) }}
                                        </p>
                                    </span>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Valor estimado</p>
                                    <p class="text-green-900 bg-green-200 rounded-md p-1">$1.000.000
                                    </p>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Valor programado</p>
                                    <p class="text-green-900 bg-green-200 rounded-md p-1">$1.000.000
                                    </p>
                                </div>
                                <div class="text-center justify-center">
                                    <p class="font-bold">Diferencia</p>
                                    <p class="text-green-900 bg-green-200 rounded-md p-1">$1.000.000
                                    </p>
                                </div>
                            </span>
                            <div v-if="loadingTasks ? true : loadingTask[slotProps.option.id] ? true : false"
                                class="flex flex-col items-center justify-center h-full p-2">
                                <Loading message="Cargando" />
                            </div>
                            <Container v-if="!loadingTask[slotProps.option.id]"
                                class="h-full p-2 overflow-auto border border-blue-400 border-dashed rounded-lg shadow-sm hover:bg-blue-50 shadow-primary custom-scroll"
                                @drop="onDrop(slotProps.option, $event)" group-name="1">
                                <div class="grid grid-cols-2 gap-1"
                                    v-if="listaDatos[slotProps.option.id] !== undefined && listaDatos[slotProps.option.id].length != 0">
                                    <div v-for="(item, index) in listaDatos[slotProps.option.id]"
                                        class="p-1 mt-1 border-2 rounded-md">
                                        <div class="flex items-center justify-between w-full ">
                                            <p class="text-sm font-semibold ">{{ item.name }}</p>
                                            <button v-tooltip.top="'Eliminar de la Actividad'"
                                                v-if="item.is_my_personal"
                                                @click="editHour(slotProps.option, item, 'delete')">
                                                <i
                                                    class="fa-solid fa-circle-xmark text-danger hover:animate-pulse hover:scale-125" />
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between w-full font-mono align-middle ">
                                            <div class="grid w-full grid-cols-3 gap-2">
                                                <div v-for="horario in item.schedule_times"
                                                    class="flex items-center justify-between px-1 py-1 text-green-900 bg-green-200 rounded-md cursor-default group">
                                                    <button v-tooltip.bottom="'En desarrollo'"
                                                        v-if="item.is_my_personal" class="hidden group-hover:flex"
                                                        @click="console.log('En desarrollo')">
                                                        <i
                                                            class="fa-solid fa-trash-can text-danger text-xs hover:animate-pulse hover:scale-125"></i>
                                                    </button>
                                                    <span class="w-full text-xs tracking-tighter text-center">
                                                        {{
                                format24h(horario.hora_inicio.slice(0,
                                    horario.hora_inicio.lastIndexOf(':')))
                            }}
                                                        {{
                                    format24h(horario.hora_fin.slice(0,
                                        horario.hora_fin.lastIndexOf(':')))
                                }}
                                                    </span>
                                                    <button v-tooltip.bottom="'Cambiar horario'"
                                                        v-if="item.is_my_personal" class="hidden group-hover:flex"
                                                        @click="optionSelectHours = 'select'; editHour(horario, item, 'modify')">
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
                        <Loading v-if="loadingProgram" class="mt-10" message="Cargando actividades" />
                    </template>

</Listbox> -->
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
                            <!-- <span class="flex items-center">
                                <Button v-tooltip.left="'Horas programadas'" class="w-full"
                                    :key="personalHours[item.Num_SAP]"
                                    :icon="personalHours[(item.Num_SAP)] == undefined ? 'fa-solid fa-spinner animate-spin' : undefined"
                                    :label="personalHours[item.Num_SAP] != undefined ? personalHours[item.Num_SAP] + ' horas' : undefined"
                                    :severity="personalHours[item.Num_SAP] < 9.5 ? 'primary' : 'success'"
                                    @click="employeeDialog(item)" :pt="{ label: '!text-xs' }" />
                            </span> -->
                        </div>
                    </Draggable>
                    <!-- </span> -->
                </Container>

            </div>
        </div>
    </AppLayout>

    <!--#region MODALES -->

    <ModalColisions v-model:visible="openConflict" v-model:conflicts="conflicts" v-model:task="task">
    </ModalColisions>
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