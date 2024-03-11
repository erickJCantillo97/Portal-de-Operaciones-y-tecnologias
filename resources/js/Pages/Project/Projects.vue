<script setup>
import {
    MagnifyingGlassPlusIcon,
    SparklesIcon,
    PhotoIcon,
    ViewColumnsIcon
} from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { usePermissions } from '@/composable/permission'
import { useSweetalert } from '@/composable/sweetAlert'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'
import Button from 'primevue/button'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import DataView from 'primevue/dataview'
import Divider from 'primevue/divider'
import FileUpload from 'primevue/fileupload'
import Image from 'primevue/image'
import Listbox from 'primevue/listbox'
import Loading from '@/Components/Loading.vue'
import OverlayPanel from 'primevue/overlaypanel'

const { hasPermission } = usePermissions();

const props = defineProps({
    projects: Array,
})

const modalDocument = ref(false)
const { toast } = useSweetalert()
const { confirmDelete } = useSweetalert()

const addAct = (event, p) => {
    router.get(route('createSchedule.create', p.id))
    // projectSelect.value = p
    // op.value.toggle(event)
}

const editClic = (event, data) => {
    router.get(route('projects.edit', data.id))
}

const addItem = () => {
    router.get(route('projects.create'))
    clearError()
}
const deleteClic = (event, data) => {
    confirmDelete(data.id, 'Proyecto', 'projects')
}

const tipologias = ref(null)
const project = ref()
const tipologia = ref()
const listTipologia = ref(0)

const getTipologias = (p) => {
    axios.get(route('get.tipologias', p.id)).then((res) => {
        tipologias.value = Object.values(res.data.tipologias)
        listTipologia.value++
    })
}

const addDoc = (event, data) => {
    const p = data
    tipologia.value = null
    tipologias.value = []
    getTipologias(p)
    project.value = p
    modalDocument.value = true
}

const clearError = () => {
    router.page.props.errors = {}
}

//#region subida de documentos
const files = ref([])
const fileup = ref(Math.random() * (10))

const onSelectedFiles = (event) => {
    files.value = event.files;
}

const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);
};

const formatSize = (bytes) => {
    const k = 1024;
    const dm = 1;
    const sizeType = ['B', 'KB', 'MB', 'GB']
    if (bytes === 0) {
        return `0 byte`;
    }

    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

    return `${formattedSize} ${sizeType[i]}`;
}

const uploadForm = useForm({
    files: [],
    project_id: null,
    tipologia_id: null,
    tipologia_name: null
})

const uploadEvent = () => {
    uploadForm.files = files.value
    uploadForm.project_id = project.value.id
    uploadForm.tipologia_id = tipologia.value.id
    uploadForm.tipologia_name = tipologia.value.name
    uploadForm.post(route('gestion.documental.store'), {
        onSuccess: (response) => {
            toast('Se agrego la documentacion', 'success')
            fileup.value = Math.random() * (10)
            selectTipologia()
            var index = tipologias.value.indexOf(tipologia.value)
            tipologias.value[index].count = tipologias.value[index].count + files.value.length
            listTipologia.value++
        },
        onError: (error) => {
            console.log(error)
        }
    })
}

function formatDateTime24h(dateTime) {
    return new Date(dateTime).toLocaleString('es-CO',
        { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false })
}

const tipologiaFiles = ref([])
const selectTipologia = () => {
    fileup.value = Math.random() * (10)
    if (tipologia.value) {
        axios.get(route('get.files.project.tipologia', { porjectID: project.value.id, tipologiaID: tipologia.value.id })).then((response) => {
            tipologiaFiles.value = response.data.files
        })
    } else {
        tipologiaFiles.value = []
    }
}

const pdf = ref()
const archivo = ref()
const archivoData = ref()
const showPdf = (event, data) => {
    archivoData.value = data
    pdf.value.toggle(event)
    fetch(data.filePath).then(response => response.blob()).then(blob => {
        const file = new File([blob], 'Documento.pdf', { type: 'application/pdf' });
        archivo.value = URL.createObjectURL(file)
    })
}
//#endregion

