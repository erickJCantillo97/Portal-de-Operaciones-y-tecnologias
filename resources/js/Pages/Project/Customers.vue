<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dropdown from 'primevue/dropdown';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import { useConfirm } from "primevue/useconfirm";
import axios from 'axios';
import TextInput from '../../Components/TextInput.vue';
import Button from '../../Components/Button.vue';

const confirm = useConfirm();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const open = ref(false)


const props = defineProps({
    customers: Array,
})

//#region UseForm
const formData = useForm({
    customer: {},
});
//#endregion

onMounted(() => {
    initFilters();
    getElements();
})

/* SUBMIT*/
const submit = () => {
    loading.value = true;

    if (formData.customer.id == null) {
        router.post(route('customers.store'), formData.customer, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast(' Cliente creado exitosamente', 'success');
            },
            onError: (errors) => {
                toast('Por favor diligencie todos los campos', 'error');
            },
            onFinish: visit => {
                loading.value = false;
            }
        })
        return 'creado';
    }

    router.put(route('customers.update', formData.customer.id), formData.customer, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast('¡Cliente actualizado exitosamente!', 'success');
        },
        onError: (errors) => {
            toast('¡Ups! Ha surgido un error', 'error');
        },
        onFinish: visit => {
            loading.value = false;
        }
    })

}

const addItem = () => {
    formData.reset();
    clearErrors();
    open.value = true;
}

const editItem = (customer) => {
    clearErrors();
    formData.customer = customer;
    open.value = true;
};


const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
};

const getElements = () => {
    loading.value = true
    if (props.url) {
        axios.get(route(props.url + '.index'))
            .then((res) => {
                elementos.value = res.data[0];
                loading.value = false;
            })
    }
}

const clearFilter = () => {
    initFilters();
};

const clearErrors = () => {
    router.page.props.errors = {};
};

const formatDate = (value) => {
    return value.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
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
    XLSX.writeFile(workbook, 'Lista de Clientes_' + customers.nit + '_' + customers.name + ".xlsb");
};

const showShips = (selectCustomer) => {

    router.get(route('ships.index'), { id: selectCustomer.id })
}

</script>

<template>
    <AppLayout>
        <div class="w-full overflow-y-auto custom-scroll">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Clientes
                    </h1>
                </div>

                <div title="Agregar Cliente" class="">
                    <Button @click="addItem()" severity="success">
                        <PlusIcon class="w-6 h-6" aria-hidden="true" />
                        Agregar
                    </Button>
                </div>
            </div>
            <!--DATATABLE-->
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="customers" v-model:filters="filters"
                dataKey="id" filterDisplay="menu" :loading="props.loading"
                :globalFilterFields="['NIT', 'name', 'type', 'email']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

                <template #header>
                    <div class="flex justify-between w-full h-8 mb-2">
                        <div class="flex space-x-4">
                            <div class="w-8" title="Filtrar Clientes">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                                </Button>
                            </div>

                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search" title="Buscar Clientes"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="NIT" header="NIT"></Column>
                <Column field="name" header="Nombre"></Column>
                <Column field="country" header="Pais"></Column>
                <Column field="type" header="Tipo"></Column>
                <Column field="email" header="Correo"></Column>

                <!--ACCIONES-->
                <Column header="Acciones" class="space-x-3">
                    <template #body="slotProps">
                        <!--BOTÓN UNIDADES-->
                        <div
                            class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">
                            <div title="Unidades del Cliente">
                                <Button severity="primary" @click="showShips(slotProps.data)" class="hover:bg-primary">
                                    <i class="w-4 h-4 fa fa-ship"></i>
                                </Button>
                            </div>
                            <!--BOTÓN EDITAR-->
                            <div title="Editar Cliente">
                                <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                    <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                            <!--BOTÓN ELIMINAR-->
                            <div title="Eliminar Cliente">
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Cliente?', 'customers')"
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
                                        <DialogTitle as="h3" class="text-xl font-semibold text-primary ">{{
                                            formData.customer.id !=
                                            null ? 'Editar ' : 'Crear' }} Cliente
                                        </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                        <div class="p-2 mt-2 space-y-4 border border-gray-200 rounded-lg">
                                            <!-- <TextInput class="mt-2 text-left" label="NIT" placeholder="e.g. 9234232988-0"
                                                v-model="formData.customer.NIT" :error="router.page.props.errors.nit">
                                            </TextInput> -->
                                            <TextInput class="mt-2 text-left" label="Nombre del Cliente"
                                                placeholder="Escriba el nombre del CLiente" v-model="formData.customer.name"
                                                :error="router.page.props.errors.name"></TextInput>
                                            <TextInput class="mt-2 text-left" label="Pais" placeholder="Escriba el Pais"
                                                v-model="formData.customer.country"
                                                :error="router.page.props.errors.country">
                                            </TextInput>
                                            <div class="text-left">
                                                <label class="text-sm font-medium" for="hull_material">Tipo de
                                                    Cliente</label>
                                                <Dropdown id="hull_material" v-model="formData.customer.type"
                                                    :options="['GOBIERNO', 'ARMADOR CIVIL', 'FUERZAS ARMADAS']"
                                                    placeholder="Selecciona un Tipo de Cliente"
                                                    class="w-full -mt-1 rounded-md md:w-14rem" :pt="{
                                                        root: {
                                                            class: 'h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
                                                        },
                                                        input: {
                                                            class: '!text-sm pt-3 pl-2'
                                                        },
                                                        item: {
                                                            class: '!text-sm'
                                                        }
                                                    }" />
                                            </div>
                                            <!-- <TextInput class="mt-2 text-left" label="Tipo de Cliente"
                                                :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.customer.type"
                                                :error="router.page.props.errors.type"></TextInput> -->
                                            <!-- <TextInput class="mt-2 text-left" label="Correo"
                                                placeholder="Escriba su correo electronico"
                                                v-model="formData.customer.email" :error="router.page.props.errors.email">
                                            </TextInput> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="flex px-2 mt-2 space-x-4">
                                    <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                        @click="open = false">Cancelar</Button>
                                    <Button severity="success" :loading="false"
                                        class="text-success hover:bg-success border-success" @click="submit()">
                                        {{ formData.customer.id != null ? 'Actualizar ' : 'Guardar' }}
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
