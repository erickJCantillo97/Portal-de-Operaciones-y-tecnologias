<script setup>
const { format_ES_Date, formatDateTime24h } = useCommonUtilities()
import { Link } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { useCommonUtilities } from "@/composable/useCommonUtilities"
import { useToast } from "primevue/usetoast"
import AppLayout from '@/Layouts/AppLayout.vue'
import CounterUp from '@/Components/sections/CounterUp.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'
import DivisionsByProject from '@/Pages/Programming/DivisionsByProject.vue'
import Knob from 'primevue/knob'
const toast = useToast()

const props = defineProps({
    projects: Array,
})

const loading = ref(false)
const date = ref(new Date())
const project = ref()
const programming = ref([])
const selectedTaskId = ref(null)
const editMode = ref(false)
const disabled = computed(() => selectedTaskId.value !== null && editMode.value)

//#region UseForm
const projectSelected = ref()

const formData = ref({
    dates: [],
    start_hour: '',
    end_hour: '',
    tasks: [],
    description: '',
})

//#region CustomDataTable
const projectsByTask = ref()
const contador = ref([])
const getProgramming = async () => {
    await axios.get(route('get.programming.date', { date: date.value }))
        .then(res => {
            programming.value = res.data.programming
            contarPersonasPorProyecto(programming.value)
        })
}

onMounted(() => {
    getProgramming()
})

function contarPersonasPorProyecto(arrayProyectos) {
    let conteo = {};
    arrayProyectos.forEach(proyecto => {
        if (conteo[proyecto.project]) {
            conteo[proyecto.project]++;
        } else {
            conteo[proyecto.project] = 1;
        }
    });
    for (let idProyecto in conteo) {
        contador.value.push({ idProyecto: idProyecto, cantidadPersonas: conteo[idProyecto] });
    }
}

const columnas = [
    // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
    { field: 'project', header: 'Proyecto', filter: true, sortable: true, filterType: 'dropdown', filterOptions: contador, filterValue: 'idProyecto', filterLabel: 'idProyecto' },
    { field: 'task', header: 'Tarea', filter: true, sortable: true },
    { field: 'turno', header: 'Turno', filter: true, sortable: true },
    { field: 'division', header: 'Oficina', filter: true, sortable: true },
    { field: 'user', header: 'Personal', filter: true, sortable: true },
    { field: 'cargo', header: 'Cargo', filter: true, sortable: true },
]
//#endregion

/**
 * The above code is a JavaScript function that takes a time string in 24-hour format (e.g., "13:30")
// and converts it to a 12-hour format with AM/PM indicator. It creates a new Date object with the time
// string appended to a date string ("1970-01-01T") and then uses the toLocaleString method with
// options to format the time in 12-hour format with the 'es-CO' locale and 'h23' hour cycle.
//
 * @param {*} hora
 */