//#region CustomDataTable
const columnas = [
    // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
    { field: 'name', header: 'Nombre', rowClass: "underline !text-left !text-sm", filter: true, sortable: true, type: 'button', event: 'goToProjectOverview', severity: 'info', text: true, },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true, visible: false },
    { field: 'contract.contract_id', header: 'Contrato', filter: true, sortable: true },
    { field: 'cost_sale', header: 'Costo de Venta', type: 'currency', filter: true, sortable: true },
    { field: 'SAP_code', header: 'Código SAP', filter: true, sortable: true },
    { field: 'contract.end_date', header: 'Fecha Finalización', filter: true, sortable: true },
    {
        field: 'status', header: 'Estado', filter: true, sortable: true, type: 'tag', filtertype: 'EQUALS', visible: false,
        severitys: [
            { text: 'DISEÑO Y CONSTRUCCIÓN', severity: 'primary', class: '' },
            { text: 'CONSTRUCCIÓN', severity: 'success', class: '' },
            { text: 'DISEÑO', severity: 'info', class: '' },
            { text: 'GARANTIA', severity: 'warning', class: '' },
            { text: 'SERVICIO POSTVENTA', severity: 'success', class: '' },
            { text: 'SIN ESTADO', severity: 'danger', class: 'animate-pulse' }
        ]
    },
]

const filterButtons = [
    { field: 'status', label: 'CONSTRUCCIÓN', data: 'CONSTRUCCIÓN', severity: 'success' },
    { field: 'status', label: 'DISEÑO Y CONSTRUCCIÓN', data: 'DISEÑO Y CONSTRUCCIÓN', severity: 'primary' },
    { field: 'status', label: 'DISEÑO', data: 'DISEÑO', severity: 'info' },
    { field: 'status', label: 'GARANTIA ', data: 'GARANTIA', severity: 'warning' },
]

const buttons = [
    { event: 'addDoc', severity: 'primary', class: '', icon: 'fa-solid fa-cloud-arrow-up', text: true, outlined: false, rounded: false },
    { event: 'addAct', severity: 'primary', class: '', icon: 'fa-regular fa-calendar-plus', text: true, outlined: false, rounded: false, show: hasPermission('schedule create') },
    { event: 'editClic', severity: 'primary', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false, show: hasPermission('projects edit') },
    { event: 'deleteClic', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false, show: hasPermission('projects delete') },
]

const goToProjectOverview = (event, data) => {
    try {
        router.get(route('projects.goToProjectOverview', data.id))
    } catch (error) {
        console.log(error)
    }
}
//#endregion
</script>

