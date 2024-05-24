<script setup>
import { ref } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomInput from '@/Components/CustomInput.vue'
import CustomModal from '@/Components/CustomModal.vue'

// const c = console.log.bind(document)

const confirm = useConfirm()
const toast = useToast()

const openProcessModal = ref(false)
const loading = ref(false)
const customInputLoading = ref(false)
const subsystem = ref([])

const formData = useForm({
    subsystem_id: '',
    maintenance_type: '',
    name: ''
})

const maintenance_type_options = [
    {
        id: 0,
        name: 'PREDICTIVO O DIAGNÓSTICO'
    },
    {
        id: 1,
        name: 'PREVENTIVO'
    },
    {
        id: 2,
        name: 'CORRECTIVO'
    },
    {
        id: 3,
        name: 'CAMBIO, REEMPLAZO O MONTAJE'
    }
]

//#region CustomDatatable
const columns = [
    { field: 'subsystem_id', header: 'Subsistema', filter: true },
    { field: 'maintenance_type', header: 'Tipo de Mantenimiento', filter: true },
    { field: 'name', header: 'Nombre', filter: true }
]

const buttons = [
    { event: 'edit', severity: 'warning', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

const getSubSystems = async () => {
    loading.value = true
    customInputLoading.value = true
    await axios.get(route('process.index'))
        .then((res) => {
            subsystem.value = res.data.map((value) => {
                return {
                    ...value,
                    name: value.code + '. ' + value.name
                }
            })
            loading.value = false
            customInputLoading.value = false
        })
}

//#region CRUD
const submit = () => {
    try {
        formData.post(route('process.store'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    group: "customToast",
                    text: `Se ha guardado el proceso ${formData.name} correctamente`,
                    life: 2000
                })
                openProcessModal.value = false
                saveLoading.value = true
                formData.reset()
            },
            onError: (error) => {
                toast.add({
                    severity: 'error',
                    group: "customToast",
                    text: `Ha ocurrido un error al intentar guardar el proceso ${formData.name}, error: ` + error,
                    life: 2000
                })
            }
        })
    } catch (error) {
        console.log('Error: ' + error)
    }
}

const editProcess = (event, data) => {
    console.log(data)
    console.log(formData.subsystem_id)
    formData.subsystem_id = parseInt(data.subsystem_id) || ''
    console.log(formData.subsystem_id)
    formData.code = data.code || ''
    formData.name = data.name || ''
    openSubSystemModal.value = true
}

const deleteProcess = (event, process_id) => {
    confirm.require({
        target: event.currentTarget,
        message: {
            message: '¿Está seguro de eliminar este proceso?',
            subMessage: 'No se puede deshacer esta acción'
        },
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-success',
        rejectLabel: 'No',
        acceptLabel: 'Sí',
        accept: () => {
            formData.delete(route('process.destroy', process_id.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        group: "customToast",
                        text: `Se ha eliminado el proceso ${formData.name} correctamente`,
                        life: 2000
                    })
                },
                onError: (error) => {
                    toast.add({
                        severity: 'error',
                        group: "customToast",
                        text: `Ha ocurrido un error al intentar eliminar el proceso ${formData.name}, error: ` + error,
                        life: 2000
                    })
                }
            })
        }, reject: () => {
            console.error('Error')
        }
    })
}
//endregion

const addProcess = () => {
    getSubSystems()
    openProcessModal.value = true
}
</script>
<template>
    <div class="size-full overflow-y-auto">
        <CustomDataTable route-data="process.index" :rows-default="100" :columnas="columns" :actions="buttons"
            @edit="editProcess" @delete="deleteProcess" :loading>
            <template #buttonHeader>
                <Button @click="addProcess" severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
            </template>
        </CustomDataTable>
    </div>

    <CustomModal v-model:visible="openProcessModal" width="30vw">
        <template #icon>
            <i class="fa-solid fa-cloud-arrow-up text-white text-xl"></i>
        </template>
        <template #titulo>
            <p class="text-white">Añadir Proceso</p>
        </template>
        <template #body>
            <div class="flex flex-col gap-4">
                <!--Subsistema-->
                <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="subsystem" label="Subsistema"
                    placeholder="Selecione un Subsistema" v-model:input="formData.subsystem_id"
                    :loading="customInputLoading" />
                <!--Código-->
                <CustomInput type="dropdown" optionLabel="name" optionValue="id" label="Tipo de Mantenimiento"
                    :options="maintenance_type_options" placeholder="Seleccione Tipo de Mantenimiento"
                    v-model:input="formData.maintenance_type" />
                <!--Nombre-->
                <CustomInput label="Nombre" placeholder="Escriba Nombre del SubSistema" v-model:input="formData.name" />
            </div>
        </template>
        <template #footer>
            <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark" @click="openProcessModal = false" />
            <Button label="Guardar" severity="success" icon="pi pi-save" :loading="false" @click="submit()" />
        </template>
    </CustomModal>
</template>
