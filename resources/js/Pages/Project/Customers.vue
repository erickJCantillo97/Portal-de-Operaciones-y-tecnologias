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
import axios from 'axios';
import CustomSelectCountries from '@/Components/CustomSelectCountries.vue';

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
            <CustomDataTable :data="customers" title="Clientes" cacheName="customers" :columnas="columnas"
                :actions="buttons" @showShips="showShips" @deleteItem="deleteItem" @editItem="editItem">
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
            <!-- <TextInput class="mt-2 text-left" label="NIT" placeholder="e.g. 9234232988-0"
                                                v-model="formData.customer.NIT" :error="router.page.props.errors.nit">
                                            </TextInput> -->
            <TextInput class="mt-2 text-left mb-4" label="Nombre del Cliente" placeholder="Escriba el nombre del Cliente"
                v-model="formData.customer.name" :error="router.page.props.errors.name"></TextInput>
            <div class="text-left mb-4">
                <label class="text-sm font-medium" for="pais">Pais</label>
                <CustomSelectCountries v-model:selected="country" />
            </div>
            <div class="text-left">
                <label class="text-sm font-medium" for="hull_material">Tipo de Cliente</label>
                <Dropdown id="hull_material" v-model="formData.customer.type"
                    :options="['GOBIERNO', 'ARMADOR CIVIL', 'FUERZAS ARMADAS']" placeholder="Selecciona un Tipo de Cliente"
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
        </template>
        <template #footer>
            <Button severity="danger" @click="open = false" label="Cancelar" class="!h-8" />
            <Button severity="success" :loading="false" @click="submit()"
                :label="formData.customer.id != null ? 'Actualizar ' : 'Guardar'" class="!h-8" />
        </template>
    </CustomModal>
</template>
