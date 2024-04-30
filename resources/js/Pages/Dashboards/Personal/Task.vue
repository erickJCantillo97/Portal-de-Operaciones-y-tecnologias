<script setup>
import { onMounted, ref, watch } from "vue"
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import CustomModal from '@/Components/CustomModal.vue'
import Tag from "primevue/tag"
import Knob from "primevue/knob"
import draggable from "vuedraggable";
import CustomInput from "@/Components/CustomInput.vue"
const { truncateString } = useCommonUtilities()

const openModal = ref(false)
const typeChange = ref('')
const sourceListIndex = ref(null)

const moveList = ref('');

const drag = ref();

const getTaskPendientes = () => {
    axios.get(route('get.times.employees')).then((res) => {
        pending.value = res.data.times
    })
}

onMounted(() => {
    getTaskPendientes()
})



const pending = ref([
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
    openModal.value = true
}
</script>
<template>
    <div class="w-full pl-4 bg-white/30 backdrop-blur-sm sticky top-0">
        <h2 class="text-[1.4rem] text-primary font-bold">
            Gestión de Actividades Semanal
        </h2>
    </div>
    <div class="grid grid-cols-3 gap-x-6 p-2 ">
        <div class="bg-orange-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-primary">
            <div class="flex justify-between w-full h-8 px-2 mb-8 bg-white/30 backdrop-blur-sm sticky top-0">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-clock text-blue-400"></i>
                    <h1
                        class="font-extrabold text-pr40 Nombre de resumen las tareas de resumenNombre de Proyecto extenso que son la ma...12/04/2024, 07:00 - 12:0040%imary">
                        Actividades Pendientes

                    </h1>
                </div>
                <div class="flex w-8 items-center justify-center rounded-full bg-primary p-1">
                    <span class="text-white text-sm font-semibold">{{ pending.length }}</span>
                </div>
            </div>

            <draggable :list="pending" @end="handleDragEnd($event, pending)" :group="{ name: 'taskss', put: false }"
                :animation="200" ghost-class="ghost-card" class="px-2 pt-4 h-full opacity-70" key="pending">
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
                                {{ element.init_Hour }} - {{ element.finish_Hour }}
                            </p>
                        </div>
                        <div>
                            <!-- <Knob v-model="element.percentDone" :size="50" readonly /> -->
                        </div>
                    </div>
                </template>
            </draggable>

            <!-- <div class="h-[80vh] overflow-y-auto snap snap-mandatory">
                <div :draggable="true" v-for="(item, index) in inProcess" :key="item.id"
                    @drop="handleDrop($event, index)" @dragstart="handleDragStart(index)" @dragend="handleDragEnd"
                    @dragover="handleDragOver()" :animation="200">
                    <div
                        class="my-2 flex items-center justify-between space-x-8 rounded-lg bg-white p-4 hover:border hover:border-primary cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-primary text-sm">
                                {{ truncateString(item.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(item.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(item.project, 40)}`" rounded />
                            <p class="flex text-xs italic text-slate-400">
                                {{ item.start }}, {{ item.init_Hour }} - {{ item.finish_Hour }}
                            </p>
                        </div>
                        <div>
                            <Knob v-model="progressTask" valueTemplate="{value}%" :size="50" readonly />
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        <div class="bg-blue-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-primary">
            <div class="flex justify-between w-full p-2 mb-1 bg-white/30 backdrop-blur-sm sticky top-0">
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
                ghost-class="ghost-card" group="taskss" class="px-2 h-full" key="inprocess">
                <template #item="{ element }">
                    <div
                        class="mb-2 flex  justify-between rounded-lg bg-white p-4 hover:border hover:border-primary cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-primary text-sm">
                                {{ truncateString(element.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded />
                            <div class="flex overflow-x-auto space-x-4 w-[22vw] text-sm cursor-pointer">
                                <div class="italic p-1 text-nowrap border text-emerald-700 rounded-lg bg-emerald-100">
                                    {{ element.init_Hour }} -{{ element.finish_Hour }}
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <Knob v-model="element.percentDone" valueTemplate="{value}%" :size="50" readonly />
                            <div class="flex space-x-1 mt-2">
                                <Button severity="secondary" text icon="fa-solid fa-pencil"></Button>
                                <Button severity="secondary" text icon="fa fa-trash-can"></Button>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>

            <!-- <div class="h-[80vh] overflow-y-auto snap snap-mandatory">
                <div :draggable="true" v-for="(item, index) in inProcess" :key="item.id"
                    @drop="handleDrop($event, index)" @dragstart="handleDragStart(index)" @dragend="handleDragEnd"
                    @dragover="handleDragOver()" :animation="200">
                    <div
                        class="my-2 flex items-center justify-between space-x-8 rounded-lg bg-white p-4 hover:border hover:border-primary cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-primary text-sm">
                                {{ truncateString(item.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(item.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(item.project, 40)}`" rounded />
                            <p class="flex  italic text-slate-400">
                                {{ item.start }}, {{ item.init_Hour }} - {{ item.finish_Hour }}
                            </p>
                        </div>
                        <div>
                            <Knob v-model="progressTask" valueTemplate="{value}%" :size="50" readonly />
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        <!--DONE-->
        <div class="bg-emerald-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-success">
            <h1 class="font-extrabold text-success">
                Completadas ({{ done.length }})
            </h1>
            <div :draggable="true" v-for="(item, index) in done" :key="item.id" @drop="handleDrop($event, index)"
                @dragstart="handleDragStart(index)" @dragend="handleDragEnd" @dragover="handleDragOver()"
                :animation="200">
                <div
                    class="my-2 flex cursor-grab items-center justify-between space-x-6 rounded-md border border-success p-4">
                    <div>
                        <p class="font-bold text-success">
                            {{ item.project }}
                        </p>
                        {{ item.title }}
                        <p class="flex text-sm italic">
                            {{ item.start }} - {{ item.end }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL DE COMENTARIOS-->
    <CustomModal v-model:visible="openModal" :closable="true" width="40rem">
        <template #icon>
            <span class="text-white material-symbols-outlined text-3xl">
                chat
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">Registro de Planilla</span>
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
            <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark"></Button>
            <Button label="Guardar" severity="success" icon="pi pi-save"></Button>

        </template>
    </CustomModal>
</template>
