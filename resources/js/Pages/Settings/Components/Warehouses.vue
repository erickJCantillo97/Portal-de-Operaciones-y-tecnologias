<script setup>
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomModal from '@/Components/CustomModal.vue';
import CustomInput from '@/Components/CustomInput.vue';
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm"
const confirm = useConfirm();
import { ref } from 'vue'

const toast = useToast()

// const gerencias = ref([])
const warehouses = ref([])
const loading = ref(true)
// axios.get(route('gerencias.index')).then(
//     (res) => {
//         gerencias.value = res.data[0]
//     }
// );
const getWarehouses = () => {
    axios.get(route('warehouse.index')).then(
        (res) => {
            // console.log(res)
            warehouses.value = res.data
            loading.value = false
        }
    )
}
getWarehouses()
const columnsAlmacen = [
    { field: 'name', header: 'Nombre', filter: true, sortable: true, },
    { field: 'gerencia', header: 'Gerencia', filter: true, sortable: true, },
    { field: 'department', header: 'Oficina', filter: true, sortable: true, },
    { field: 'status', header: 'Estado', filter: true, sortable: true, },
]
const buttonsAlmacen = [
    { event: 'edit', severity: 'warning', class: '', icon: 'fa-solid fa-pen', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', class: '', icon: 'fa-solid fa-trash-can', text: true, outlined: false, rounded: false },
]
const modalAlmacen = ref(false)
const formAlmacen = ref({
    id: null,
    name: null,
    status: null,
    processing: false,
    error: {}

})
const openModal = (event, data) => {
    if (data) {
        formAlmacen.value.error = {}
        Object.assign(formAlmacen.value, data)
    } else {
        formAlmacen.value = {
            id: null,
            name: null,
            status: null,
            processing: false,
            error: {}
        }
    }
    modalAlmacen.value = true
}

const saveAlmacen = async () => {
    if (formAlmacen.value.name) {
        formAlmacen.value.processing = true
        if (formAlmacen.value.id) {
            await axios.put(route('warehouse.update', formAlmacen.value.id), formAlmacen.value).then(
                res => {
                    toast.add({ severity: 'success', group: 'customToast', text: 'Actualizado', life: 2000 });
                    formAlmacen.value.processing = false
                    getWarehouses()
                })
        } else {
            await axios.post(route('warehouse.store'), formAlmacen.value).then(
                res => {
                    toast.add({ severity: 'success', group: 'customToast', text: 'Creado', life: 2000 });
                    formAlmacen.value.processing = false
                    getWarehouses()
                })
        }
        modalAlmacen.value = false
    } else {
        formAlmacen.value.error.name = 'El campo es requerido'
    }
}
const deleteAlmacen = async (event, data) => {
    confirm.require({
        target: event.currentTarget,
        message: '¿Esta seguro de eliminar este almacen?',
        icon: 'fa-solid fa-trash-can text-danger',
        rejectClass: 'p-button-danger p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-success',
        rejectLabel: 'No',
        acceptLabel: 'Si',
        accept: async () => {
            await axios.delete(route('warehouse.destroy', data.id)).then(
                res => {
                    toast.add({ severity: 'success', group: 'customToast', text: 'Eliminado', life: 2000 });
                    getWarehouses()
                }
            )
        },
    })
}

</script>

<template>
    <CustomDataTable :loading :rowsDefault="10" :data="warehouses" :columnas="columnsAlmacen" :actions="buttonsAlmacen"
        :showAdd="true" title="Almacenes" :showColumns="false" :filter="false" @addClick="openModal" @edit="openModal"
        @delete="deleteAlmacen" />

    <CustomModal v-model:visible="modalAlmacen" width="30rem"
        :titulo="formAlmacen.id ? 'Editar almacen' : 'Añadir almacen'">
        <template #body>
                <CustomInput @input="formAlmacen.error = {}" :required="true" v-model:input="formAlmacen.name" label="Nombre"
                :invalid="formAlmacen.error.name ? true : false" :errorMessage="formAlmacen.error.name" />
        
        </template>
        <template #footer>
            <Button severity="danger" label="Cancelar" :disabled="formAlmacen.processing" @click="modalAlmacen = false" />
            <Button type="submit" severity="success" label="Guardar" :loading="formAlmacen.processing"
                @click="saveAlmacen" />
        </template>
    </CustomModal>
</template>