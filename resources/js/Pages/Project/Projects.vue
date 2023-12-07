<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import '../../../sass/dataTableCustomized.scss'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import { FilterMatchMode, FilterOperator } from 'primevue/api'
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon, MagnifyingGlassPlusIcon, SparklesIcon, EyeIcon, PhotoIcon, TableCellsIcon, ArrowUpCircleIcon, ViewColumnsIcon } from '@heroicons/vue/24/outline'
import { useSweetalert } from '@/composable/sweetAlert'

// import plural from 'pluralize-es'
import Button from '../../Components/Button.vue'
import OverlayPanel from 'primevue/overlaypanel'
import CustomModal from '@/Components/CustomModal.vue'
import axios from 'axios'
import Listbox from 'primevue/listbox'
import FileUpload from 'primevue/fileupload'
import DataView from 'primevue/dataview'

// import Button from 'primevue/button'

const modalDocument = ref(false)
const { toast } = useSweetalert()
const loading = ref(false)
const { confirmDelete } = useSweetalert()
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const projectSelect = ref()
const op = ref()
const toggle = (event, p) => {
    projectSelect.value = p
    op.value.toggle(event)
}

const props = defineProps({
    projects: Array,
})

onMounted(() => {
    initFilters()
})

const addItem = () => {
    router.get(route('projects.create'))
    clearError()
    // formData.reset()
    // open.value = true
}

const tipologias = ref(null)
const project = ref()
const tipologia = ref()
const addDoc = (p) => {
    tipologia.value = null
    if (tipologias.value == null) {
        axios.get(route('get.tipologias')).then((res) => {
            tipologias.value = Object.values(res.data.tipologias)
        })
    }
    project.value = p
    modalDocument.value = true

}


const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
}

const clearFilter = () => {
    initFilters()
}

const clearError = () => {
    router.page.props.errors = {}
}

// Formatear el número en moneda (USD)
const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP'
    })
}

const getStatusSeverity = (status) => {
    switch (status) {
        case 'DISEÑO Y CONSTRUCCIÓN':
            return 'info'

        case 'CONSTRUCCIÓN':
            return 'info'

        case 'DISEÑO':
            return 'warning'

        case 'GARANTIA':
            return 'info'

        case 'SERVICIO POSTVENTA':
            return 'success'

        default:
            return 'danger'
    }
}

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
    page: 'wizard'
},
{
    title: 'Crear proyecto desde proyecto anterior',
    description: 'Actualmente en desarollo',
    icon: PhotoIcon,
    background: 'bg-green-500',
},
{
    title: 'Crear proyecto desde plantilla prediseñada',
    description: 'Actualmente en desarrollo',
    icon: ViewColumnsIcon,
    background: 'bg-blue-500',
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
    uploadForm.tipologia_id = tipologia.value.id_trd_gd
    uploadForm.tipologia_name = tipologia.value.Tipologia
    console.log(uploadForm)
    uploadForm.post(route('gestion.documental.store'), {
        onSuccess: (response) => {
            toast('Se agrego la documentacion', 'success')
            fileup.value = Math.random() * (10)
        },
        onError: (error) => {
            console.log(error)
        }
    })
}

//#endregion


</script>