function format24h(hora) {
    return new Date(hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}

const openModal = ref(false)

const openDialog = () => {
    resetFormData()
    openModal.value = true
}

const taskOptions = ref()
const getTaskByProjects = async () => {
    try {
        await axios.get(route('extended.schedule.getTask', projectSelected.value))
            .then(res => {
                taskOptions.value = res.data
            })
    } catch (error) {
        console.error('Error ' + error)
    }
}

const resetFormData = () => {
    openModal.value = false

    formData.value = {
        dates: [],
        start_hour: '',
        end_hour: '',
        tasks: []
    }
    selectedTaskId.value = null
    projectSelected.value = ''
}

const url = [
    {
        ruta: 'programming',
        label: 'Programación',
        active: true
    }
]
</script>
<template>
    <AppLayout :href="url">
        <div class="size-full">
            <div class="flex justify-between items-center px-4 h-min">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    <p> Reporte Programación de Actividades por Proyectos
                        <span class="italic text-xs text-red-700">
                            (EN DESARROLLO)
                        </span>
                    </p>
                </span>

                <div class="flex items-center space-x-2">
                    <Link :href="route('programming.create')">
                    <Button label="Programar Personal" severity="success" icon="fa-solid fa-plus" :project="project" />
                    </Link>
                    <Button label="Programación de Extendidos" severity="warning" icon="fa-solid fa-plus"
                        @click="openDialog()" />
                </div>
            </div>
            <div class="flex my-4 px-4 w-full space-x-4 overflow-x-auto cursor-default sm:w-1/3 snap-x snap-mandatory">
                <CounterUp :label="project.idProyecto" :value="project.cantidadPersonas" v-for="project in contador" />
            </div>

            <!-- <DivisionsByProject :projects /> -->

            <div class="w-full h-full overflow-y-auto">
                <CustomDataTable :filterButtons="filterButtons" :data="programming" :rows-default="100"
                    :columnas="columnas" :actions="buttons">
                </CustomDataTable>
            </div>

            <!-- <div class="mt-2 px-4 h-[79vh] overflow-y-auto">
                <div v-if="!project && !loading" class="flex items-center">
                    <NoContentToShow class="mt-5" :subject="'Por favor seleccione un Proyecto'" />
                </div>
        </div> -->
        </div>

        <!--CUSTOM MODAL - EXTENDIDOS-->
        <CustomModal v-model:visible="openModal" width="70vw">
            <template #icon>
                <i class="fa-solid fa-file-contract"></i>
            </template>

            <template #titulo>
                <p>Programación de Extendidos</p>
            </template>

            <template #body>
                <div class="block md:flex space-x-2 space-y-4">
                    <div class="space-y-2">
                        <CustomInput type="date" label="Fecha de extendidos" v-model:input="formData.dates"
                            selectionMode="multiple" />

                        <div class="flex items-center">
                            <CustomInput type="time" label="Hora Inicio del Turno" v-model:input="formData.start_hour"
                                :multiple="true" />
                            <i class="fa-solid fa-minus mx-2 mt-[1.60rem]"></i>
                            <CustomInput type="time" label="Hora Fin del Turno" v-model:input="formData.end_hour"
                                :multiple="true" />
                        </div>

                        <CustomInput @change="getTaskByProjects()" label="Proyectos" type="dropdown" optionLabel="name"
                            option-value="id" :options="projects" placeholder="Seleccione un proyecto"
                            :disabled="disabled" :class="[disabled ? 'cursor-not-allowed' : '']"
                            filterPlaceholder="Buscar proyecto" v-model:input="projectSelected">
                        </CustomInput>

                        <CustomInput label="Actividades" selectionMode optionLabel="name" option-value="id"
                            type="multiselect" :options="taskOptions" placeholder="Seleccione actividad(es)"
                            :disabled="disabled" v-model:input="formData.tasks"
                            :class="[disabled ? 'cursor-not-allowed' : '']">
                        </CustomInput>

                        <CustomInput class="mt-2" label="Observaciones" placeholder="Observaciones" type="textarea"
                            v-model:input="formData.description">
                        </CustomInput>

                        <div class="flex justify-end space-x-2">
                            <Button severity="success" :loading="false" @click="submit()">
                                {{ selectedTaskId != null ? 'Actualizar' : 'Guardar' }}
                            </Button>
                        </div>
                    </div>

                    <div class="w-full">
                        <div class="border border-gray-300 rounded-lg">
                            <div class="flex justify-center items-center bg-blue-900 rounded-t-lg p-2">
                                <h2 class="text-center text-white">
                                    Listado de Actividades Extendidas
                                </h2>
                            </div>
                            <div class="h-[25rem] snap-y snap-mandatory overflow-y-auto p-2">
                                <ul v-for="project in projects">
                                    <div class="mb-2 snap-center gap-2 space-y-2 rounded-lg border border-gray-300 p-2">
                                        <li class="font-semibold text-primary">{{ project.name }}</li>
                                        <div v-for="task in project.tasks"
                                            class="block border-b-2 p-2 mb-2 rounded-lg hover:bg-gray-50"
                                            :class="[task.task.id == selectedTaskId ? 'bg-gray-200 p-1 rounded-lg' : '']">
                                            <div class="flex justify-between items-center">
                                                <li class="font-semibold">{{ task.task.name }}</li>
                                                <Knob v-tooltip.top="`Avance: ${parseInt(task.task.percentDone)}%`"
                                                    :model-value="parseInt(task.task.percentDone)" readonly :size="40"
                                                    valueTemplate="{value}%" />
                                                <div class="flex space-x-3 ">
                                                    <Button v-tooltip.top="'Editar Tarea'" class="mb-2"
                                                        icon="pi pi-pencil" severity="warning" outlined small
                                                        @click="editTask(task)" />
                                                    <Button v-tooltip.top="'Clonar Tarea'" class="mb-2"
                                                        icon="pi pi-clone" severity="info" outlined small
                                                        @click="cloneTask(task)" />
                                                    <Button v-tooltip.top="'Eliminar Tarea'" class="mb-2"
                                                        icon="pi pi-trash" severity="danger" outlined small
                                                        @click="deleteTask(task.id)" />
                                                </div>
                                            </div>
                                            <div class="flex justify-between">
                                                <li class="italic">
                                                    {{ format_ES_Date(task.date) }}</li>
                                                <li v-tooltip.left="'Turno Ordinario'"
                                                    class="italic rounded-lg bg-success p-2 text-white text-xs">
                                                    {{ format24h(task.start_hour) }} - {{ format24h(task.end_hour) }}
                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </CustomModal>
    </AppLayout>
</template>
