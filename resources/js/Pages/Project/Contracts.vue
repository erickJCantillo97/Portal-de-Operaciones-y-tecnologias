<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import { webNotifications } from '@/composable/webNotifications';
import TextInput from '../../Components/TextInput.vue';
import Button from '../../Components/Button.vue';
import Combobox from '@/Components/Combobox.vue';
import FileUpload from 'primevue/fileupload';
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const { contractNotification } = webNotifications();

//Props
const props = defineProps({
    contracts: Array,
    customers: Array
})

//#region v-models (ref())
const customerSelect = ref({});
const managerSelect = ref([]);

//Tipo de Venta
const typeOfSaleSelect = ref([])
const typeOfSaleOptions = ref([
    { name: 'VENTA DIRECTA' },
    { name: 'FINANCIADA' },
    { name: 'LEASING' }
]);

//Moneda
const currencySelect = ref([]);
const currencyOptions = ref([
    { name: 'COP' },
    { name: 'USD' },
    { name: 'EUR' }
]);

//Estado de la venta
const stateSelect = ref([]);
const stateOptions = ref([
    { name: 'LIQUIDADO' },
    { name: 'EN EJECUCIÓN' }
]);

//Abrir Modal
const open = ref(false)
//#endregion

//#region UseForm
const formData = useForm({
    id: props.contracts?.id ?? '0',
    contract_id: props.contracts?.contract_id ?? '',
    subject: props.contracts?.subject ?? '',
    customer_id: props.contracts?.customer_id ?? '0',
    manager_id: props.contracts?.manager_id ?? '0',
    type_of_sale: props.contracts?.type_of_sale ?? '',
    supervisor: props.contracts?.supervisor ?? '',
    start_date: props.contracts?.start_date ?? '',
    end_date: props.contracts?.end_date ?? '',
    currency: props.contracts?.currency ?? '0',
    cost: props.contracts?.cost ?? '0',
    state: props.contracts?.state ?? '',
    pdf: null
});
//#endregion

onMounted(() => {
    getManagers()
    // window.Echo.private("contracts").listen(".ContractsEvent", (e) => {
    //     // Push.Permission.get();
    //     const customerName = customerSelect.value
    //         ? customerSelect.value.name
    //         : "Cliente no seleccionado";
    //     const managerName = managerSelect.value
    //         ? managerSelect.value.name
    //         : "Buque no seleccionado";
    //     Push.create(e.message, {
    //         body: `Cliente: ${customerName},
    //                 \nBuque: ${managerName},
    //                 \nCosto: ${formatCurrency(formData.cost)}`,
    //         icon: "/images/cotecmar-logo-bg-white.png",
    //         requireInteraction: true,
    //         // timeout: 5000,
    //         onClick: function () {
    //             window.open("https://www.cotecmar.com/", "_blank");
    //             this.close();
    //         },
    //     });
    // });
    // contractNotification(customerSelect, managerSelect, formData.cost);
    initFilters();
})

const getManagers = () => {
    try {
        const manager = {
            key: 'Cargo',
            value: 'Gerente'
        }
        axios.get(route('search.personal', manager))
            .then((res) => {
                // Acciones a realizar en caso de éxito
                managerSelect.value = Object.values(res.data.employees)
                toast('Éxito', 'success')
            })
            .catch((error) => {
                // Acciones a realizar en caso de error
                toast('Error', 'error')
            })
    } catch (error) {
        console.error('Error al obtener empleados:', error)
    }
}

