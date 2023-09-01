<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Calendar from 'primevue/calendar';
import Tag from 'primevue/tag';
import Dropdown from 'primevue/dropdown';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import DownloadExcelIcon from '@/Components/DownloadExcelIcon.vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import { useConfirm } from "primevue/useconfirm";
import axios from 'axios';
// import plural from 'pluralize-es'
import TextInput from '../../Components/TextInput.vue';
import Button from '../../Components/Button.vue';
// import Button from 'primevue/button';

const confirm = useConfirm();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const open = ref(false)


const props = defineProps({
    contracts: Array,
    gerencias: Array
})

//#region UseForm
const formData = useForm({
    id: props.contracts?.id ?? '0',
    customer_id: props.contracts?.customer_id ?? '0',
    name: props.contracts?.name ?? '',
    gerencia: props.contracts?.gerencia ?? '',
    cost: props.contracts?.cost ?? '0',
    start_date: props.contracts?.start_date ?? '',
    end_date: props.contracts?.end_date ?? '',
});
//#endregion

onMounted(() => {
    initFilters();
})

/* SUBMIT*/
const submit = () => {
    loading.value = true;
    if (formData.id == 0) {
        router.post(route('contracts.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast(' Contrato creado exitosamente', 'success');
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
    router.put(route('contracts.update', formData.id), formData, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast('Contrato actualizado exitosamente!', 'success');
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
    open.value = true;
}

const editItem = (contract) => {
    formData.id = contract.id;
    formData.name = contract.name;
    formData.gerencia = contract.gerencia;
    formData.cost = contract.cost;
    formData.start_date = contract.start_date;
    formData.end_date = contract.end_date;
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

const getContractStatusSeverity = (contract) => {
    switch (contract.status) {
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
    XLSX.writeFile(workbook, 'Lista de Contratos_' + contract.nit + '_' + contract.name + ".xlsb");
};


</script>

<template>
    <AppLayout>
        <div class="px-auto  w-full p-4">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl capitalize font-semibold leading-6 text-primary">Contratos</h1>
                </div>

                <div class="">
                    <Button @click="addItem()" severity="success">
                        <PlusIcon class="h-6 w-6" aria-hidden="true" /> Agregar
                    </Button>
                </div>
            </div>
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="contracts" v-model:filters="filters"
                dataKey="id" filterDisplay="menu" :loading="loading"
                :globalFilterFields="['name', 'gerencia', 'cost', 'start_date', 'end_date']"
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

                <!--COLUMNAS-->
                <Column field="name" header="Nombre"></Column>
                <Column field="gerencia" header="Gerencia"></Column>
                <Column field="cost" header="Costo">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.cost) }}
                    </template>
                </Column>
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
                            class="whitespace-normal pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8 flex space-x-2 ">
                            <div>
                                <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                    <PencilIcon class="h-4 w-4 " aria-hidden="true" />
                                </Button>
                            </div>
                            <div>
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Contrato', 'contracts')"
                                    class="hover:bg-danger">
                                    <TrashIcon class="h-4 w-4 " aria-hidden="true" />
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
                                        <DialogTitle as="h3" class="text-xl font-semibold text-primary ">{{ formData.id !=
                                            0 ? 'Editar ' : 'Crear' }} Contrato
                                        </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                        <div class="mt-2 space-y-4 border border-gray-200 p-2 rounded-lg">
                                            <TextInput class="mt-2 text-left" label="Nombre del Contrato"
                                                placeholder="Escriba Nombre del Contrato" v-model="formData.name"
                                                :error="router.page.props.errors.name"></TextInput>
                                            <Dropdown v-model="formData.gerencia" optionLabel="name" optionValue="id"
                                                :options="gerencias" placeholder="Seleccione Gerencia"
                                                :class="error != null ? 'p-invalid' : ''" />
                                            <small id="gerencia-help" class="p-error">
                                                {{ formData.errors.gerencia }}
                                            </small>

                                            <!-- <div class="mt-2">
                                                <label for="type"
                                                    class="block capitalize text-left text-sm text-gray-900">Tipo de
                                                    Cliente</label>
                                                <Dropdown v-model="form.type" :options="types" filter optionLabel="name"
                                                    placeholder="Selecccionar" class="w-full md:w-14rem">
                                                </Dropdown>
                                            </div> -->
                                            <TextInput class="mt-2 text-left" label="Costo"
                                                :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.cost"
                                                :error="router.page.props.errors.cost"></TextInput>

                                            <!--CAMPO FECHA INICIO-->
                                            <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                                <label for="icon" class="mt-2 text-left text-sm font-semibold">
                                                    Fecha Inicio
                                                </label>
                                                <Calendar id="calendar-24h" v-model="formData.start_date" touchUI showTime
                                                    showIcon manualInput="true" hideOnDateTimeSelect="true" hourFormat="24"
                                                    :showOnFocus="false" :showButtonBar="false"
                                                    placeholder="Fecha Inicio del Contrato"
                                                    :class="error != null ? 'p-invalid' : ''"
                                                    @input="$emit('update:start_date', $event.target.value)" />
                                                <small id="start_date-help" class="p-error">
                                                    {{ formData.errors.start_date }}
                                                </small>
                                            </div>

                                            <!--CAMPO FECHA FINALIZACIÓN-->
                                            <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                                <label for="icon" class="mt-2 text-left text-sm font-semibold">
                                                    Fecha Finalización
                                                </label>
                                                <Calendar id="calendar-24h" v-model="formData.end_date" touchUI showTime
                                                    showIcon manualInput="true" hideOnDateTimeSelect="true" hourFormat="24"
                                                    :showOnFocus="false" :showButtonBar="false"
                                                    placeholder="Fecha Finalización del Contrato"
                                                    :class="error != null ? 'p-invalid' : ''"
                                                    @input="$emit('update:end_date', $event.target.value)" />
                                                <small id="fechaInicio-help" class="p-error">
                                                    {{ formData.errors.end_date }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 flex space-x-4 px-2">
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
