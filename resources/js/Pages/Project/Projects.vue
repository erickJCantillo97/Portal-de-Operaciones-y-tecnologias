<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Calendar from 'primevue/calendar';
import Tag from 'primevue/tag';
import Combobox from '@/Components/Combobox.vue';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon, MagnifyingGlassPlusIcon, SparklesIcon, EyeIcon, PhotoIcon, TableCellsIcon, ViewColumnsIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import { useConfirm } from "primevue/useconfirm";

// import plural from 'pluralize-es'
import TextInput from '../../Components/TextInput.vue';
import Button from '../../Components/Button.vue';
import OverlayPanel from 'primevue/overlaypanel';

// import Button from 'primevue/button';

const confirm = useConfirm();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const open = ref(false);
const project = ref();
const projectSelect=ref();
const op = ref();
const toggle = (event, p) => {
    projectSelect.value=p;
    op.value.toggle(event);
}

const props = defineProps({
    projects: Array,
    ships: Array,
    contracts: Array
})

//#region UseForm
const formData = useForm({
    id: props.projects?.id ?? '0',
    contract_id: props.projects?.contract_id ?? '0',
    authorization_id: props.projects?.authorization_id ?? '0',
    quote_id: props.projects?.quote_id ?? '0',
    ship_id: props.projects?.ship_id ?? '0',
    intern_communications: props.projects?.intern_communications ?? '0',
    name: props.projects?.name ?? '',
    start_date: props.projects?.start_date ?? '',
    end_date: props.projects?.end_date ?? '',
    hoursPerDay: props.projects?.hoursPerDay ?? '8.5',
    daysPerWeek: props.projects?.daysPerWeek ?? '5',
    daysPerMonth: props.projects?.daysPerMonth ?? '20',
    pdf: null
});
//#endregion

onMounted(() => {
    initFilters();
})

//#region SUBMIT
const submit = () => {
    loading.value = true;
    if (formData.id == 0) {
        router.post(route('projects.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast('Proyecto creado exitosamente', 'success');
            },
            onError: (errors) => {
                toast('¡Ups! Ha surgido un error', 'error');
            },
            onFinish: visit => {
                loading.value = false;
            }
        })
        return 'creado';
    }
    router.put(route('projects.update', formData.id), formData, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast('¡Proyecto actualizado exitosamente!', 'success');
        },
        onError: (errors) => {
            toast('¡Ups! Ha surgido un error', 'error');
        },
        onFinish: visit => {
            loading.value = false;
        }
    })
}
//#endregion

const addItem = () => {
    router.get(route('projects.create'))
    // formData.reset();
    // open.value = true;
}

const editItem = (project) => {
    formData.id = project.id;
    formData.name = project.name;
    formData.gerencia = project.gerencia;
    formData.start_date = project.start_date;
    formData.hoursPerDay = project.hoursPerDay;
    formData.daysPerWeek = project.daysPerWeek;
    formData.daysPerMonth = project.daysPerMonth;
    formData.pdf = project.pdf
    open.value = true;
};

const addTask = (id) => {
    router.get(route('createSchedule.create',id));
}


const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const clearFilter = () => {
    initFilters();
};

const formatDate = (value) => {
    return value.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Formatear el número en moneda (USD)
const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP'
    });
};

const getContractStatusSeverity = (project) => {
    switch (project.status) {
        case 'INICIADO':
            return 'info';

        case 'PROCESO':
            return 'warning';

        case 'PENDIENTE':
            return 'danger';

        case 'FINALIZADO':
            return 'success';

        default:
            return null;
    }
};

const exportarExcel = () => {
    //console.log(dt.value)
    // Acquire Data (reference to the HTML table)
    var table_elt = document.getElementById("tabla");

    var workbook = XLSX.utils.table_to_book(table_elt);

    var ws = workbook.Sheets["Sheet1"];
    XLSX.utils.sheet_add_aoa(ws, [
        ["Creado " + new Date().toISOString()]
    ], { origin: -1 });

    // Package and Release Data (`writeFile` tries to write and save an XLSB file)
    XLSX.writeFile(workbook, 'Lista de Contratos_' + project.nit + '_' + project.name + ".xlsb");
};

