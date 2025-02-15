<script setup>
import { ref, onMounted } from "vue"
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import { useToast } from "primevue/usetoast"
import CustomInput from "@/Components/CustomInput.vue"
import CustomModal from '@/Components/CustomModal.vue'
import draggable from "vuedraggable";
import Knob from "primevue/knob"
import Tag from "primevue/tag"

const { truncateString, formatUTCOffset } = useCommonUtilities()

const pending = ref([])
const inProcess = ref([])
const done = ref([])

const toast = useToast()
const inProcessModal = ref(false)
const doneModal = ref(false)
const typeChange = ref('')
const loading = ref(false)
const moveList = ref('') // De donde proviene (oldIndex)
const sourceListIndex = ref(null) // Donde está (newIndex)

const route = ref()
const file = ref()

const quotesTest = ref([
    {
        title: 'Hola'
    }
])

const inProcessFormData = ref({
    id: null,
    schedule_id: null,
    progress: 0,
    start: null,
    end: null
})

const doneFormData = ref({
    id: null,
    project_id: null,
    bloque: '',
    sistema_grupo: '',
    document_ref: '',
    start: null,
    end: null,
    requirements: null,
    files: null,
    note: ''
})

const getTaskPendientes = () => {
    inProcessModal.value = false
    doneModal.value = false
    axios.get(route('get.times.employees'))
        .then((res) => {
            pending.value = res.data.times
            inProcess.value = res.data.inProcess
            done.value = res.data.done
        })
}

onMounted(() => {
    // getTaskPendientes()
})

const handleDragStart = (event, move) => {
    moveList.value = move
}

const handleDragEnd = (event) => {
    sourceListIndex.value = event.newIndex;
}

