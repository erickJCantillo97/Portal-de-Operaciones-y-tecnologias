<script setup>
import { onMounted, ref, watch } from "vue"
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import CustomModal from '@/Components/CustomModal.vue'
import Tag from "primevue/tag"
import Knob from "primevue/knob"
import draggable from "vuedraggable";
import CustomInput from "@/Components/CustomInput.vue"
const { truncateString } = useCommonUtilities()

const inProcessModal = ref(false)
const doneModal = ref(false)
const typeChange = ref('')
const sourceListIndex = ref(null)

const moveList = ref('');

const drag = ref();

// const getTaskPendientes = () => {
//     axios.get(route('get.times.employees')).then((res) => {
//         pending.value = res.data.times
//     })
// }

// onMounted(() => {
//     getTaskPendientes()
// })



const pending = ref([
    {
        id: 2,
        title: 'Nombre de resumen las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        init_Hour: '07:00',
        finish_Hour: '12:00',
        percentDone: 40
    },
])

const inProcess = ref([

    {
        id: 2,
        title: 'Nombre de resumen las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        init_Hour: '07:00',
        finish_Hour: '12:00',
        percentDone: 0
    },
    {
        id: 3,
        title: ' los nombres de las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        init_Hour: '07:00',
        finish_Hour: '12:00',
        percentDone: 30
    },
])

const done = ref([
    {
        id: 3,
        title: 'Nombre de resumen de tareas contiene los ',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        percentDone: 30
    }
])

const draggedItem = ref(null)

const handleDragEnd = (event, move) => {
    // draggedItem.value = list[event.oldIndex]
    moveList.value = move
    sourceListIndex.value = event.newIndex;
    // console.log(event['to'])

}
// watch(inProcess, (oldValue, newValue) => {
//     console.log('Holaaaaaaaaaa')
//     console.log(oldValue, newValue)
// }, deep: true)

// const handleDragEnd = (event) => {
//     console.log(event)
// }

const handleDragOver = (event) => {
    event.preventDefault()
    // console.log('handleDragOver')
}