const items = [
    {
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


</script>

<template>
    <AppLayout>
        <div class="w-full p-4 px-auto">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Proyectos
                    </h1>
                </div>

                <div class="">
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
                            <div class="w-8">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                                </Button>
                            </div>

                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="name" header="Nombre"></Column>
                <Column field="gerencia" header="Gerencia"></Column>
                <Column field="start_date" header="Fecha Inicio"></Column>
                <Column field="end_date" header="Fecha Finalización"></Column>
                <Column field="hoursPerDay" header="Horas por Dia"></Column>
                <Column field="daysPerWeek" header="Dias por Semana"></Column>
                <Column field="daysPerMonth" header="Dias por Mes"></Column>
                <Column field="status" header="Estado" sortable>
                    <template #body="slotProps">
                        <Tag :value="'EJECUCIÓN'" :severity="getContractStatusSeverity('EJECUCIÓN')" />
                    </template>
                </Column>

                <!--ACCIONES-->
                <Column header="Acciones" class="space-x-3">
                    <template #body="slotProps">
                        <!--BOTÓN EDITAR-->
                        <div
                            class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">
                            <div>
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
                            <div>
                                <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                    <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                            <!--BOTÓN ELIMINAR-->
                            <div>
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Proyecto', 'projects')"
                                    class="hover:bg-danger">
                                    <TrashIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                            <!--BOTÓN AGREGAR TAREAS-->
                            <div>
                                <Button severity="success" @click=""
                                    class="hover:bg-success">
                                    <EyeIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!--MODAL DE FORMULARIO-->
        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-30" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                    leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 z-30 w-screen h-screen transition-opacity bg-gray-500 bg-opacity-75" />
                </TransitionChild>

                <div class="fixed inset-0 z-50 h-screen overflow-y-auto">
                    <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel :class="props.heigthDialog"
                                class="relative px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg ">
                                <div>
                                    <div class="px-2 mt-2 text-center">
                                        <DialogTitle as="h3" class="text-xl font-semibold text-primary ">{{ formData.id !=
                                            0 ? 'Editar ' : 'Crear' }} Proyecto
                                        </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                        <div class="p-2 mt-2 space-y-4 border border-gray-200 rounded-lg">
                                            <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                                <Combobox class="mt-2 text-left text-gray-900" label="Contrato"
                                                    placeholder="Seleccione Contrato" :options="contracts"
                                                    v-model="contractSelect">
                                                </Combobox>

                                                <Combobox class="mt-2 text-left" label="Buque"
                                                    placeholder="Seleccione Buque" :options="ships" v-model="shipSelect">
                                                </Combobox>
                                            </div>

                                            <!--CAMPO FECHA INICIO-->
                                            <TextInput class="mt-2 text-left" type="date" label="Fecha De Inicio"
                                                :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.start_date"
                                                :error="$page.props.errors.start_date">
                                            </TextInput>

                                            <!-- <div class="mt-2">
                                                <label for="type"
                                                    class="block text-sm text-left text-gray-900 capitalize">Tipo de
                                                    Cliente</label>
                                                <Dropdown v-model="form.type" :options="types" filter optionLabel="name"
                                                    placeholder="Selecccionar" class="w-full md:w-14rem">
                                                </Dropdown>
                                            </div> -->

                                            <!--CAMPO FECHA FINALIZACIÓN-->
                                            <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                                <TextInput class="mt-2 text-left" type="date" label="Fecha de Finalización"
                                                    :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.end_date"
                                                    :error="$page.props.errors.end_date">
                                                </TextInput>
                                            </div>

                                            <TextInput class="mt-2 text-left" label="Horas por Dia"
                                                :placeholder="'Escriba Numero de Horas por Dia'"
                                                v-model="formData.hoursPerDay"
                                                :error="router.page.props.errors.hoursPerDay"></TextInput>

                                            <TextInput class="mt-2 text-left" label="Dias por Semana"
                                                :placeholder="'Escriba Numero de Horas por Dia'"
                                                v-model="formData.daysPerWeek"
                                                :error="router.page.props.errors.daysPerWeek"></TextInput>

                                            <TextInput class="mt-2 text-left" label="Dias por Mes"
                                                :placeholder="'Escriba Numero de Horas por Dia'"
                                                v-model="formData.daysPerMonth"
                                                :error="router.page.props.errors.daysPerMonth"></TextInput>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex px-2 mt-2 space-x-4">
                                    <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                        @click="open = false">Cancelar</Button>
                                    <Button severity="success" :loading="false"
                                        class="text-success hover:bg-success border-success" @click="submit()">
                                        {{ formData.id != 0 ? 'Actualizar ' : 'Guardar' }}
                                    </Button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <OverlayPanel ref="op">
            <div>
                <h2 class="text-base font-semibold leading-6 text-gray-900">Creacion o edicion de cronograma</h2>
                <p class="mt-1 text-sm text-gray-500">Aqui podra escoger como desea crear el cronograma del proyecto.</p>

                <ul role="list" class="mt-6 grid grid-cols-1 gap-6 border-b border-t border-gray-200 py-6 sm:grid-cols-2">
                    <li v-for="(item, itemIdx) in items" :key="itemIdx" class="flow-root">
                        <div @click="router.get(route(item.page, projectSelect.id))"
                            class="relative -m-2 flex items-center space-x-4 rounded-xl p-2 focus-within:ring-2 focus-within:ring-indigo-500 hover:bg-gray-50">
                        <div
                            :class="[item.background, 'flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-lg']">
                            <component :is="item.icon" class="h-6 w-6 text-white" aria-hidden="true" />
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">
                                <a href="#" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true" />
                                    <span>{{ item.title }}</span>
                                    <span aria-hidden="true"> &rarr;</span>
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">{{ item.description }}</p>
                        </div>
                    </div>
                    </li>
                </ul>
                <!-- <div class="mt-4 flex">
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Or start from an empty project
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div> -->
            </div>
        </OverlayPanel>
    </AppLayout>
</template>
