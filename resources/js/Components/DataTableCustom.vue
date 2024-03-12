<script setup>
const { confirmDelete } = useSweetalert();
const { toast } = useSweetalert();
import '../../sass/dataTableCustomized.scss';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline'
import { ref, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3';
import { useSweetalert } from '@/composable/sweetAlert'
import axios from 'axios';
import Button from './Button.vue';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue';
import Dropdown from 'primevue/dropdown';
import plural from 'pluralize-es'
import TextInput from './TextInput.vue';

const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const open = ref(false)

const form = useForm({
    object: {}
})

const elementos = ref([])

const props = defineProps({
    add: {
        type: Boolean,
        default: false
    },

    tittle: String,

    url: { // Hace referecia al nombre del modelo que su usa en las url resorces para formas la rutas (index, create, store, update, Etc..)
        type: String,
        required: true
    },

    headers: {
        type: Array,
        required: true
    },
    loading: {
        type: Boolean,
        default: false
    },

    height: {
        default: '500px',
        type: String
    },
    editItem: {
        type: Boolean,
        default: false
    },
    delete: {
        type: Boolean,
        default: false
    },
    globalSearch: {
        default: true,
        type: Boolean
    }
})

onMounted(() => {
    initFilters();
    getElements();
})

/* Funcion para AGREGAR Elementos al modelo*/
const submit = () => {
    loading.value = true;
    router.post(route(props.url + '.store'), form.object, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast(props.title + ' Creado Exitosamente', 'success');
            getElements()
        },
        onError: (errors) => {
            toast('Ha surgido un error', 'error');
        },
        onFinish: visit => {
            loading.value = false;
        }
    })
}

const deleteItem = async (id) => {
    await confirmDelete(id, 'Actividad', props.url)
    getElements();
}

const addItem = () => {
    resetValues();
    open.value = true;
}

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const getElements = () => {
    loading.value = true
    if (props.url) {
        axios.get(route(props.url + '.index')).then((res) => {
            elementos.value = res.data[0];
            loading.value = false;
        })
    }
}

const clearFilter = () => {
    initFilters();
};

const getPlural = (str) => {
    return plural(str);
}

const resetValues = () => {
    for (let header of props.headers) {
        form.object[header.field] = header.type === 'multiple' ? [] : '';
        router.page.props.errors[header.field] = null
    }
}
</script>

<template>
    <div class="px-auto  w-full">
        <div class="flex items-center mx-2 mb-2">
            <div class="flex-auto">
                <h1 class="text-xl capitalize font-semibold leading-6 text-primary">{{ getPlural(tittle) }}</h1>
            </div>

            <div class="">
                <Button type="button" @click="addItem()" v-if="add" severity="success">
                    <PlusIcon class="h-6 w-6" aria-hidden="true" />
                </Button>
            </div>
        </div>
        <DataTable id="tabla" stripedRows class="p-datatable-sm" :scrollHeight="height" :value="elementos"
            v-model:filters="filters" dataKey="id" filterDisplay="menu" :loading="props.loading"
            :globalFilterFields="['code', 'name', 'email']" currentPageReportTemplate=" {first} al {last} de {totalRecords}"
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
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <MagnifyingGlassIcon class="h-4 w-5  text-gray-400" aria-hidden="true" />
                            </div>
                            <input type="search"
                                class="block w-10/12 rounded-md border-0 py-4 pl-10 text-gray-900 ring-1  ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="filters.global.value" placeholder="Buscar..." />
                        </div>
                    </div>
                </div>
            </template>

            <template v-for="label in headers">
                <template v-if="label.show !== false">
                    <Column :field="label.field" :header="label.header">
                    </Column>
                </template>
            </template>
            <!--ACCIONES-->
            <Column header="Acciones" class="space-x-3" v-if="editItem || props.delete">
                <template #body="slotProps">
                    <!--BOTÓN EDITAR-->
                    <div
                        class="whitespace-normal pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8 flex space-x-2 ">
                        <div class="" v-if="editItem">
                            <Button severity="primary" class="hover:bg-primary">
                                <PencilIcon class="h-4 w-4 " aria-hidden="true" />
                            </Button>
                        </div>
                        <div class="" @click="deleteItem(slotProps.data.id)" v-if="props.delete">
                            <Button severity="danger">
                                <TrashIcon class="h-4 w-4 " aria-hidden="true" />
                            </Button>
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>

    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-30" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed h-screen w-screen inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-30" />
            </TransitionChild>

            <div class="fixed inset-0 z-50 overflow-y-auto h-screen">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel :class="props.heigthDialog"
                            class="relative transform overflow-hidden rounded-lg bg-white px-2 pb-4 pt-2 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg ">
                            <div>
                                <div class="text-center mt-2 px-2">
                                    <DialogTitle as="h3" class="text-xl font-semibold text-primary ">Crear {{ title }}
                                    </DialogTitle>
                                    <div class="mt-2 space-y-4 border border-gray-200 p-2 rounded-lg">
                                        <template v-for="header of headers">
                                            <TextInput class="mt-2 text-left"
                                                v-if="header.input !== false && header.type == 'text'"
                                                :label="header.header" :error="router.page.props.errors[header.field]"
                                                v-model="form.object[header.field]"></TextInput>

                                            <div v-if="header.input !== false && header.type == 'multiple'">
                                                <label for="password"
                                                    class="block capitalize text-left text-sm text-gray-900">{{
                                                        header.header }}</label>
                                                <Dropdown v-model="form.object[header.field]" :options="header.options[0]"
                                                    filter optionLabel="name" placeholder="Selecccionar"
                                                    class="w-full md:w-14rem">
                                                </Dropdown>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 flex space-x-4 px-2">

                                <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                    @click="open = false">Cancelar</Button>
                                <Button severity="success" :loading="loading" class="text-success hover:bg-success"
                                    @click="submit">Guardar</Button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