<template>
    <AppLayout>
        <div class="w-full h-[89vh] overflow-y-auto">
            <CustomDataTable title="Proyectos" :filterButtons="filterButtons" :data="projects" :rows-default="100"
                :columnas="columnas" :actions="buttons" @addDoc="addDoc" @addAct="addAct" @editClic="editClic"
                @deleteClic="deleteClic" @goToProjectOverview="goToProjectOverview">
                <template #buttonHeader>
                    <Button @click="addItem" severity="success" v-if="hasPermission('projects create')"
                        icon="fa-solid fa-plus" label="Agregar" outlined />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>

    <CustomModal v-model:visible="modalDocument" :maximizable="true" width="95vw">
        <template #icon>
            <i class="fa-solid fa-cloud-arrow-up text-white text-xl"></i>
        </template>
        <template #titulo>
            <p class="text-white">Agregar archivos al proyecto {{ project.name }}</p>
        </template>
        <template #body>
            <div v-if="tipologias[0]" class="grid grid-cols-5 gap-2 max-h-full">
                <div class="col-span-2">
                    <p class="w-full text-center font-bold text-primary text-lg">{{
                        tipologias[0].Subserie }}</p>
                    <Listbox :key="listTipologia" v-model="tipologia" :options="tipologias" filter optionLabel="name"
                        @click="selectTipologia()" listStyle="max-height:60vh" class="w-full md:w-14rem" :pt="{
                            filterInput: { class: 'rounded-md border !h-8 border-gray-200' },
                            item: { class: 'hover:bg-blue-100 text-md !px-1 !py-0.5' },
                        }">
                        <template #option="slotProps">
                            <div class="grid grid-cols-7 h-min">
                                <p class="col-span-6 flex items-center">{{ slotProps.option.name }}</p>
                                <div class="flex space-x-1 rounded-md p-1 justify-end text-right items-center"
                                    v-if="slotProps.option.count != 0">
                                    <p class="text-sm">{{ slotProps.option.count }}</p>
                                    <i class="fa-regular fa-file text-danger border p-1 rounded-md border-danger">
                                    </i>
                                </div>
                            </div>
                        </template>
                    </Listbox>
                </div>
                <div v-if="tipologia" class="col-span-3 h-[60vh] space-y-2">
                    <div class="border rounded-md">
                        <span class="flex space-x-2 p-2">
                            <p class="font-bold">Tipologia:</p>
                            <p>{{ tipologia.name }}</p>
                        </span>
                        <Divider />
                        <div v-if="tipologiaFiles.length > 0" class="overflow-y-auto h-[40vh]">
                            <DataView :value="tipologiaFiles" class="w-full overflow-y-auto">
                                <template #list="slotProps">
                                    <div class="p-1 flex justify-between items-center w-full">
                                        <div class="flex">
                                            <i v-if="slotProps.data.filePath.slice(slotProps.data.filePath.lastIndexOf('.') + 1) == 'pdf'"
                                                class=" text-danger fa-regular border p-1 rounded-md border-danger text-xl w-9 flex items-center justify-center fa-file-pdf"></i>
                                            <Image v-else :src="slotProps.data.filePath" preview class="w-9">
                                                <template #image>
                                                    <div class="flex items-center h-full">
                                                        <img :src="slotProps.data.filePath" alt="image" />
                                                    </div>
                                                </template>
                                                <template #preview="slotProps1">
                                                    <img :src="slotProps.data.filePath" class="!max-w-[80vw] !max-h-[80vh]"
                                                        alt="preview" :style="slotProps1.style"
                                                        @click="slotProps1.previewCallback" />
                                                </template>
                                            </Image>
                                            <div class="px-3">
                                                <p class="text-sm">{{ (slotProps.index + 1) + '. ' +
                                                    slotProps.data.tipologia_name }}
                                                </p>
                                                <p class="text-xs font-semibold">{{ slotProps.data.name }}</p>
                                                <span class="flex space-x-2">
                                                    <p class="text-xs">{{ slotProps.data.name_user }} </p>
                                                    <p class="text-xs">{{ formatDateTime24h(slotProps.data.created_at) }}
                                                    </p>
                                                    <p class="text-xs">{{ formatSize(slotProps.data.file_size) }} </p>
                                                    <p class="text-xs"
                                                        v-if="slotProps.data.filePath.slice(slotProps.data.filePath.lastIndexOf('.') + 1) == 'pdf'">
                                                        {{ slotProps.data.num_folios }} folio(s) </p>
                                                </span>
                                            </div>
                                        </div>
                                        <span class="space-x-1">
                                            <Button class="!h-6 !w-6" icon="fa-regular fa-eye" outlined rounded
                                                @click="showPdf($event, slotProps.data)"
                                                v-if="slotProps.data.filePath.slice(slotProps.data.filePath.lastIndexOf('.') + 1) == 'pdf'"
                                                severity="success">
                                            </Button>
                                            <Button class="!h-6 !w-6" icon="fa-regular fa-trash-can" outlined disabled
                                                @click="" rounded severity="danger">
                                            </Button>
                                        </span>
                                    </div>
                                </template>
                            </DataView>
                        </div>
                        <div class="flex items-center justify-center h-[30vh]" v-if="tipologiaFiles.length == 0">
                            <span>
                                <i class="w-full text-center text-2xl text-danger fa-solid fa-file-circle-exclamation"></i>
                                <p class="w-full text-center font-bold text-danger">
                                    Aun no hay archivos
                                </p>
                            </span>
                        </div>
                        <div class="h-full flex items-center" v-if="tipologia.count > 0 && tipologiaFiles.length == 0">
                            <Loading message="Cargando archivos" />
                        </div>
                    </div>
                    <div class="">
                        <FileUpload ref="fileUp" :multiple="true" accept="image/*,application/pdf" :key="fileup"
                            invalidFileTypeMessage="Solo se aceptan imagenes o pdf" :maxFileSize="10000000"
                            @select="onSelectedFiles" :pt="{
                                content: { class: '!p-0.5' },
                                message: { class: 'py-0.5' }
                            }">
                            <template #header="{ chooseCallback, clearCallback, files }">
                                <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                                    <div class="flex gap-2">
                                        <Button @click="chooseCallback()" outlined class="!h-8" severity="primary"
                                            icon="fa-solid fa-folder-open" label="Seleccionar">
                                        </Button>
                                        <Button @click="uploadEvent()" outlined severity="success" class="!h-8"
                                            label="Subir" icon="fa-solid fa-cloud-arrow-up" :loading="uploadForm.processing"
                                            :disabled="!files || files.length === 0">
                                        </Button>
                                        <Button @click="clearCallback()" outlined icon="fa-regular fa-trash-can"
                                            class="!h-8" label="Quitar todo" severity="danger"
                                            :disabled="!files || files.length === 0">
                                        </Button>
                                    </div>
                                </div>
                            </template>
                            <template #content="{ files, removeFileCallback }">
                                <DataView v-if="files.length > 0" :value="files"
                                    class="w-full max-h-[20vh] overflow-y-auto">
                                    <template #list="slotProps">
                                        <div class="p-1 flex justify-between items-center w-full">
                                            <div class="flex">
                                                <i :class="slotProps.data.type == 'application/pdf' ? 'fa-regular fa-file-pdf' : 'fa-regular fa-image'"
                                                    class=" text-danger border p-1 rounded-md border-danger text-xl w-9 flex items-center justify-center"></i>
                                                <div class="px-3">
                                                    <p class="text-sm">{{ slotProps.data.name }} </p>
                                                    <p class="text-xs">{{ formatSize(slotProps.data.size) }} </p>
                                                </div>
                                            </div>
                                            <Button class="!h-6 !w-6" icon="fa-regular fa-trash-can" outlined
                                                @click="onRemoveTemplatingFile(slotProps.data, removeFileCallback, slotProps.index)"
                                                rounded severity="danger">
                                            </Button>
                                        </div>
                                    </template>
                                </DataView>
                            </template>
                            <template #empty>
                                <div class="flex items-center flex-col py-3">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-blue-800" />
                                    <p class="mt-2 mb-0">Arrastra los archivos a adjuntar en la tipologia</p>
                                    <p class="text-xs text-gray-700 ">Se aceptan imagenes y PDF</p>
                                </div>
                            </template>
                        </FileUpload>

                    </div>
                </div>
                <Loading v-else class="col-span-3 h-full pt-10" message="Seleccione una tipologia" />
            </div>
            <Loading class="mt-5" v-else message="Cargando tipologias" />
        </template>
        <template #footer>
            <Button severity="danger" outlined label="Cerrar" icon="fa-solid fa-xmark" @click="modalDocument = false"
                class="!h-8"></Button>
        </template>
    </CustomModal>

    <OverlayPanel ref="pdf">
        <div class="">
            <p class="text-sm font-semibold">{{ archivoData.name }}Nombre de archivo quemado</p>
            <span class="flex space-x-2 p-1 cursor-default">
                <p title="Encargado" class="text-sm border rounded-md px-2">{{ archivoData.name_user }}</p>
                <p title="Fecha subida" class="text-sm border rounded-md px-2">{{ formatDateTime24h(archivoData.created_at)
                }} </p>
                <p title="Tamaño" class="text-sm border rounded-md px-2">{{ formatSize(archivoData.file_size) }} </p>
                <p title="Cantidad de folios" class="text-sm border rounded-md px-2">
                    {{ archivoData.num_folios }} folio(s) </p>
            </span>
        </div>
        <object :data="archivo + '#toolbar=0'" type="application/pdf" style="width:60vw;height:60vh;">
            <a :href="archivo" target="_blank">Haz clic aquí para ver el archivo PDF</a>
        </object>
        <div class="flex justify-end p-2a">
            <a :href="archivo" target="_blank" rel="noopener noreferrer">
                <Button label="Descargar" icon="fa-solid fa-save" outlined rounded class="!h-8" />
            </a>
        </div>
    </OverlayPanel>
</template>
