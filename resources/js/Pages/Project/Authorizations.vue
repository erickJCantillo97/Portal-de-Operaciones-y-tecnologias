<script setup>
const { confirmDelete } = useSweetalert();
const { toast } = useSweetalert();
const confirm = useConfirm();
const loading = ref(false);
import '../../../sass/dataTableCustomized.scss';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useSweetalert } from '@/composable/sweetAlert';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '../../Components/Button.vue';
import Column from 'primevue/column';
import Combobox from '@/Components/Combobox.vue';
import DataTable from 'primevue/datatable';
import FileUpload from 'primevue/fileupload';
import Tag from 'primevue/tag';
import TextInput from '../../Components/TextInput.vue';

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const open = ref(false)
const contractSelect = ref()
const shipSelect = ref()
const quoteSelect = ref()

const props = defineProps({
    authorizations: Array,
    contracts: Array,
    quotes: Array,
    ships: Array
})

//#region UseForm
const formData = useForm({
    id: props.authorizations?.id ?? '0',
    project_id: props.authorizations?.project_id ?? '0',
    contract_id: props.authorizations?.contract_id ?? '0',
    quote_id: props.authorizations?.quote_id ?? '0',
    start_date: props.authorizations?.start_date ?? '',
    end_date: props.authorizations?.end_date ?? '',
    name: props.authorizations?.name ?? '',
    pdf: null
});
//#endregion

onMounted(() => {
    initFilters();
})

/* SUBMIT*/
const submit = () => {
    loading.value = true;

    if (!contractSelect.value) {
        toast('Por favor seleccione un contrato.', 'error')
        return;
    }

    if (!quoteSelect.value) {
        toast('Por favor seleccione una estimación.', 'error')
        return;
    }

    formData.contract_id = contractSelect.value.id
    formData.quote_id = quoteSelect.value.id

    if (formData.id == 0) {
        router.post(route('authorizations.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast(' Autorización creado exitosamente', 'success');
            },
            onError: (errors) => {
                toast('Por favor diligencie todos los campos.', 'error');
            },
            onFinish: visit => {
                loading.value = false;
            }
        })
        return 'creado';
    }
    router.put(route('authorizations.update', formData.id), formData, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false
            toast('¡Autorización actualizada exitosamente!', 'success');
        },
        onError: (errors) => {
            toast('Por favor diligencie todos los campos.', 'error');
        },
        onFinish: visit => {
            loading.value = false
        }
    })
}

const addItem = () => {
    formData.reset()
    clearErrors()
    contractSelect.value = {}
    quoteSelect.value = {}
    open.value = true
}

const editItem = (authorization) => {
    clearErrors()

    formData.id = authorization.id
    contractSelect.value = authorization.contract
    quoteSelect.value = authorization.quote
    formData.start_date = authorization.end_date
    formData.end_date = authorization.end_date
    formData.name = authorization.name
    formData.pdf = authorization.pdf
    open.value = true;
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const clearFilter = () => {
    initFilters();
};

const clearErrors = () => {
    router.page.props.errors = {};
};

const getContractStatusSeverity = (authorization) => {
    switch (authorization.status) {
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
</script>

<template>
    <AppLayout>
        <div class="w-full overflow-y-auto custom-scroll">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">Autorizaciones</h1>
                </div>

                <div class="" title="Agrega Autorización">
                    <Button @click="addItem()" severity="success">
                        <PlusIcon class="w-6 h-6" aria-hidden="true" />
                        Agregar
                    </Button>
                </div>
            </div>
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="authorizations" v-model:filters="filters"
                dataKey="id" filterDisplay="menu" :loading="loading"
                :globalFilterFields="['name', 'start_date', 'end_date']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

                <template #header>
                    <div class="flex justify-between w-full h-8 mb-2">
                        <div class="flex space-x-4">
                            <div class="w-8" title="Filtrar Autorizaciones">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                                </Button>
                            </div>

                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search" title="Buscar Autorizaciones"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="name" header="Código Autorización"></Column>
                <Column field="start_date" header="Fecha Inicio"></Column>
                <Column field="end_date" header="Fecha Finalización"></Column>
                <Column field="status" header="Estado" sortable>
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.status" :severity="getContractStatusSeverity(slotProps.data)" />
                    </template>
                </Column>

                <!--ACCIONES-->
                <Column header="Acciones" class="space-x-3">
                    <template #body="slotProps">
                        <!--BOTÓN EDITAR-->
                        <div
                            class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">
                            <div title="Editar Autorizaciones">
                                <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                    <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                            <!--BOTÓN ELIMINAR-->
                            <div title="Eliminar Autorizaciones">
                                <Button severity="danger"
                                    @click="confirmDelete(slotProps.data.id, 'Autorización', 'authorizations')"
                                    class="hover:bg-danger">
                                    <TrashIcon class="w-4 h-4 " aria-hidden="true" />
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
                                            0 ? 'Editar ' : 'Crear' }} Autorización
                                        </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                        <div class="p-2 mt-2 space-y-4 border border-gray-200 rounded-lg">
                                            <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                                <Combobox class="mt-2 text-left text-gray-900" label="Contrato"
                                                    placeholder="Seleccione Contrato" :options="contracts"
                                                    v-model="contractSelect" :error="$page.props.errors.contract">
                                                </Combobox>
                                                <Combobox class="mt-2 text-left" label="Estimación"
                                                    placeholder="Seleccione Estimación" :options="quotes"
                                                    v-model="quoteSelect" :error="$page.props.errors.quote">
                                                </Combobox>
                                            </div>

                                            <!--CAMPO FECHA INICIO-->
                                            <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                                <TextInput class="mt-2 text-left" type="text" label="Codigo de autorizacion"
                                                    :placeholder="'Escriba el codigo de autorizacion'"
                                                    v-model="formData.name" :error="$page.props.errors.cost">
                                                </TextInput>

                                                <TextInput class="mt-2 text-left" type="date" label="Fecha de inicio"
                                                    :placeholder="'Escriba el Tipo de Cliente'"
                                                    v-model="formData.start_date" :error="$page.props.errors.cost">
                                                </TextInput>

                                                <!--CAMPO FECHA FINALIZACIÓN-->
                                                <TextInput class="mt-2 text-left" type="date" label="Fecha de Finalización"
                                                    :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.end_date"
                                                    :error="$page.props.errors.end_date">
                                                </TextInput>

                                                <FileUpload chooseLabel="Adjuntar PDF" mode="basic" name="demo[]"
                                                    :multiple="false" accept=".pdf" :maxFileSize="1000000"
                                                    @input="formData.pdf = $event.target.files[0]">
                                                </FileUpload>

                                            </div>
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
    </AppLayout>
</template>