const handleDrop = (type) => {
    typeChange.value = type
    inProcessModal.value = true
}
</script>
<template>
    <div class="w-full pl-4 bg-white/30 backdrop-blur-sm sticky top-0">
        <h2 class="text-[1.4rem] text-primary font-bold">
            Lista de Tareas
        </h2>
    </div>
    <div class="grid grid-cols-3 gap-x-6 p-2 ">
        <!--PENDING-->
        <div class="bg-orange-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-orange-500">
            <div class="flex justify-between w-full h-8 px-2 mb-8 bg-white/30 backdrop-blur-sm sticky top-0">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-circle-exclamation text-orange-600"></i>
                    <h1 class="font-extrabold text-orange-500">
                        Asignadas
                    </h1>
                </div>
                <div class="flex w-8 items-center justify-center rounded-full bg-orange-500 p-1">
                    <span class="text-white text-sm font-semibold">{{ pending.length }}</span>
                </div>
            </div>

            <draggable :list="pending" @end="handleDragEnd($event, pending)"
                :group="{ name: 'taskss', pull: 'clone', put: false }" :animation="200" ghost-class="ghost-card"
                class="px-2 pt-4 h-full" key="pending">
                <template #item="{ element }">
                    <div
                        class="my-2 flex items-center justify-between space-x-8 rounded-lg bg-white p-4 hover:border hover:border-orange-400 cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-primary text-sm">
                                {{ truncateString(element.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded />
                            <p class="flex text-xs italic text-slate-400">
                                {{ element.start }}, {{ element.init_Hour }} - {{ element.finish_Hour }}
                            </p>
                        </div>
                        <div>
                            <!-- <Knob v-model="element.percentDone" :size="50" readonly /> -->
                        </div>
                    </div>
                </template>
            </draggable>
        </div>
        <!--IN PROCESS-->
        <div class="bg-blue-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-primary">
            <div class="flex justify-between w-full h-8 px-2 mb-8 bg-white/30 backdrop-blur-sm sticky top-0">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-clock text-blue-400"></i>
                    <h1 class="font-extrabold text-primary">
                        En Proceso
                    </h1>
                </div>
                <div class="flex w-8 items-center justify-center rounded-full bg-primary p-1">
                    <span class="text-white text-sm font-semibold">{{ inProcess.length }}</span>
                </div>
            </div>

            <draggable :list="inProcess" @start="handleDragEnd" @change="handleDrop('process')" :animation="200"
                ghost-class="ghost-card" group="taskss" class="px-2 pt-4 h-full" key="inprocess">
                <template #item="{ element }">
                    <div
                        class="my-2 flex items-center justify-between space-x-8 rounded-lg bg-white p-4 hover:border hover:border-primary cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-primary text-sm">
                                {{ truncateString(element.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded />
                            <p class="flex text-xs italic text-slate-400">
                                {{ element.start }}, {{ element.init_Hour }} - {{ element.finish_Hour }}
                            </p>
                        </div>
                        <div>
                            <Knob v-model="element.percentDone" valueTemplate="{value}%" :size="50" readonly />
                        </div>
                    </div>
                </template>
            </draggable>

            <!-- <div class="h-[80vh] overflow-y-auto snap snap-mandatory">

            </div> -->
        </div>
        <!--DONE-->
        <div class="bg-emerald-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-emerald-500">
            <div class="flex justify-between w-full h-8 px-2 mb-8 bg-white/30 backdrop-blur-sm sticky top-0">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <h1 class="font-extrabold text-emerald-500">
                        En Proceso
                    </h1>
                </div>
                <div class="flex w-8 items-center justify-center rounded-full bg-emerald-500 p-1">
                    <span class="text-white text-sm font-semibold">{{ inProcess.length }}</span>
                </div>
            </div>

            <draggable :list="done" @start="handleDragEnd" @change="handleDrop('done')" :animation="200"
                ghost-class="ghost-card" group="taskss" class="px-2 pt-4 h-full" key="inprocess">
                <template #item="{ element }">
                    <div
                        class="my-2 flex items-center justify-between space-x-8 rounded-lg bg-white p-4 hover:border hover:border-emerald-500 cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-primary text-sm">
                                {{ truncateString(element.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded />
                            <p class="flex text-xs italic text-slate-400">
                                {{ element.start }}, {{ element.init_Hour }} - {{ element.finish_Hour }}
                            </p>
                        </div>
                        <div>
                            <Knob v-model="element.percentDone" valueTemplate="{value}%" :size="50" readonly />
                        </div>
                    </div>
                </template>
            </draggable>

            <!-- <div class="h-[80vh] overflow-y-auto snap snap-mandatory">

            </div> -->
        </div>
    </div>

    <!--MODAL DE "EN PROCESO"-->
    <CustomModal v-model:visible="inProcessModal" :closable="true" width="40rem">
        <template #icon>
            <span class="text-white material-symbols-outlined text-3xl">
                assignment
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                Registrar {{  }} como completada
            </span>
        </template>
        <template #body>
            <div v-if="typeChange == 'process'" class="space-y-2 mb-4">
                <CustomInput type="number" :max="99" label="Porcentaje de avance"
                    v-model:input="inProcess[sourceListIndex].percentDone" />
                <div class="flex w-full justify-between items-center">
                    <CustomInput type="time" label="Hora de Inicio" />
                    <CustomInput type="time" label="Hora Fin" />
                </div>
            </div>
        </template>
        <template #footer>
            <Button @click="inProcessModal = false" label="Cancelar" severity="danger"
                icon="fa fa-circle-xmark"></Button>
            <Button label="Guardar" severity="success" icon="pi pi-save"></Button>
        </template>
    </CustomModal>

    <!--MODAL DE "COMPLETADAS"-->
    <CustomModal v-model:visible="doneModal" :closable="true" width="40rem">
        <template #icon>
            <span class="text-white material-symbols-outlined text-3xl">
                task_alt
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                Registro de Planilla
            </span>
        </template>
        <template #body>
            <div v-if="typeChange == 'process'" class="space-y-2 mb-4">
                <CustomInput type="number" :max="99" label="Porcentaje de avance"
                    v-model:input="inProcess[sourceListIndex].percentDone" />
                <div class="flex w-full justify-between items-center">
                    <CustomInput type="time" label="Hora de Inicio" />
                    <CustomInput type="time" label="Hora Fin" />
                </div>
            </div>
        </template>
        <template #footer>
            <Button @click="doneModal = false" label="Cancelar" severity="danger" icon="fa fa-circle-xmark"></Button>
            <Button label="Guardar" severity="success" icon="pi pi-save"></Button>
        </template>
    </CustomModal>
</template>
