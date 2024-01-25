<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import '../../../sass/dataTableCustomized.scss';
import Dropdown from 'primevue/dropdown';
import { useSweetalert } from '@/composable/sweetAlert';
import { useConfirm } from "primevue/useconfirm";
import TextInput from '../../Components/TextInput.vue';
import Button from 'primevue/button';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomModal from '@/Components/CustomModal.vue';
import CustomSelectCountries from '@/Components/CustomSelectCountries.vue';
import CustomInput from '@/Components/CustomInput.vue';

const confirm = useConfirm();
const { toast } = useSweetalert();
const loading = ref(false);
const { confirmDelete } = useSweetalert();
const open = ref(false)
const country = ref()
const props = defineProps({
    customers: Array,
})

//#region UseForm
const formData = useForm({
    customer: {},
});
//#endregion

/* SUBMIT*/
const submit = () => {
    loading.value = true;
    formData.customer.country = country.value.translations.spa.common
    formData.customer.country_en = country.value.name.common
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

const editItem = (event, customer) => {
    clearErrors();
    formData.customer = customer;
    open.value = true;
};

const clearErrors = () => {
    router.page.props.errors = {};
};

const showShips = (event, selectCustomer) => {
    router.get(route('ships.index'), { id: selectCustomer.id })
}
const deleteItem = (event, data) => {
    confirmDelete(data.id, 'Cliente?', 'customers')
}

const columnas = [
    // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
    // { field: 'NIT', header: 'NIT', filter: true, sortable: true },
    { field: 'name', header: 'Nombre', filter: true, sortable: true },
    { field: 'country', header: 'Pais', filter: true, sortable: true },
    { field: 'type', header: 'Tipo', filter: true, sortable: true },
    // { field: 'email', header: 'Correo', filter: true, sortable: true }
]
const buttons = [
    { event: 'showShips', severity: 'success', icon: 'fa-solid fa-ship', text: true, outlined: false, rounded: false },
    { event: 'editItem', severity: 'warning', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'deleteItem', severity: 'danger', icon: 'fa-solid fa-trash', text: true, outlined: false, rounded: false },
]
</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <CustomDataTable :data="customers" :rows-default="100" title="Clientes" cacheName="customers"
                :columnas="columnas" :actions="buttons" @showShips="showShips" @deleteItem="deleteItem"
                @editItem="editItem">
                <template #buttonHeader>
                    <Button title="Agregar Estimación" @click="addItem()" severity="success" label="Agregar" outlined
                        icon="fa-solid fa-plus" class="!h-8" />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>

    <!--MODAL DE FORMULARIO-->

    <CustomModal v-model:visible="open">
        <template #icon>
            <i class="fa-solid fa-plus text-xl"></i>
        </template>
        <template #titulo>
            <p> {{ formData.customer.id != null ? 'Editar ' : 'Crear' }} Cliente </p>
        </template>
        <template #body>
            <CustomInput label="Nombre del Cliente" placeholder="Escriba el nombre del Cliente" id="name"
                v-model:input="formData.customer.name" :error="router.page.props.errors.name" />
            <CustomInput label="Pais" placeholder="Seleccione un pais" id="country" type="country" v-model:input="country"
                :error="router.page.props.errors.country" />
            <CustomInput label="Tipo de Cliente" placeholder="Selecciona un Tipo de Cliente"
                :options="['GOBIERNO', 'ARMADOR CIVIL', 'FUERZAS ARMADAS']" id="type" type="dropdown"
                v-model:input="formData.customer.name" :error="router.page.props.errors.name" />
        </template>
        <template #footer>
            <Button severity="danger" @click="open = false" label="Cancelar" class="!h-8" />
            <Button severity="success" :loading="false" @click="submit()"
                :label="formData.customer.id != null ? 'Actualizar ' : 'Guardar'" class="!h-8" />
        </template>
    </CustomModal>
</template>
