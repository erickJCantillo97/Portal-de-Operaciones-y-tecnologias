<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import '../../../sass/dataTableCustomized.scss'
import { MagnifyingGlassPlusIcon, SparklesIcon, PhotoIcon, ViewColumnsIcon } from '@heroicons/vue/24/outline'
import { useSweetalert } from '@/composable/sweetAlert'
import Loading from '@/Components/Loading.vue';
import OverlayPanel from 'primevue/overlaypanel'
import CustomModal from '@/Components/CustomModal.vue'
import axios from 'axios'
import Listbox from 'primevue/listbox'
import FileUpload from 'primevue/fileupload'
import DataView from 'primevue/dataview'
import Button from 'primevue/button'
import Image from 'primevue/image'
import Divider from 'primevue/divider'
import Tag from 'primevue/tag'
import CustomDataTable from '@/Components/CustomDataTable.vue'

const props = defineProps({
    projects: Array,
})
const modalDocument = ref(false)
const { toast } = useSweetalert()
const { confirmDelete } = useSweetalert()
const projectSelect = ref()
const op = ref()

const addAct = (event, p) => {
    console.log(p)
    projectSelect.value = p
    console.log(event)
    op.value.toggle(event)
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

// Formatear el número en moneda (USD)

const items = [{
    title: 'Crear cronograma nuevo',
    description: 'Aqui podra crear un cronograma vacio',
    icon: MagnifyingGlassPlusIcon,
    background: 'bg-pink-500',
    page: 'createSchedule.create'
},
{
    title: 'Crear cronograma con asistente',
    description: 'Podra crear el proyecto con un asistente inteligente',
    icon: SparklesIcon,
    background: 'bg-yellow-500',
    page: 'wizard',
    dev: true
},
{
    title: 'Crear proyecto desde proyecto anterior',
    description: 'Actualmente en desarollo',
    icon: PhotoIcon,
    background: 'bg-green-500',
    dev: true
},
{
    title: 'Crear proyecto desde plantilla prediseñada',
    description: 'Actualmente en desarrollo',
    icon: ViewColumnsIcon,
    background: 'bg-blue-500',
    dev: true
},
]
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


const columnas = [
    // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
    { field: 'SAP_code', header: 'Código SAP', filter: true, sortable: true },
    { field: 'name', header: 'Nombre', filter: true, sortable: true },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true },
    { field: 'cost_sale', header: 'Costo de Venta', type: 'currency', filter: true, sortable: true },
    { field: 'end_date', header: 'Fecha Finalización', filter: true, sortable: true },
    {
        field: 'status', header: 'Estado', filter: true, sortable: true, type: 'tag', filtertype: 'EQUALS',
        severitys: [
            { text: 'DISEÑO Y CONSTRUCCIÓN', severity: 'info', class: '' },
            { text: 'CONSTRUCCIÓN', severity: 'info', class: '' },
            { text: 'DISEÑO', severity: 'warning', class: '' },
            { text: 'GARANTIA', severity: 'info', class: '' },
            { text: 'SERVICIO POSTVENTA', severity: 'success', class: '' },
            { text: 'SIN ESTADO', severity: 'danger', class: 'animate-pulse' }
        ]
    },
]

//#region Botones de CustomDatatable
const buttons = [
    { event: 'addDoc', severity: 'primary', class: '', icon: 'fa-solid fa-cloud-arrow-up', text: true, outlined: false, rounded: false },
    { event: 'addAct', severity: 'primary', class: '', icon: 'fa-regular fa-calendar-plus', text: true, outlined: false, rounded: false },
    { event: 'editClic', severity: 'primary', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'deleteClic', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

</script>

<template>
    <AppLayout>
        <div class="w-full h-[89vh] overflow-y-auto">
            <CustomDataTable title="Proyectos" :data="projects" :columnas="columnas" :actions="buttons" @addDoc="addDoc"
                @addAct="addAct" @editClic="editClic" @deleteClic="deleteClic">
                <template #buttonHeader>
                    <Button @click="addItem" severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>

    <OverlayPanel ref="op">
        <div>
            <h2 class="text-base font-semibold leading-6 text-gray-900">Creación o edición de cronograma</h2>
            <p class="mt-1 text-sm text-gray-500">Aquí podrá escoger como desea crear el cronograma del proyecto.</p>

            <ul role="list" class="grid grid-cols-1 gap-6 py-6 mt-6 border-t border-b border-gray-200 sm:grid-cols-2">
                <li v-for="(item, itemIdx) in items" :key="itemIdx" class="flow-root">
                    <div @click="router.get(route(item.page, projectSelect.id))"
                        class="relative flex items-center p-2 -m-2 space-x-4 rounded-xl focus-within:ring-2 focus-within:ring-indigo-500 hover:bg-gray-50">
                        <div
                            :class="[item.background, 'flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-lg']">
                            <component :is="item.icon" class="w-6 h-6 text-white" aria-hidden="true" />
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">
                                <a href="#" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true" />
                                    <span>{{ item.title }}</span>
                                    <span aria-hidden="true"> &rarr</span>
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">{{ item.description }}</p>
                            <Tag v-if="item.dev" value="En Desarrollo" severity="info" class="animate-pulse" rounded :pt="{
                                value: '!text-xs'
                            }" />
                        </div>
                    </div>
                </li>
            </ul>
            <!-- <div class="flex mt-4">
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Or start from an empty project
                        <span aria-hidden="true"> &rarr</span>
                    </a>
                </div> -->
        </div>
    </OverlayPanel>

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
            <a :href="archivo">Haz clic aquí para ver el archivo PDF</a>
        </object>
        <div class="flex justify-end p-2">
            <a :href="archivo" target="_blank" rel="noopener noreferrer">
                <Button label="Descargar" icon="fa-solid fa-save" outlined rounded class="!h-8" />
            </a>
        </div>
    </OverlayPanel>
</template>
