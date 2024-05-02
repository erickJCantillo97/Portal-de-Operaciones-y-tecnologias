<script setup>
import { ref, onMounted } from "vue"
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import CustomModal from '@/Components/CustomModal.vue'
import Tag from "primevue/tag"
import Knob from "primevue/knob"
import draggable from "vuedraggable";
import CustomInput from "@/Components/CustomInput.vue"
import { useToast } from "primevue/usetoast"

const { truncateString } = useCommonUtilities()

const toast = useToast()
const inProcessModal = ref(false)
const doneModal = ref(false)
const typeChange = ref('')
const moveList = ref('') // De donde proviene (oldIndex)
const sourceListIndex = ref(null) // Donde está (newIndex)



const getTaskPendientes = () => {
    axios.get(route('get.times.employees')).then((res) => {
        pending.value = res.data.times
        inProcess.value = res.data.times
        done.value = res.data.times
    })
}

onMounted(() => {
    getTaskPendientes()
})

const pending = ref([])

const inProcess = ref([])

const done = ref([])

const handleDragStart = (event, move) => {
    moveList.value = move
}

const handleDragEnd = (event) => {
    sourceListIndex.value = event.newIndex;
}

const handleDrop = (type) => {
    console.log(moveList.value + ' - ' + type)
    switch (type) {
        case 'pending':
            if (moveList.value == 'inProcess')
                toast.add({ severity: 'success', group: 'customToast', text: 'Añadido en Actividades Pendientes', life: 2000 });
            else if (moveList.value == 'done') {
                toast.add({ severity: 'success', group: 'customToast', text: 'Añadido en Actividades Pendientes', life: 2000 });
            }
            break;
        case 'inProcess':
            if (moveList.value == 'pending')
                inProcessModal.value = true
            else if (moveList.value == 'done') {
                alert('Estas seguro de cancelar la culminación de la tarea?')
            }
            break;
        case 'done':
            if (moveList.value == 'inProcess' || moveList.value == 'pending') {
                doneModal.value = true;
            }
            break;
    }
}
</script>
<template>
    <div class="w-full pl-4 bg-white/30 backdrop-blur-sm sticky top-0">
        <h2 class="text-[1.4rem] text-primary font-bold">
            Gestión de Actividades Semanal
        </h2>
    </div>
    <!--ASIGNADAS-->
    <div class="grid grid-cols-3 gap-x-2 p-2 ">
        <div class="bg-orange-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-orange-500">
            <div class="flex justify-between w-full p-2 mb-1 bg-white/30 backdrop-blur-sm sticky top-0">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-clock text-blue-400"></i>
                    <h1 class="font-extrabold text-primary">
                        Actividades Pendientes
                    </h1>
                </div>
                <div class="flex w-8 items-center justify-center rounded-full bg-primary p-1">
                    <span class="text-white text-sm font-semibold">{{ pending.length }}</span>
                </div>
            </div>

            <draggable :list="pending" @start="handleDragStart($event, 'pending')" @end="handleDragEnd"
                @change="handleDrop('pending')" :group="'taskss'" :animation="200" ghost-class="ghost-card"
                class="px-2 h-full opacity-70" key="pending">
                <template #item="{ element }">
                    <div
                        class="mb-2 flex items-center justify-between space-x-8 rounded-lg bg-white p-4 hover:border hover:border-orange-500 cursor-grab">
                        <div class="space-y-4">
                            <h3 class="font-bold text-primary text-sm" v-tooltip="element.title">
                                {{ element.title }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded />
                            <p class="flex text-sm italic text-slate-800">
                                {{ element.date }}
                            </p>
                        </div>
                        <div>
                            <div class="text-center bg-emerald-300 rounded-lg text-emerald-800 p-2 font-bold text-sm">
                                {{ element.hours == 9.5 ? element.hours - 1 : element.hours.toFixed(1) }}
                                <h4>Horas</h4>
                            </div>

                            <div class="text-center p-2 text-xs text-success" v-if="element.differentDays > 0">
                                {{ element.differentDays }} Dias restantes
                            </div>
                            <div class="text-center p-2 text-xs text-danger" v-if="element.differentDays < 0">
                                {{ element.differentDays }} Dias de retraso
                            </div>
                            <!-- <Knob v-model="element.percentDone" :size="50" readonly /> -->
                        </div>
                    </div>
                </template>
            </draggable>
        </div>
        <!--EN PROCESO-->
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

            <draggable :list="inProcess" @start="handleDragStart($event, 'inProcess')" @end="handleDragEnd"
                @change="handleDrop('inProcess')" :animation="200" ghost-class="ghost-card" group="taskss"
                class="px-2 h-full" key="inprocess">
                <template #item="{ element }">
                    <div
                        class="mb-2 flex justify-between rounded-lg bg-white p-4 hover:border hover:border-primary cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-primary text-sm">
                                {{ truncateString(element.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded />
                            <div class="flex overflow-x-auto space-x-4 w-[22vw] text-sm cursor-default">
                                <div class="italic p-1 text-nowrap border text-emerald-700 rounded-lg bg-emerald-100">
                                    {{ element.init_Hour }} -{{ element.finish_Hour }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-center items-center space-y-2 -ml-6">
                            <Knob v-model="element.percentDone" valueTemplate="{value}%" :size="50" readonly />
                            <div class="flex">
                                <Button v-tooltip.bottom="'Editar'" severity="secondary" text
                                    icon="fa-solid fa-pen-to-square hover:text-orange-400" />
                                <Button v-tooltip.bottom="'Eliminar'" severity="secondary" text
                                    icon="fa fa-trash-can hover:text-red-600" />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>
        <!--DONE-->
        <div class="bg-emerald-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-emerald-500">
            <div class="flex justify-between w-full p-2 mb-1 bg-white/30 backdrop-blur-sm sticky top-0">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <h1 class="font-extrabold text-emerald-600">
                        Completadas
                    </h1>
                </div>
                <div class="flex w-8 items-center justify-center rounded-full bg-emerald-500 p-1">
                    <span class="text-white text-sm font-semibold">{{ done.length }}</span>
                </div>
            </div>

            <draggable :list="done" @change="handleDrop('done')" @start="handleDragStart($event, 'done')"
                @end="handleDragEnd" :animation="200" ghost-class="ghost-card" group="taskss" class="px-2 h-full"
                key="done">
                <template #item="{ element }">
                    <div
                        class="mb-2 flex justify-between rounded-lg bg-white p-4 hover:border hover:border-emerald-500 cursor-grab">
                        <div class="space-y-4 ">
                            <h3 class="font-bold text-emerald-500 text-sm">
                                {{ truncateString(element.title, 80) }}
                            </h3>
                            <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded />
                            <div class="flex overflow-x-auto space-x-4 w-[22vw] text-sm cursor-default">
                                <div class="italic p-1 text-nowrap border text-emerald-700 rounded-lg bg-emerald-100">
                                    {{ element.init_Hour }} -{{ element.finish_Hour }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-center items-center space-y-2 -ml-6">
                            <Knob v-model="element.percentDone" valueTemplate="{value}%" :size="50" readonly />
                            <div class="flex">
                                <Button v-tooltip.bottom="'Editar'" severity="secondary" text
                                    icon="fa-solid fa-pen-to-square hover:text-orange-400" />
                                <Button v-tooltip.bottom="'Eliminar'" severity="secondary" text
                                    icon="fa fa-trash-can hover:text-red-600" />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>
    </div>

    <!--MODAL DE "EN PROCESO"-->
    <CustomModal v-model:visible="inProcessModal" :closable="false" width="40rem">
        <template #icon>
            <span class="text-white material-symbols-outlined text-3xl">
                assignment
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                Registrar avance de la Actividad
            </span>
        </template>
        <template #body>
            <div class="space-y-2 mb-4">
                <div class="text-xl text-center font-extrabold text-primary border-b border-primary">
                    {{ inProcess[sourceListIndex].id }}.
                    {{ inProcess[sourceListIndex].project }}
                </div>
                <div>
                    <span class="text-primary font-bold text-lg">
                        Actividad:
                    </span>
                    <span class="text-lg">
                        {{ inProcess[sourceListIndex].title }}
                    </span>
                </div>

                <CustomInput type="number" :max="99" label="Porcentaje de avance"
                    v-model:input="inProcess[sourceListIndex].percentDone" />
                <div class="flex w-full justify-between items-center">
                    <CustomInput type="time" label="Hora de Inicio" v-model:input="inProcess[sourceListIndex].start" />
                    <CustomInput type="time" label="Hora Fin" v-model:input="inProcess[sourceListIndex].end" />
                </div>
                <div>
                    <div class="flex overflow-x-auto space-x-4 w-[22vw] text-sm cursor-default">
                        <div class="italic p-1 text-nowrap border text-emerald-700 rounded-lg bg-emerald-100">
                            12:00 - 16:30
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <Button @click="inProcessModal = false" label="Cancelar" severity="danger"
                icon="fa fa-circle-xmark"></Button>
            <Button label="Guardar" severity="success" icon="pi pi-save"></Button>

        </template>
    </CustomModal>

    <!--MODAL DE "COMPLETADA"-->
    <CustomModal v-model:visible="doneModal" width="40rem">
        <template #icon>
            <span class="text-white material-symbols-outlined text-3xl">
                done
            </span>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                Registro de Planilla
            </span>
        </template>
        <template #body>
            <div v-if="typeChange == 'done'" class="space-y-2 mb-4">
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
