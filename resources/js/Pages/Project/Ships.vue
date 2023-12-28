<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon, DocumentDuplicateIcon } from '@heroicons/vue/24/outline';
import { useSweetalert } from '@/composable/sweetAlert';
import TextInput from '../../Components/TextInput.vue';
import Button from '../../Components/Button.vue';
import FileUpload from 'primevue/fileupload';
import CustomModal from '@/Components/CustomModal.vue';
import Dropdown from 'primevue/dropdown';

const modalType = ref('Nueva unidad')
const customerSelect = ref();
const typeSelect = ref();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})
const modalVisible = ref(false)

const props = defineProps({
    ships: Array,
    typeShips: Object,
    customers: Array,
    customer: Object
})

//#region UseForm
const formData = useForm({
    id: null,
    name: null,
    idHull: null,
    customer_id: null,
    type_ship_id: null,
    quilla: null,
    pantoque: null,
    acronyms: null,
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
    formData.type_ship_id = typeSelect.value ? typeSelect.value.id : null
    if (formData.id == null) {
        router.post(route('ships.store'), formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                modalVisible.value = false;
                toast(' Buque creado exitosamente', 'success');
            },
            onError: (errors) => {
                console.table(errors);
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
            modalVisible.value = false;
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
    modalType.value = "Nueva unidad"
    modalVisible.value = true;
}

const editItem = (ship) => {
    clearErrors();
    modalType.value = "Editar unidad"
    typeSelect.value = ship.type_ship
    formData.id = ship.id;
    formData.name = ship.name;
    formData.idHull = ship.idHull;
    formData.customer_id = ship.customer_id;
    formData.type_ship_id = ship.type_ship_id;
    formData.quilla = ship.quilla;
    formData.pantoque = ship.pantoque;
    formData.acronyms = ship.acronyms;
    formData.image = ship.image;
    modalVisible.value = true;
};

const cloneItem = (ship) => {
    clearErrors();
    modalType.value = "Clonar unidad"
    typeSelect.value = ship.type_ship
    formData.id = null;
    formData.name = ship.name;
    formData.idHull = null;
    formData.customer_id = ship.customer_id;
    formData.type_ship_id = ship.type_ship_id;
    formData.quilla = ship.quilla;
    formData.pantoque = ship.pantoque;
    formData.acronyms = ship.acronyms;
    formData.image = ship.image;
    modalVisible.value = true;
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
        <div class="h-full overflow-y-auto custom-scroll">
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
                <Column field="name" header="Nombre" class="p-1">
                    <template #body="slotProps">
                        <div class="flex items-center space-x-2">
                            <img :src="slotProps.data.file" onerror="this.src='/images/generic-boat.png'" alt="Image"
                                class="h-0 mr-1 rounded-lg sm:h-12 sm:w-16" />
                            <div>
                                <p class="font-bold">{{ slotProps.data.name }} </p>
                                <p class="text-xs italic">{{ slotProps.data.type_ship.name }} </p>
                            </div>

                        </div>
                    </template>
                </Column>
                <Column field="idHull" header="N° Casco" class=""></Column>
                <Column field="quilla" header="Quillas" class=""></Column>
                <Column field="pantoque" header="Pantoque" class=""></Column>
                <Column field="acronyms" header="Siglas" class=""></Column>

                <Column header="Cliente" class="">
                    <template #body="slotProps">
                        <p v-if=slotProps.data.customer>{{ slotProps.data.customer.name }}</p>
                        <p v-else> Sin asignar cliente</p>
                    </template>
                </Column>
                <!--ACCIONES-->
                <Column header="Acciones" class="flex justify-center space-x-1 ">
                    <template #body="slotProps">
                        <!--BOTÓN EDITAR-->
                        <div title="Editar Unidad" class="py-1">
                            <Button severity="primary" @click="editItem(slotProps.data)" class="hover:bg-primary">
                                <PencilIcon class="w-4 h-4 " aria-hidden="true" />
                            </Button>
                        </div>
                        <div title="Clonar Unidad" class="py-1">
                            <Button severity="primary" @click="cloneItem(slotProps.data)" class="hover:bg-primary">
                                <DocumentDuplicateIcon class="w-4 h-4 " aria-hidden="true" />
                            </Button>
                        </div>
                        <!--BOTÓN ELIMINAR-->
                        <div title="Eliminar Unidad" class="py-1">
                            <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Buque', 'ships')"
                                class="hover:bg-danger">
                                <TrashIcon class="w-4 h-4 " aria-hidden="true" />
                            </Button>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

    </AppLayout>

    <CustomModal v-model:visible="modalVisible">
        <template #icon>
            <i class="text-white fa-solid fa-ship"></i>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                {{ modalType }}</span>
        </template>
        <template #body>
            <div class="grid grid-cols-4 gap-2 px-1 pt-4">
                <TextInput label="Numero del casco" type="text" :placeholder="'Numero del casco'" v-model="formData.idHull"
                    :error="router.page.props.errors.idHull"></TextInput>

                <TextInput label="Nombre del Buque" type="text" :placeholder="'Nombre del Buque'" v-model="formData.name"
                    :error="router.page.props.errors.name"></TextInput>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 capitalize" for="customer">
                        Cliente</label>
                    <Dropdown id="customer" filter v-model="customerSelect" :options="customers" v-if="customers"
                        @change="formData.customer_id = $event.value.id" optionLabel="name" placeholder="Seleccione Cliente"
                        class="w-full rounded-md md:w-14rem" :pt="{
                            root: {
                                class: 'h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
                            },
                            input: {
                                class: '!text-sm pt-3 pl-2'
                            },
                            header: {
                                class: '!p-2'
                            },
                            filterInput: {
                                class: '!p-1'
                            },
                            item: {
                                class: '!text-sm'
                            },
                            emptyMessage: {
                                class: '!text-sm'
                            }
                        }" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 capitalize" for="hull_material">
                        Clase de buque</label>
                    <Dropdown id="hull_material" filter v-model="typeSelect" clearIcon :options="typeShips" v-if="customers"
                        optionLabel="name" placeholder="Seleccione tipo" class="w-full rounded-md md:w-14rem" :pt="{
                            root: {
                                class: 'h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
                            },
                            input: {
                                class: '!text-sm pt-3 pl-2'
                            },
                            header: {
                                class: '!p-2'
                            },
                            filterInput: {
                                class: '!p-1'
                            },
                            item: {
                                class: '!text-sm'
                            },
                            emptyMessage: {
                                class: '!text-sm'
                            }
                        }" />
                </div>

                <TextInput label="Siglas" type="text" :placeholder="'Digite las siglas'" v-model="formData.acronyms"
                    :error="router.page.props.errors.acronyms">
                </TextInput>

                <TextInput label="Carros Quillas" type="number" :placeholder="'Números de carros de Quillas necesarios'"
                    v-model="formData.quilla" :error="router.page.props.errors.quilla">
                </TextInput>

                <TextInput label="Carros de Pantoques" type="number" :placeholder="'Números carros de Pantoques necesarios'"
                    v-model="formData.pantoque" :error="router.page.props.errors.pantoque">
                </TextInput>
                <div class="flex items-end">
                    <FileUpload mode="basic" chooseLabel="Adjuntar foto" :show-upload-button=false name="demo[]"
                        :multiple="false" :show-cancel-button=false accept="image/*" :maxFileSize="10000000"
                        invalidFileTypeMessage="Archivo Inválido: solo se permite imagen."
                        invalidFileSizeMessage="Este archivo supera el tamaño permitido: "
                        @input="formData.image = $event.target.files[0]">
                    </FileUpload>
                </div>
            </div>
        </template>
        <template #footer>
            <Button severity="success" :loading="loading" class="text-success hover:bg-success border-success"
                @click="submit()">
                {{ formData.id != null ? 'Actualizar ' : 'Guardar' }}
            </Button>
            <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                @click="modalVisible = false">Cancelar</Button>
        </template>
    </CustomModal>
</template>
