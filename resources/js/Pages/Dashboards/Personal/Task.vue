<script setup>
import { onMounted, ref } from "vue"
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import CustomModal from '@/Components/CustomModal.vue'
import Tag from "primevue/tag"
import Knob from "primevue/knob"

const { truncateString } = useCommonUtilities()

const openModal = ref(false)
const progressTask = ref(60)
const sourceListIndex = ref(null)

const getTaskPendientes = () => {
    axios.get(route('get.times.employees')).then((res) => {
        pending.value = res.data.times
    })
}

onMounted(() => {
    getTaskPendientes()
})

const pending = ref([
    {
        id: 1,
        title: 'Nombre de resumen de tareas contiene los nombres de las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024'
    }
])

const inProcess = ref([
    {
        id: 0,
        title: 'Nombre de resumen de tareas contiene los nombres de las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        init_Hour: '07:00',
        finish_Hour: '12:00',
    },
    {
        id: 1,
        title: 'Nombre de resumen de tareas contiene los nombres de las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        init_Hour: '07:00',
        finish_Hour: '12:00',
    },
    {
        id: 2,
        title: 'Nombre de resumen de tareas contiene los nombres de las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        init_Hour: '07:00',
        finish_Hour: '12:00',
    },
    {
        id: 3,
        title: 'Nombre de resumen de tareas contiene los nombres de las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024',
        init_Hour: '07:00',
        finish_Hour: '12:00',
    },
])

const done = ref([
    {
        id: 3,
        title: 'Nombre de resumen de tareas contiene los nombres de las tareas de resumen',
        project: 'Nombre de Proyecto extenso que son la mayoría de los nombres en Cotecmar',
        start: '12/04/2024',
        end: '02/05/2024'
    }
])

const draggedItem = ref(null)

const handleDragStart = (event, item, index) => {
    console.log(index)
    draggedItem.value = item
    event.dataTransfer.effectAllowed = 'move'
    // openModal.value = true
}

const handleDragEnd = (event) => {
    console.log(event)
}

const handleDragOver = (event) => {
    event.preventDefault()
    // console.log('handleDragOver')
}

const handleDrop = (event, item, index) => {
    console.log(index)
    if (item !== sourceListIndex.value) {
        item[index].splice(index, 0, draggedItem.value);
        const sourceItems = lists[sourceListIndex.value];
        const indexToRemove = sourceItems.findIndex(i => i.id === draggedItem.value.id);
        sourceItems.splice(indexToRemove, 1);
    }
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
        <div class="bg-orange-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-warning">
            <div class="w-full flex justify-between">
                <h1 class="font-extrabold text-danger">
                    Asignadas
                </h1>
                <h1 class="font-extrabold text-danger">
                    {{ pending.length }} / {{ (pending.length + inProcess.length + done.length) }}
                </h1>
            </div>
            <div :draggable="true" v-for="(item, index) in pending" :key="item.id"
                @drop="handleDrop($event, item, index)" @dragstart="handleDragStart($event, item, index)"
                @dragend="handleDragEnd" @dragover="handleDragOver($event)" :animation="200">
                <div
                    class="my-2 flex cursor-grab items-center justify-between space-x-6 rounded-md border border-danger p-4">
                    <div>
                        <p class="font-bold text-danger">
                            {{ truncateString(item.project, 10) }}
                        </p>
                        {{ truncateString(item.title, 10) }}
                        <p class="flex text-sm italic">
                            {{ item.start }} - {{ item.end }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!--IN PROGRESS-->
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
            <div class="h-[80vh] overflow-y-auto snap snap-mandatory">
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
            </div>
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
        </template>
    </CustomModal>
</template>