/* SUBMIT*/
const submit = () => {
    loading.value = true;
    if (!customerSelect.value) {
        toast('Por favor seleccione un cliente.', 'error')
        return;
    }

    if (!managerSelect.value) {
        toast('Por favor seleccione un buque.', 'error')
        return;
    }

    formData.customer_id = customerSelect.value.id
    formData.manager_id = managerSelect.value.id

    if (formData.id == 0) {
        router.post(route('contracts.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast(' Contrato creado exitosamente', 'success');
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
    clearErrors();
    customerSelect.value = {}; //Resetear los datos
    managerSelect.value = {}; //Resetear los datos
    open.value = true;
}

const editItem = (contract) => {
    clearErrors();

    formData.id = contract.id;
    formData.contract_id = contract.contract_id;
    formData.subject = contract.subject;
    customerSelect.value = contract.customer; //Este dato viene del Contract::with('customer')
    managerSelect.value = contract.manager; //Este dato viene del Contract::with('manager')
    formData.type_of_sale = contract.type_of_sale;
    formData.supervisor = contract.supervisor;
    formData.start_date = contract.start_date;
    formData.end_date = contract.end_date;
    formData.currency = contract.currency;
    formData.cost = contract.cost;
    formData.state = contract.state;
    formData.pdf = contract.pdf;
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
        currency: 'COP',
    });
};

const excelExport = () => {
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
        <div class="w-full overflow-y-auto custom-scroll">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Contratos
                    </h1>
                </div>

                <div class="" title="Agregar Contrato">
                    <Button @click="addItem()" severity="success">
                        <PlusIcon class="w-6 h-6" aria-hidden="true" />
                        Agregar
                    </Button>
                </div>
            </div>
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="contracts" v-model:filters="filters"
                dataKey="id" filterDisplay="menu" :loading="loading"
                :globalFilterFields="['contract_id', 'customer.name', 'start_date', 'end_date', 'cost']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

                <template #header>
                    <div class="flex justify-between w-full h-8 mb-2">
                        <div class="flex space-x-4">
                            <div class="w-8" title="Filtrar Contratos">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                                </Button>
                            </div>

                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search" title="Buscar Contrato"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="contract_id" header="Contrato ID"></Column>
                <!-- <Column field="subject" header="Objeto del Contrato"></Column> -->
                <Column field="customer.name" header="Cliente"></Column>
                <!-- <Column field="manager.name" header="Gerente"></Column> -->
                <!-- <Column field="type_of_sale" header="Tipo de Venta"></Column> -->
                <!-- <Column field="supervisor" header="Supervisor"></Column> -->
                <Column field="start_date" header="Fecha Inicio"></Column>
                <Column field="end_date" header="Fecha Finalización"></Column>
                <!-- <Column field="currency" header="Moneda">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.cost) }}
                    </template>
                </Column> -->
                <Column field="cost" header="Costo">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.cost) }}
                    </template>
                </Column>
                <!-- <Column field="state" header="Estado del Contrato"></Column> -->
                <!-- <Column field="status" header="Estado" sortable>
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.status" :severity="getContractStatusSeverity(slotProps.data)" />
                    </template>
                </Column> -->

                <!--ACCIONES-->
                <Column header="Acciones" class="space-x-3">
                    <template #body="slotProps">
                        <!--BOTÓN EDITAR-->
                        <div
                            class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8 ">
                            <div title="Editar Contrato">
                                <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                    <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                            <!--BOTÓN ELIMINAR-->
                            <div title="Eliminar Contrato">
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Contrato', 'contracts')"
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
                            <DialogPanel
                                class="w-full px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-[800px]">
                                <div>
                                    <div class="px-2 mt-2 text-center">
                                        <DialogTitle as="h3" class="text-xl font-semibold text-center text-primary">
                                            {{ formData.id != 0 ? 'Editar ' : 'Crear' }}
                                            Contrato
                                        </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                        <div class="grid grid-cols-1 gap-4 p-2 md:grid-cols-2 space-y-2 rounded-lg">
                                            <div class="col-span-1 p-2 mt-2">
                                                <!--CAMPO CONTRATO ID-->
                                                <TextInput class="mt-2 text-left" label="Contrato ID"
                                                    placeholder="Escriba ID del Contrato" v-model="formData.contract_id"
                                                    :error="$page.props.errors.contract_id">
                                                </TextInput>

                                                <!--CAMPO OBJETO DEL CONTRATO ()-->
                                                <TextInput class="mt-2 text-left" label="Objeto del Contrato"
                                                    placeholder="Escriba Objeto del Contrato" v-model="formData.subject"
                                                    :error="$page.props.errors.subject">
                                                </TextInput>

                                                <!--CAMPO CLIENTE (customer)-->
                                                <Combobox class="mt-2 text-left" label="Cliente"
                                                    placeholder="Seleccione Cliente" :options="customers"
                                                    v-model="customerSelect">
                                                </Combobox>

                                                <!--CAMPO GERENTE (manager)-->
                                                <Combobox class="mt-2 text-left" label="Gerente" :options="managerSelect"
                                                    placeholder="Seleccione Gerente" v-model="managerSelect">
                                                </Combobox>

                                                <!--CAMPO TIPO DE VENTA (type_of_sale)-->
                                                <Combobox class="mt-2 text-left" label="Tipo de Venta"
                                                    :options="typeOfSaleOptions" placeholder="Seleccione un Tipo de Venta"
                                                    v-model="typeOfSaleSelect">
                                                </Combobox>

                                                <!--CAMPO SUPERVISOR-->
                                                <TextInput class="mt-2 text-left" label="Supervisor"
                                                    placeholder="Escriba nombre del supervisor"
                                                    v-model="formData.supervisor" :error="$page.props.errors.supervisor">
                                                </TextInput>
                                            </div>

                                            <div class="col-span-1 p-2 mt-2">
                                                <!--CAMPO FECHA INICIO-->
                                                <TextInput class="mt-2 text-left" type="date" label="Fecha de inicio"
                                                    :placeholder="'Escriba el Tipo de Cliente'"
                                                    v-model="formData.start_date" :error="$page.props.errors.start_date">
                                                </TextInput>

                                                <!--CAMPO FECHA FINALIZACIÓN-->
                                                <TextInput class="mt-2 text-left" type="date" label="Fecha de Finalización"
                                                    :placeholder="'Escriba el Tipo de Cliente'" v-model="formData.end_date"
                                                    :error="$page.props.errors.end_date">
                                                </TextInput>

                                                <!--CAMPO MONEDA-->
                                                <Combobox class="mt-2 text-left" label="Moneda" placeholder="Ej: COP"
                                                    v-model="currencySelect" :options="currencyOptions">
                                                </Combobox>

                                                <!--CAMPO PRECIO DE VENTA (cost)-->
                                                <TextInput class="text-left" label="Precio de Venta" type="number"
                                                    :placeholder="'Escriba el valor total estimado'" v-model="formData.cost"
                                                    :error="router.page.props.errors.cost">
                                                </TextInput>

                                                <!--CAMPO ESTADO DE VENTA (state)-->
                                                <Combobox class="mt-2 text-left" label="Estado del Contrato"
                                                    :options="stateOptions" placeholder="Seleccione un Tipo de Venta"
                                                    v-model="stateSelect">
                                                </Combobox>

                                                <!--CAMPO SUBIR ARCHIVO-->
                                                <FileUpload class="mt-10" chooseLabel="Adjuntar PDF" mode="basic" name="demo[]"
                                                    :multiple="false" accept=".pdf" :maxFileSize="1000000"
                                                    @input="formData.pdf = $event.target.files[0]">
                                                </FileUpload>
                                            </div>
                                        </div>
                                        <div class="">
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
