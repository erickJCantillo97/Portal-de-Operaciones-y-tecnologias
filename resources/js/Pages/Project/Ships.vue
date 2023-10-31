<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Combobox from '@/Components/Combobox.vue';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import TextInput from '../../Components/TextInput.vue';
import Button from '../../Components/Button.vue';
import FileUpload from 'primevue/fileupload';

const customerSelect = ref();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const open = ref(false)

const props = defineProps({
    ships: Array,
    customers: Array,
    customer: Object
})

//#region UseForm
const formData = useForm({
    id: '0',
    customer_id: '0',
    name: '',
    type: '',
    quilla: '',
    pantoque: '',
    eslora: '',
    details: '',
    image: null
});
//#endregion

onMounted(() => {
    initFilters();
})

/* SUBMIT*/
const submit = () => {
    loading.value = true;

    if (props.customers == null) {
        formData.customer_id = props.customer.id;
    } else if (customerSelect.value != null) {
        formData.customer_id = customerSelect.value.id;
    }

    if (formData.id == 0) {
        router.post(route('ships.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                open.value = false;
                toast(' Buque creado exitosamente', 'success');
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
    router.post(route('ships.update', formData.id), formData, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false;
            toast('¡Buque actualizado exitosamente!', 'success');
        },
        onError: (errors) => {
            console.log(errors);
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

const editItem = (ship) => {
    clearErrors();

    formData.id = ship.id;
    formData.customer_id = ship.customer_id;
    formData.name = ship.name;
    formData.type = ship.type;
    formData.quilla = ship.quilla;
    formData.pantoque = ship.pantoque;
    formData.eslora = ship.eslora;
    formData.details = ship.details;
    formData.image = ship.image;
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

const formatMeters = (value) => {
    // Eliminar caracteres no numéricos, excepto el punto decimal
    const unformattedLength = value.replace(/[^0-9.]/g, "");

    // Convertir la cadena a un número
    length = parseFloat(unformattedLength);

    // Formatear el número con la unidad "m"
    value = `${length} metros`;

    return value;
};

</script>

<template>
    <AppLayout>
        <div class="h-full custom-scroll overflow-y-auto">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 text-primary">
                        <p v-if="customer" icon="pi pi-eye">Unidades del cliente: {{ customer.name }}</p>
                        <p v-else>Todas las unidades</p>
                    </h1>
                </div>
            </div>
            <DataTable id="tabla" stripedRows class="p-datatable-sm" :value="ships" v-model:filters="filters" dataKey="id"
                filterDisplay="menu" :loading="loading" :globalFilterFields="['name', 'gerencia', 'type', 'details']"
                currentPageReportTemplate=" {first} al {last} de {totalRecords}"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                :paginator="true" :rows="10" :rowsPerPageOptions="[10, 25, 50, 100]">

                <template #header>
                    <div class="flex justify-between w-full h-8 mb-2">
                        <div class="flex space-x-4">
                            <div class="w-8" title="Filtrar Buques">
                                <Button @click="clearFilter()" type="button" severity="primary" class="hover:bg-primary ">
                                    <i class="pi pi-filter-slash" style="color: 'var(--primary-color)'"></i>
                                </Button>
                            </div>
                            <div class="relative flex rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="w-5 h-4 text-gray-400" aria-hidden="true" />
                                </div>
                                <input type="search" title="Buscar Buque"
                                    class="block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    v-model="filters.global.value" placeholder="Buscar..." />
                            </div>
                        </div>
                        <div class="" title="Agregar Unidad">
                            <Button @click="addItem()" severity="success">
                                <PlusIcon class="w-5" aria-hidden="true" />
                                </Button>
                        </div>
                    </div>
                </template>

                <!--COLUMNAS-->
                <Column field="name" header="Nombre" class="w-3/12 p-1">
                    <template #body="slotProps">
                        <div class="flex space-x-2 items-center">
                            <img :src="slotProps.data.file" onerror="this.src='/images/generic-boat.png'" alt="Image"
                                class="h-0 mr-1 rounded-lg sm:h-12 sm:w-16" />
                            <p>{{ slotProps.data.name }} </p>
                        </div>
                    </template>
                </Column>
                <Column field="customer.name" header="Cliente" class="w-1/12 p-1"></Column>
                <Column field="type" header="Tipo" class="w-1/12 p-1"></Column>
                <Column field="quilla" header="Quillas" class="w-1/12 p-1"></Column>
                <Column field="pantoque" header="Pantoque" class="w-1/12 p-1"></Column>
                <Column field="eslora" header="Eslora" class="w-1/12 p-1">
                    <template #body="slotProps">
                        {{ formatMeters(slotProps.data.eslora) }}
                    </template>
                </Column>
                <Column header="Cliente" class="w-1/12 p-1">
                    <template #body="slotProps">
                        <p v-if=slotProps.data.customer>{{ slotProps.data.customer.name }}</p>
                        <p v-else> Sin asignar cliente</p>
                    </template>
                </Column>
                <Column field="details" header="Detalles" class="w-1/6 p-1"></Column>
                <!--ACCIONES-->
                <Column header="Acciones" class="space-x-1 w-1/12 p-1 flex ">
                    <template #body="slotProps">
                        <!--BOTÓN EDITAR-->
                            <div title="Editar Unidad">
                                <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                    <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
                            </div>
                            <!--BOTÓN ELIMINAR-->
                            <div title="Eliminar Unidad">
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Buque', 'ships')"
                                    class="hover:bg-danger">
                                    <TrashIcon class="w-4 h-4 " aria-hidden="true" />
                                </Button>
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
                                        <DialogTitle as="h3" class="text-xl font-semibold text-primary ">
                                            {{ formData.id != 0 ? 'Editar ' : 'Crear' }}
                                            Unidad {{ customer != null ? ' para ' + customer.name : '' }}
                                        </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                        <div class="p-2 mt-2 space-y-2  rounded-lg">
                                            <Combobox v-if="customers" class="mt-2 text-left" label="Cliente"
                                                placeholder="Seleccione Cliente" :options="customers"
                                                v-model="customerSelect">
                                            </Combobox>

                                            <TextInput class="mt-2 text-left" label="Nombre del Buque"
                                                :placeholder="'Nombre del Buque'" v-model="formData.name"
                                                :error="router.page.props.errors.name"></TextInput>

                                            <TextInput class="mt-2 text-left" label="Tipo de Buque"
                                                :placeholder="'Escriba el Tipo de Buque'" v-model="formData.type"
                                                :error="router.page.props.errors.type"></TextInput>

                                            <TextInput class="mt-2 text-left" label="Carros Quillas" type="number"
                                                :placeholder="'Números de carros de Quillas necesarios'"
                                                v-model="formData.quilla" :error="router.page.props.errors.quilla">
                                            </TextInput>

                                            <TextInput class="mt-2 text-left" label="Carros de Pantoques" type="number"
                                                :placeholder="'Números carros de Pantoques necesarios'"
                                                v-model="formData.pantoque" :error="router.page.props.errors.pantoque">
                                            </TextInput>

                                            <TextInput class="mt-2 text-left" label="Longitud de Eslora" type="number"
                                                :placeholder="'Longitud de Eslora'" v-model="formData.eslora"
                                                :error="router.page.props.errors.eslora">
                                            </TextInput>

                                            <TextInput class="mt-2 text-left" label="Detalles"
                                                :placeholder="'Escriba los detalles del Buque'" v-model="formData.details"
                                                :error="router.page.props.errors.details"></TextInput>

                                            <!--maxFileSize = 10MB, la expresión viene dada en Bytes-->
                                            <FileUpload chooseLabel="Adjuntar foto" cancelLabel="Cancelar"
                                                :show-upload-button=false name="demo[]" :multiple="false" :fileLimit=1
                                                :show-cancel-button=false accept="image/*" :maxFileSize="10000000"
                                                invalidFileTypeMessage="Archivo Inválido: solo se permite imagen."
                                                invalidFileSizeMessage="Este archivo supera el tamaño permitido: "
                                                @input="formData.image = $event.target.files[0]">
                                                <template #empty>
                                                    <p>Arrastre aquí una imagen del buque</p>
                                                </template>
                                            </FileUpload>
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