const handleDrop = (type) => {

    switch (type) {
        case 'pending':
            if (moveList.value == 'inProcess')
                toast.add({ severity: 'success', group: 'customToast', text: 'Añadido en Actividades Pendientes', life: 2000 })
            else if (moveList.value == 'done') {
                toast.add({ severity: 'success', group: 'customToast', text: 'Añadido en Actividades Pendientes', life: 2000 })
            }
            break;
        case 'inProcess':
            if (moveList.value == 'pending') {
                inProcessModal.value = true
            }
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

//# region InProcess's CRUD
const inProcessSubmit = () => {
    try {
        loading.value = true
        if (inProcessFormData.value.id == null) {
            inProcessFormData.value.schedule_id = inProcess.value[sourceListIndex.value].id
            axios.post(route('ProgrammingAdvances.store'), {
                ...inProcessFormData.value,
                start: formatUTCOffset(inProcessFormData.value.start),
                end: formatUTCOffset(inProcessFormData.value.end)
            }).then((res) => {
                toast.add({
                    severity: 'success',
                    group: 'customToast',
                    text: 'Añadido en Actividades En Proceso',
                    life: 2000
                })
                inProcessModal.value = false
            })
        } else {
            axios.put(route('ProgrammingAdvances.update', inProcessFormData.value.id), {
                ...inProcessFormData.value,
                start: formatUTCOffset(inProcessFormData.value.start),
                end: formatUTCOffset(inProcessFormData.value.end)
            }).then((res) => {
                inProcessModal.value = false
                toast.add({
                    severity: 'success',
                    group: 'customToast',
                    text: 'Se ha actualizado la actividad correctamente',
                    life: 2000
                })
            })
        }
        getTaskPendientes()
    } catch (error) {
        console.log('Error: ' + error)
    }
}

const inProcessDelete = () => {
    try {
        axios.delete(route(''))
            .then((res) => {

            })
    } catch (error) {
        console.error('Error: ' + error)
    }
    inProcessModal.value = false
}

const inProcessEdit = () => {
    inProcessModal.value = true

    inProcessFormData.schedule_id = inProcess.value[sourceListIndex.value].schedule_id
    inProcessFormData.progress = inProcess.value[sourceListIndex.value].progress

    doneFormData.start = inProcess.value[sourceListIndex.value].start
    doneFormData.end = inProcess.value[sourceListIndex.value].end
}
//# endregion

//# region Done's CRUD
const doneSubmit = () => {
    try {
        loading.value = true
        if (doneFormData.value.id == null) {
            doneFormData.value.project_id = done.value[sourceListIndex.value].id
            axios.post(route('programming.done.store'), {
                ...doneFormData.value,
                start: formatUTCOffset(doneFormData.value.start),
                end: formatUTCOffset(doneFormData.value.end)
            }).then((res) => {
                toast(
                    toast.add({
                        severity: 'success',
                        group: 'customToast',
                        text: 'Añadido en Actividades En Proceso',
                        life: 2000
                    }))
                doneModal.value = false
            })
        } else {
            axios.put(route('programming.done.update', doneFormData.value.id), {
                ...doneFormData.value,
                start: formatUTCOffset(doneFormData.value.start),
                end: formatUTCOffset(doneFormData.value.end)
            }).then((res) => {
                doneModal.value = false
                toast(
                    toast.add({
                        severity: 'success',
                        group: 'customToast',
                        text: 'Se ha actualizado la actividad correctamente',
                        life: 2000
                    }))
            })
        }
        getTaskPendientes()
    } catch (error) {
        console.log('Error: ' + error)
    }
}
//# endregion
</script>
<template>
    <div class="w-full pl-4 bg-white/30 backdrop-blur-sm sticky top-0">
        <h2 class="text-[1.4rem] text-primary font-bold">
            Gestión de Actividades Semanal
        </h2>
    </div>
    <!--ESTIMACIONES PENDIENTES-->
    <div class="grid grid-cols-3 gap-x-2 p-2 ">
        <div class="bg-orange-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-orange-500">
            <div
                class="sticky top-0 mb-1 flex w-full justify-between rounded-lg bg-gradient-to-l from-orange-200 p-2 backdrop-blur-sm">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-circle-exclamation text-orange-400"></i>
                    <h1 class="font-extrabold text-orange-500">
                        Estimaciones Pendientes
                    </h1>
                </div>
                <div class="flex w-8 items-center justify-center rounded-full bg-orange-500 p-1">
                    <span class="text-white text-sm font-semibold">{{ pending.length }}</span>
                </div>
            </div>
            <!-- <draggable :list="pending" @start="handleDragStart($event, 'pending')" @end="handleDragEnd"
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
                        </div>
                    </div>
                </template>
</draggable> -->
            <draggable :list="quotesTest" @start="handleDragStart($event, 'pending')" @end="handleDragEnd"
                @change="handleDrop('pending')" :group="'taskss'" :animation="200" ghost-class="ghost-card"
                class="px-2 h-full opacity-70" key="pending">
                <template #item="{ element }">
                    <div
                        class="mb-2 flex items-center justify-between space-x-8 rounded-lg bg-white p-4 hover:border hover:border-orange-500 cursor-grab">
                        <div class="space-y-4">
                            <h3 class="font-bold text-primary text-sm" v-tooltip="element.title">
                                {{ element.title }}
                            </h3>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>
        <!--EN PROCESO DE FIRMA-->
        <div class="bg-blue-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-primary">
            <div
                class="sticky top-0 mb-1 flex w-full justify-between rounded-lg bg-gradient-to-l from-blue-200 p-2 backdrop-blur-sm">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-clock text-blue-400"></i>
                    <h1 class="font-extrabold text-primary">
                        En Proceso de Firma
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
                            <!-- <Tag v-tooltip="`${truncateString(element.project, 60)}`" severity="info"
                                class="cursor-default" :value="`${truncateString(element.project, 40)}`" rounded :pt="{

                                }" />
                            <div class="flex overflow-x-auto space-x-4 w-[22vw] text-sm cursor-default">
                                <div class="italic p-1 text-nowrap border text-emerald-700 rounded-lg bg-emerald-100">
                                    {{ element.start }} -{{ element.end }}
                                </div>
                            </div> -->
                        </div>
                        <!-- <div class="flex flex-col justify-center items-center space-y-2 -ml-6">
                            <Knob v-model="element.progress" valueTemplate="{value}%" :size="50" readonly />
                            <div class="flex">
                                <Button @click="inProcessEdit()" v-tooltip.bottom="'Editar'" severity="secondary" text
                                    icon="fa-solid fa-pen-to-square hover:text-orange-400" />
                                <Button @click="inProcessDelete()" v-tooltip.bottom="'Eliminar'" severity="secondary"
                                    text icon="fa fa-trash-can hover:text-red-600" />
                            </div>
                        </div> -->
                    </div>
                </template>
            </draggable>
        </div>
        <!--FIRMADAS-->
        <div class="bg-emerald-100 h-full rounded-lg p-4 hover:shadow-md hover:shadow-emerald-500">
            <div
                class="sticky top-0 mb-1 flex w-full justify-between rounded-lg bg-gradient-to-l from-emerald-200 p-2 backdrop-blur-sm">
                <div class="flex space-x-2 justify-center items-center">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <h1 class="font-extrabold text-emerald-600">
                        Firmadas
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
                                <!-- <Button @click="" v-tooltip.bottom="'Editar'" severity="secondary" text
                                    icon="fa-solid fa-pen-to-square hover:text-orange-400" /> -->
                                <Button @click="doneDelete()" v-tooltip.bottom="'Eliminar'" severity="secondary" text
                                    icon="fa fa-trash-can hover:text-red-600" />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>
    </div>

    <!--MODAL DE "EN PROCESO"-->
    <CustomModal v-model:visible="inProcessModal" :closable="false">
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
            <div class="grid grid-cols-2 w-full space-x-2">
                <CustomInput label="Ruta" v-model:input="route" optionLabel="name"
                    placeholder="Escriba o pegue la ruta a la carpeta de los documentos" />
                <CustomInput type="file" v-model:input="file" label="Subir Archivo" acceptFile="application/pdf" />
            </div>
        </template>
        <template #footer>
            <!-- <Button @click="getTaskPendientes" label="Cancelar" severity="danger" icon="fa fa-circle-xmark" />
            <Button @click="inProcessSubmit()" label="Guardar" severity="success" icon="pi pi-save" /> -->
            <Button @click="inProcessModal = false" label="Cancelar" severity="danger" icon="fa fa-circle-xmark" />
            <Button @click="alert('Guardado')" label="Guardar" severity="success" icon="pi pi-save" />
        </template>
    </CustomModal>

    <CustomModal v-model:visible="doneModal" :closable="false">
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
            <h2 class="text-2xl text-center font-extrabold text-primary border-b border-primary">
                {{ done[sourceListIndex].project }}
            </h2>
            <div>
                <span class="text-primary font-bold text-lg">
                    Actividad:
                </span>
                <span class="text-lg">
                    {{ done[sourceListIndex].title }}
                </span>
            </div>
            <span class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <CustomInput label="Bloque" placeholder="Escribir Bloque" id="bloque" type="number"
                    v-model:input="doneFormData.bloque">
                </CustomInput>
                <CustomInput label="Grupo/Sistema" placeholder="Escribir grupo/sistema" id="grupo"
                    v-model:input="doneFormData.sistema_grupo">
                </CustomInput>
                <CustomInput label="Documento de Referencia" placeholder="Escribir Documento de Referencia"
                    id="document" v-model:input="doneFormData.document_ref">
                </CustomInput>
                <div class="flex gap-2">
                    <CustomInput type="time" label="Hora de Inicio" v-model:input="doneFormData.start" />
                    <CustomInput type="time" label="Hora Fin" v-model:input="doneFormData.end" />
                </div>
                <CustomInput class="col-span-1" label="Notas" placeholder="Escribir nota" type="textarea"
                    v-model:input="doneFormData.note">
                </CustomInput>
                <div class="col-span-1 space-y-4">
                    <CustomInput type="file" class="col-span-2" v-model:input="doneFormData.requirements"
                        label="Adjuntar Requerimiento de Materiales"
                        acceptFile="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                        id="requirements">
                    </CustomInput>
                    <CustomInput type="file" class="col-span-2" v-model:input="doneFormData.files"
                        label="Adjuntar Documentos de la Actividad"
                        acceptFile="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                        id="files">
                    </CustomInput>
                </div>
            </span>
        </template>

        <template #footer>
            <Button @click="getTaskPendientes" label="Cancelar" severity="danger" icon="fa fa-circle-xmark" />
            <Button @click="doneSubmit()" :loading label="Guardar" severity="success" icon="pi pi-save" />
        </template>
    </CustomModal>
</template>
