<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useSweetalert } from '@/composable/sweetAlert';
import TextInput from '../../Components/TextInput.vue';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import CustomModal from '@/Components/CustomModal.vue';
import Dropdown from 'primevue/dropdown';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';

const modalType = ref('Nueva unidad')
const customerSelect = ref();
const typeSelect = ref();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const visible = ref(false)

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
                visible.value = false;
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
            visible.value = false;
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
    visible.value = true;
}

const editItem = (event, ship) => {
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
    visible.value = true;
};

const cloneItem = (event, ship) => {
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
    visible.value = true;
};

const deleteItem = (event, ship) => {
    confirmDelete(ship.id, 'Buque', 'ships')
}
const clearErrors = () => {
    router.page.props.errors = {};
};

const columnas = ref([
    {
        field: 'name', header: 'Nombre', class: 'w-[20%]', type: 'object', objectRows: {
            photo: { field: 'file' },
            primary: { field: 'name' },
            secundary: { field: 'type_ship', subfield: 'name' }
        }
    },
    { field: 'idHull', header: 'N° CASCO' },
    { field: 'quilla', header: 'QUILLAS' },
    { field: 'pantoque', header: 'PANTOQUE' },
    { field: 'acronyms', header: 'SIGLAS' },
    {
        field: 'customer.name', header: 'CLIENTE', type: 'object', objectRows: {
            primary: { field: 'customer', subfield: 'name' },
            secundary: { field: 'customer', subfield: 'type' }
        }
    },
])
const buttons = ref([
    { event: 'editItem', severity: 'primary', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'cloneItem', severity: 'warning', icon: 'fa-solid fa-copy', class: '!h-8', text: true, outlined: false, rounded: false },
    { event: 'confirmDelete', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
])

</script>

<template>
    <AppLayout>
        <div class="w-full h-[89vh] overflow-y-auto">
            <CustomDataTable :data="ships" :columnas="columnas" cacheName="ships" :actions="buttons"
                :title="customer ? 'Unidades del cliente:' + customer.name : 'Todas las unidades'"
                @confirmDelete="deleteItem" @editItem="editItem" @cloneItem="cloneItem">
                <template #buttonHeader>
                    <Button title="Agregar Estimación" @click="addItem()" severity="success" label="Agregar" outlined
                        icon="fa-solid fa-plus" class="!h-8" />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>

    <CustomModal v-model:visible="visible">
        <template #icon>
            <i class="text-white fa-solid fa-ship"></i>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                {{ modalType }}</span>
        </template>
        <template #body>
            <div class="grid grid-cols-2 gap-2">
                <CustomInput label="Numero del casco" type="text" :placeholder="'Numero del casco'"
                    v-model:input="formData.idHull" :error="router.page.props.errors.idHull" />
                <CustomInput label="Nombre del Buque" type="text" :placeholder="'Nombre del Buque'"
                    v-model:input="formData.name" :error="router.page.props.errors.name" />
                <CustomInput id="customer" label="Cliente" type="dropdown" filter v-model:input="customerSelect"
                    :options="customers" v-if="customers" @change="formData.customer_id = $event.value.id"
                    optionLabel="name" placeholder="Seleccione Cliente" />
                <CustomInput id="class" label="Tipo" type="dropdown" filter v-model:input="typeSelect" clearIcon
                    :options="typeShips" v-if="customers" optionLabel="name" placeholder="Seleccione tipo" />
                <CustomInput label="Siglas" type="text" :placeholder="'Digite las siglas'" v-model:input="formData.acronyms"
                    :error="router.page.props.errors.acronyms" />
                <CustomInput label="Carros Quillas" type="number" :placeholder="'Números de carros de Quillas necesarios'"
                    v-model:input="formData.quilla" :error="router.page.props.errors.quilla" />
                <CustomInput label="Carros de Pantoques" type="number"
                    :placeholder="'Números carros de Pantoques necesarios'" v-model:input="formData.pantoque"
                    :error="router.page.props.errors.pantoque" />
                <CustomInput type="file" label="Adjuntar foto" acceptFile="image/*" v-model:input="formData.image" />
            </div>
        </template>
        <template #footer>
            <Button severity="success" :loading="loading" @click="submit()" icon="fa-solid fa-floppy-disk"
                :label="formData.id != null ? 'Actualizar ' : 'Guardar'" />
            <Button severity="danger" @click="visible = false" label="Cancelar" icon="fa-regular fa-circle-xmark" />
        </template>
    </CustomModal>
</template>
