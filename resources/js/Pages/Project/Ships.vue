<script setup>
const { hasRole, hasPermission } = usePermissions()
const { toast, confirmDelete } = useSweetalert();
const customerSelect = ref();
const loading = ref(false);
const modalType = ref('Nueva unidad')
const typeSelect = ref();
const visible = ref(false)
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';
import CustomModal from '@/Components/CustomModal.vue';

const props = defineProps({
    ships: Array,
    typeShips: Object,
    customers: Array,
    customer: Object
})

//#region UseForm
const formData = ref({
    ship: {}
});
//#endregion

/* SUBMIT*/
const submit = () => {
    loading.value = true;
    formData.value.ship.customer_id = props.customer?.id ?? formData.value.ship.customer?.id ?? null;
    formData.value.ship.type_ship_id = formData.value.ship.type_ship?.id ?? null
    if (formData.value.ship.id == null) {
        router.post(route('ships.store'), formData.value.ship, {
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
    } else {
        router.post(route('ships.update', formData.value.ship.id), formData.value.ship, {
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

}

const addItem = () => {
    formData.value.ship = {}
    modalType.value = "Nueva unidad"
    visible.value = true;
}

const editItem = (event, ship) => {
    modalType.value = "Editar unidad"
    typeSelect.value = ship
    formData.value.ship = ship
    visible.value = true;
};

const cloneItem = (event, ship) => {
    modalType.value = "Clonar unidad"
    typeSelect.value = ship.type_ship
    formData.value.ship = ship
    formData.value.ship.id = null;
    formData.value.ship.idHull = null;
    visible.value = true;
};

const deleteItem = (event, ship) => {
    confirmDelete(ship.id, 'Buque', 'ships')
}

const columnas = ref([
    {
        field: 'name', header: 'Nombre', class: 'w-[20%]', type: 'object', objectRows: {
            photo: { field: 'file' },
            primary: { field: 'name' },
            secundary: { field: 'type_ship', subfield: 'name' }
        },
        filter: true, sortable: true
    },
    { field: 'type_ship.name', header: 'clase', filter: true, sortable: true },
    { field: 'idHull', header: 'N° CASCO', filter: true, sortable: true },
    { field: 'acronyms', header: 'SIGLAS' },
])
const buttons = ref([
    { event: 'editItem', severity: 'primary', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false, show: hasPermission('ship edit') },
    { event: 'cloneItem', severity: 'warning', icon: 'fa-solid fa-copy', class: '!h-8', text: true, outlined: false, rounded: false, show: hasPermission('ship create') },
    { event: 'confirmDelete', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false, show: hasPermission('ship delete') },
])

</script>

<template>
    <AppLayout>
        <div class="w-full h-[89vh] overflow-y-auto">
            <CustomDataTable :data="ships" exportRute="export.ships" :columnas="columnas" :actions="buttons"
                :rowsDefault=20 :title="customer ? 'Unidades del cliente:' + customer.name : 'Todas las unidades'"
                @confirmDelete="deleteItem" @editItem="editItem" @cloneItem="cloneItem">
                <template #buttonHeader>
                    <Button title="Agregar Estimación" @click="addItem()" severity="success" label="Agregar" outlined
                        icon="fa-solid fa-plus" class="!h-8" v-if="hasPermission('ship create')" />
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
            <span class="grid grid-cols-2 gap-2">
                <CustomInput label="Numero del casco" type="text" :placeholder="'Numero del casco'"
                    v-model:input="formData.ship.idHull" :error="router.page.props.errors.idHull" />
                <CustomInput label="Nombre del Buque" type="text" :placeholder="'Nombre del Buque'"
                    v-model:input="formData.ship.name" :error="router.page.props.errors.name" />
                <CustomInput id="class" label="Clase de buque" type="dropdown" filter
                    v-model:input="formData.ship.type_ship" clearIcon :options="typeShips" optionLabel="name"
                    placeholder="Seleccione tipo" />
                <CustomInput label="Siglas" type="text" :placeholder="'Digite las siglas'"
                    v-model:input="formData.ship.acronyms" :error="router.page.props.errors.acronyms" />

                <CustomInput v-if="$page.props.auth.user.gerencia != 'GECON'" label="Carros Quillas" type="number"
                    :placeholder="'Números de carros de Quillas necesarios'" v-model:input="formData.ship.quilla"
                    :error="router.page.props.errors.quilla" />
                <CustomInput v-if="$page.props.auth.user.gerencia != 'GECON'" label="Carros de Pantoques" type="number"
                    :placeholder="'Números carros de Pantoques necesarios'" v-model:input="formData.ship.pantoque"
                    :error="router.page.props.errors.pantoque" />
                <CustomInput type="file" label="Adjuntar foto" acceptFile="image/*"
                    v-model:input="formData.ship.image" />
            </span>
        </template>
        <template #footer>
            <Button severity="success" :loading="loading" @click="submit()" icon="fa-solid fa-floppy-disk"
                :label="formData.ship.id != null ? 'Actualizar ' : 'Guardar'" />
            <Button severity="danger" :loading="loading" @click="visible = false" label="Cancelar"
                icon="fa-regular fa-circle-xmark" />
        </template>
    </CustomModal>
</template>
