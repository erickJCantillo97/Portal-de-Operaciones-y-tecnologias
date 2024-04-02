<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Dropdown from 'primevue/dropdown';
import NoContentToShow from '@/Components/NoContentToShow.vue'
import WeekTable from '@/Pages/Programming/WeekTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'

const props = defineProps({
    projects: Array,
})

onMounted(() => {
    // getTask()
})

const loading = ref(false)
const project = ref()

const divisionsOptions = ref(
    [
        'GEMAM',
        'GEBOC',
        'DEEST',
        'DEGPM',
        'DEPRO',
        'DVPCP',
        'DVARD',
        'DVSOL',
        'DVMEC',
        'DVPIN',
        'DVELC',
        'DVHAB',
        'DVAIR',
        'DVEAT',
        'DVMOT',
        'DVADQ',
        'DEINE',
        'DEMTO',
        'CLIENTE'
    ])

const Options = ref([
    {
        id: 1,
        name: 'GEMAM',
        project: 'TOP',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
    {
        id: 2,
        name: 'GEBOC',
        project: 'PUNTA BORINQUEN',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
    {
        id: 3,
        name: 'GEMAM',
        project: 'TOP',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
    {
        id: 4,
        name: 'GEBOC',
        project: 'PUNTA BORINQUEN',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
    {
        id: 5,
        name: 'GEMAM',
        project: 'TOP',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
    {
        id: 6,
        name: 'GEBOC',
        project: 'PUNTA BORINQUEN',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
    {
        id: 7,
        name: 'GEBOC',
        project: 'PUNTA BORINQUEN',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
    {
        id: 8,
        name: 'GEBOC',
        project: 'PUNTA BORINQUEN',
        date: '12-04-2024',
        shift: '7:00 - 16:30'
    },
])

//#region UseForm
const projectSelected = ref()
const formData = useForm({
    date: [],
    timeStart: '',
    timeEnd: '',
    task: [],
    observations: '',
})
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
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}

const openModal = ref(false)

const openDialog = () => {
    formData.reset()
    projectSelected.value = ''
    openModal.value = true
}

//#region Requests
const submit = async() => {
    try{
        await axios.post(route('task.store.i'), formData)
            .then(res => {
                //TODO request
                console.log('Hace algo')
            })
    } catch (error) {
        console.error('Error' + error)
    }
}

const task = ref()
const getTask = async() => {
    try{
        await axios.get(route('get.task'))
            .then(res => {
                //TODO request
                task.value = res.data
        })
    } catch (error) {
        console.error('Error ' + error)
    }
}

const editTask = async(id) => {
    try{
        await axios.put(route('task.store.i', task.id))
            .then(res => {
                //TODO request
                console.log('Hace algo')
            })
    } catch (error) {
        console.error('Error ' + error)
    }
}

const deleteTask = async(id) => {
    try{
        await axios.delete(route('task.delete.i', task.id))
            .then(res => {
                //TODO request
                console.error('Hace algo')
            })
    } catch (error) {
        console.error('Error ' + error)
    }
}
//#endregion
</script>
<template>
    <AppLayout>
        <div class="h-full w-full">
            <div class="flex justify-between items-center px-4 h-min">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    <p> Programación de Actividades </p>
                    <!-- <CustomInput type="week" v-model:input="date" @change="getTask('date'); getPersonalData()" /> -->
                </span>
                <div class="flex items-center space-x-2">
                    <span class="flex flex-col sm:flex-row items-center sm:space-x-2">
                        <Dropdown :options="projects" placeholder="Seleccione un proyecto" optionLabel="name"
                            optionValue="id" showClear :filter="true" filterPlaceholder="Buscar proyecto"
                            v-model="project" :pt="{
                            root: '!h-8',
                            input: '!py-0 !flex !items-center !text-sm !font-normal',
                            item: '!py-1 !px-3 !text-sm !font-normal',
                            filterInput: '!h-8'
                        }" />
                    </span>
                    <span class="flex flex-col sm:flex-row items-center sm:space-x-2">
                        <Dropdown :options="divisionsOptions" placeholder="Seleccione una división" showClear
                            :filter="true" filterPlaceholder="Buscar proyecto" v-model="project" :pt="{
                            root: '!h-8',
                            input: '!py-0 !flex !items-center !text-sm !font-normal',
                            item: '!py-1 !px-3 !text-sm !font-normal',
                            filterInput: '!h-8'
                        }" />
                    </span>
                    <!-- <CustomInput type="week" placeholder="Seleccione una semana"></CustomInput> -->
                    <Link :href="route('programming.create')">
                    <Button label="Programación Ordinaria" severity="success" icon="fa-solid fa-plus"
                        :project="project" />
                    </Link>
                    <Button label="Programación de Extendidos" severity="warning" icon="fa-solid fa-plus"
                        @click="openDialog()" />
                </div>
            </div>
            <div class="mt-2 px-4 h-[79vh] overflow-y-auto">
                <div v-if="!project && !loading" class="flex items-center">
                    <NoContentToShow class="mt-5" :subject="'Por favor seleccione un Proyecto'" />
                </div>
                <WeekTable v-else />
            </div>
        </div>

        <!--CUSTOM MODAL-->
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
                        <CustomInput type="date" label="Fecha de extendidos" v-model:input="formData.date"
                            :multiple="true" />

                        <div class="flex items-center">
                            <CustomInput type="time" label="Hora Inicio del Turno" v-model:input="formData.timeStart"
                                :multiple="true" />
                            <i class="fa-solid fa-minus mx-2 mt-[1.60rem]"></i>
                            <CustomInput type="time" label="Hora Fin del Turno" v-model:input="formData.timeEnd"
                                :multiple="true" />
                        </div>

                        <CustomInput label="Proyectos" type="dropdown" optionLabel="name" :options="projects"
                            placeholder="Seleccione un proyecto" filterPlaceholder="Buscar proyecto" v-model:input="projectSelected">
                        </CustomInput>

                        <CustomInput label="Actividades" selectionMode optionLabel="name" type="multiselect"
                            :options="Options" placeholder="Seleccione actividad(es)" v-model:input="formData.task">
                        </CustomInput>

                        <CustomInput class="mt-2" label="Observaciones" placeholder="Observaciones" type="textarea"
                            v-model:input="formData.observations">
                        </CustomInput>

                        <div class="flex justify-end space-x-2">
                            <Button severity="success" :loading="false" @click="submit()">
                                Guardar
                            </Button>
                        </div>
                    </div>

                    <div class="w-full">
                        <div class="border border-gray-300 rounded-lg">
                            <div class="flex justify-center items-center bg-blue-900 rounded-t-lg p-2">
                                <h2 class="text-center text-white">
                                    Listado de Actividades Extendidos
                                </h2>
                            </div>
                            <div class="h-[25rem] snap-y snap-mandatory overflow-y-auto p-2">
                                <ul v-for="task in Options">
                                    <div class="mb-2 snap-center gap-2 space-y-2 rounded-lg border border-gray-300 p-2">
                                        <li class="font-semibold text-primary">{{ task.project }}</li>
                                        <div class="block"> <!--TODO v-for tasks, edit tasks & -->
                                            <div class="flex justify-between items-center">
                                                <li class="font-semibold">{{ task.name }}</li>
                                                <div class="flex space-x-3 w-20">
                                                    <Button v-tooltip.top="'Editar Tarea'" class="mb-2"
                                                        icon="pi pi-pencil" severity="warning" outlined small
                                                        @click="editTask(task.id)" />
                                                    <Button v-tooltip.top="'Eliminar Tarea'" class="mb-2"
                                                        icon="pi pi-trash" severity="danger" outlined small
                                                        @click="deleteTask(task.id)" />
                                                </div>
                                            </div>
                                            <div class="flex justify-between">
                                                <li class="italic">Lunes {{ task.date }}</li>
                                                <li v-tooltip.left="'Turno Ordinario'"
                                                    class="italic rounded-lg bg-success p-2 text-white text-xs">
                                                    {{ task.shift }}
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
