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

const openBaseActivitiesModal = ref(false)
const loading = ref(false)
const customInputLoading = ref(false)
const baseActivities = ref([])

const formData = useForm({
    name: ''
})

//#region CustomDatatable
const columns = [
    { field: 'name', header: 'Nombre', filter: true }
]

const buttons = [
    { event: 'edit', severity: 'warning', class: '', icon: 'fa-solid fa-pencil', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', icon: 'fa-regular fa-trash-can', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

//#region CRUD
const submit = () => {
    try {
        formData.post(route('baseActivities.store'), formData, {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    group: "customToast",
                    text: `Se ha guardado la actividad base ${formData.name} correctamente`,
                    life: 2000
                })
                openBaseActivitiesModal.value = false
                saveLoading.value = true
                formData.reset()
            },
            onError: (error) => {
                toast.add({
                    severity: 'error',
                    group: "customToast",
                    text: `Ha ocurrido un error al intentar guardar la actividad base ${formData.name}, error: ` + error,
                    life: 2000
                })
            }
        })
    } catch (error) {
        console.log('Error: ' + error)
    }
}

const editBaseActivities = (event, data) => {
    formData.name = data.name || ''
    openBaseActivitiesModal.value = true
}

const deleteBaseActivities = (event, baseActivities_id) => {
    confirm.require({
        target: event.currentTarget,
        message: {
            message: '¿Está seguro de eliminar esta actividad base?',
            subMessage: 'No se puede deshacer esta acción'
        },
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-success',
        rejectLabel: 'No',
        acceptLabel: 'Sí',
        accept: () => {
            formData.delete(route('baseActivities.destroy', baseActivities_id.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        group: "customToast",
                        text: `Se ha eliminado la actividad base ${formData.name} correctamente`,
                        life: 2000
                    })
                },
                onError: (error) => {
                    toast.add({
                        severity: 'error',
                        group: "customToast",
                        text: `Ha ocurrido un error al intentar eliminar la actividad base ${formData.name}, error: ` + error,
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

const addBaseActivities = () => {
    openBaseActivitiesModal.value = true
}
</script>
<template>
    <div class="size-full overflow-y-auto">
        <CustomDataTable route-data="baseActivities.index" :rows-default="100" :columnas="columns" :actions="buttons"
            @edit="editBaseActivities" @delete="deleteBaseActivities" :loading>
            <template #buttonHeader>
                <Button @click="addBaseActivities" severity="success" icon="fa-solid fa-plus" label="Agregar" outlined />
            </template>
        </CustomDataTable>
    </div>

    <CustomModal v-model:visible="openBaseActivitiesModal" width="30vw">
        <template #icon>
            <i class="fa-solid fa-cloud-arrow-up text-white text-xl"></i>
        </template>
        <template #titulo>
            <p class="text-white">Añadir Actividad Base</p>
        </template>
        <template #body>
            <div class="flex flex-col gap-4">
                <!--Nombre-->
                <CustomInput label="Nombre" placeholder="Escriba Nombre de la Actividad Base" v-model:input="formData.name" />
            </div>
        </template>
        <template #footer>
            <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark" @click="openBaseActivitiesModal = false" />
            <Button label="Guardar" severity="success" icon="pi pi-save" :loading="false" @click="submit()" />
        </template>
    </CustomModal>
</template>