<template>
    <AppLayout>
        <div class="w-full overflow-y-auto custom-scroll">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Proyectos
                    </h1>
                </div>
                <div class="" title="Agregar Proyecto">
                    <Button @click="addItem()" severity="success">
                        <PlusIcon class="w-6 h-6" aria-hidden="true" />
                        Agregar
                    </Button>
                </div>
            </div>
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="projects" v-model:filters="filters"
                dataKey="id" filterDisplay="menu" :loading="loading"
                :globalFilterFields="['name', 'gerencia', 'start_date', 'end_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

                <template #header>
                    <div class="flex justify-between w-full h-8 mb-2">
                        <div class="flex space-x-4">
                            <div class="w-8" title="Filtrar Proyectos">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                                </Button>
                            </div>

                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search" title="Buscar Proyectos"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="SAP_code" header="Código SAP"></Column>
                <Column field="name" header="Nombre"></Column>
                <Column field="gerencia" header="Gerencia"></Column>
                <Column field="cost_sale" header="Costo de Venta">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.cost_sale) }}
                    </template>
                </Column>
                <Column field="end_date" header="Fecha Finalización"></Column>
                <Column field="status" header="Estado" sortable>
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.status" :severity="getStatusSeverity(slotProps.data.status)"
                            class="animate-pulse" />
                    </template>
                </Column>

                <!--ACCIONES-->
                <Column header="Acciones" class="space-x-3">
                    <template #body="slotProps">
                        <div
                            class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">
                            <div title="Agregar documentos">
                                <Button severity="primary" @click="addDoc(slotProps.data)" class="hover:bg-primary">
                                    <i class="fa-solid fa-cloud-arrow-up h-4 w-4 flex items-center"></i>
                                </Button>
                            </div>
                            <div title="Crear Actividades">
                                <Button severity="primary" @click="toggle($event, slotProps.data)" class="hover:bg-primary">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 16L16 16M16 16L14 16M16 16L16 14M16 16L16 18" stroke="#1C274C"
                                            stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M7 4V2.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M17 4V2.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M21.5 9H16.625H10.75M2 9H5.875" stroke="#1C274C" stroke-width="1.5"
                                            stroke-linecap="round" />
                                        <path
                                            d="M14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12V14C22 17.7712 22 19.6569 20.8284 20.8284C20.1752 21.4816 19.3001 21.7706 18 21.8985"
                                            stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                </Button>
                            </div>

                            <!--BOTÓN EDITAR-->
                            <div title="Editar Proyecto">
                                <Link :href="route('projects.edit', slotProps.data.id)">
                                <Button severity="primary" class="hover:bg-primary">
                                    <PencilIcon class="w-4 h-4" aria-hidden="true" />
                                </Button>
                                </Link>
                            </div>

                            <!--BOTÓN ELIMINAR-->
                            <div title="Eliminar Proyecto">
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Proyecto', 'projects')"
                                    class="hover:bg-danger">
                                    <TrashIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

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

        <CustomModal :visible="modalDocument">
            <template #icon>
                <i class="fa-solid fa-cloud-arrow-up text-white text-xl"></i>
            </template>
            <template #titulo>
                <p class="text-white">Agregar archivos al proyecto {{ project.name }}</p>
            </template>
            <template #body>
                <div class="grid grid-cols-5 gap-2 max-h-full overflow-y-auto">
                    <div class="col-span-2 max-h-full">
                        <p v-if="tipologias" class="w-full h-[5%] text-center font-bold text-primary text-lg">{{
                            tipologias[0].Subserie }}</p>
                        <Listbox v-model="tipologia" :options="tipologias" filter optionLabel="Tipologia"
                            class="w-full md:w-14rem h-[95%]" listStyle="height:40vh" :pt="{
                                filterInput: { class: 'rounded-md border border-gray-200' },
                                root: { class: 'rounded-md border' },
                                item: { class: 'hover:bg-blue-100 text-md' }

                            }">
                            <template #option="slotProps">
                                <div class="grid grid-cols-7">
                                    <p class="col-span-6">{{ slotProps.option.Tipologia }}</p>
                                    <div class="flex space-x-1 rounded-md p-1 justify-end text-right items-center">
                                        <p class="text-sm">10</p>
                                        <i
                                            class="fa-regular fa-file-pdf text-danger border p-1 rounded-md border-danger"></i>
                                    </div>
                                </div>
                            </template>
                        </Listbox>
                    </div>
                    <div class="col-span-3">
                        <div v-if="tipologia" class="h-1/2 p-1">{{ tipologia }}</div>
                        <div v-if="tipologia" class="h-1/2 items-end grid">
                            <FileUpload ref="fileUp" :multiple="true" accept="image/*,application/pdf" :key="fileup"
                                :maxFileSize="10000000" @select="onSelectedFiles" class="">
                                <template #header="{ chooseCallback, clearCallback, files }">
                                    <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                                        <div class="flex gap-2">
                                            <Button @click="chooseCallback()" class="!h-8" severity="primary">
                                                <i class="fa-solid fa-images"> </i>
                                                <p>Seleccionar</p>
                                            </Button>
                                            <Button @click="uploadEvent()" severity="success"
                                                :loading="uploadForm.processing" :disabled="!files || files.length === 0">
                                                <i class="fa-solid fa-cloud-arrow-up" />
                                                <p>Subir</p>
                                            </Button>
                                            <Button @click="clearCallback()" icon="pi pi-times" class="!h-8"
                                                severity="danger" :disabled="!files || files.length === 0">
                                                <i class="pi pi-times"></i>
                                                <p>Quitar</p>
                                            </Button>
                                        </div>
                                    </div>
                                </template>
                                <template #content="{ files, removeFileCallback }">
                                    <DataView :value="files" class="w-full">
                                        <template #list="slotProps">
                                            <div class="p-1 flex justify-between w-full">
                                                <div class="flex">
                                                    <i :class="slotProps.data.type == 'application/pdf' ? 'fa-regular fa-file-pdf' : 'fa-regular fa-image'"
                                                        class=" text-danger border p-1 rounded-md border-danger text-xl w-9 flex items-center justify-center"></i>
                                                    <div class="px-3">
                                                        <p class="text-sm">{{ slotProps.data.name }} </p>
                                                        <p class="text-xs">{{ formatSize(slotProps.data.size) }} </p>
                                                    </div>
                                                </div>
                                                <Button class="!h-4 !w-4"
                                                    @click="onRemoveTemplatingFile(slotProps.data, removeFileCallback, slotProps.index)"
                                                    rounded severity="danger">
                                                    <i class="pi pi-times"></i></Button>
                                            </div>
                                        </template>
                                    </DataView>
                                </template>
                                <template #empty>
                                    <div class="flex items-center flex-col">
                                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-blue-800" />
                                        <p class="mt-2 mb-0">Arrastra los archivos a adjuntar en la tipologia</p>
                                        <p class="text-xs text-gray-700 ">Se aceptan imagenes y PDF</p>
                                    </div>
                                </template>
                            </FileUpload>

                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <Button severity="danger" @click="modalDocument = false" class="hover:bg-danger">
                    Cerrar
                </Button>
            </template>
        </CustomModal>

    </AppLayout>
</template>
