<script setup>
import { ref } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'

const confirm = useConfirm()
const toast = useToast()

const openChangeModal = ref(false)
const saveLoading = ref(false)

const props = defineProps({
    projects: Array,
    personal: Array
})

const loading = ref(false)

//#region CustomDataTable
const turnos = [
    {
        label: 'Turno Dia',
        value: '4'
    },
    {
        label: 'Turno Amanecida',
        value: 1
    },
    {
        label: 'Turno Nocturno',
        value: 1
    },
]

const columnas = [
    // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
    { field: 'user', header: 'Personal', filter: true, sortable: true },
    { field: 'project', header: 'Proyecto', filter: true, sortable: true },
    { field: 'task', header: 'Tarea', filter: true, sortable: true },
    { field: 'turno', header: 'Turno', filter: true, sortable: true },
]

const buttons = [
    { event: 'approve', severity: 'success', class: '', icon: 'fa-solid fa-check', text: true, outlined: false, rounded: false },
    { event: 'change', severity: 'primary', icon: 'fa-solid fa-pencil', class: '!h-8', text: true, outlined: false, rounded: false },
    { event: 'reject', severity: 'danger', icon: 'fa-regular fa-circle-xmark', class: '!h-8', text: true, outlined: false, rounded: false },
]
//#endregion

//#region Actions

//#region CRUD
const approvePayroll = (event, payroll_id) => {
    confirm.require({
        target: event.currentTarget,
        message: {
            message: '¿Está seguro de aprobar esta planilla?',
            subMessage: `⚠️ No se puede deshacer esta acción`
        },
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-success',
        rejectLabel: 'No',
        acceptLabel: 'Sí',
        accept: () => {
            // axios.post(route('payroll.approve', payroll_id.id), {
            //     preserveScroll: true,
            //     onSuccess: () => {
            //         toast.add({
            //             severity: 'success',
            //             group: "customToast",
            //             text: `Se ha aprobado la planilla del señor(a) ' ' correctamente`,
            //             life: 2000
            //         })
            //     },
            //     onError: (error) => {
            //         toast.add({
            //             severity: 'error',
            //             group: "customToast",
            //             text: 'Ha ocurrido un error inesperado: ' + error,
            //             life: 2000
            //         })
            //     }
            // })
            console.log('Hi')
        }, reject: () => {
            console.error('Error')
        }
    })
}

const rejectPayroll = (event, payroll_id) => {
    confirm.require({
        target: event.currentTarget,
        message: {
            message: '¿Está seguro de rechazar esta planilla?',
            subMessage: `⚠️ No se puede deshacer esta acción`
        },
        icon: 'pi pi-exclamation-triangle text-danger',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm p-button-success',
        rejectLabel: 'No',
        acceptLabel: 'Sí',
        accept: () => {
            // axios.delete(route('payroll.destroy', payroll_id.id), {
            //     preserveScroll: true,
            //     onSuccess: () => {
            //         toast.add({
            //             severity: 'success',
            //             group: "customToast",
            //             text: `Se ha rechazado la planilla del señor(a) ' ' correctamente`,
            //             life: 2000
            //         })
            //     },
            //     onError: (error) => {
            //         toast.add({
            //             severity: 'error',
            //             group: "customToast",
            //             text: 'Ha ocurrido un error inesperado: ' + error,
            //             life: 2000
            //         })
            //     }
            // })
            console.log('Hi')
        }, reject: () => {
            console.error('Error')
        }
    })
}

const personas = [
    {
        nombre: "Juan",
        apellido: "Pérez",
        edad: 30,
        direccion: {
            calle: "Calle Falsa",
            numero: 123,
            ciudad: "Ciudad Ejemplo"
        },
        hobbies: ["leer", "correr", "programar"]
    },
    {
        nombre: "Armando",
        apellido: "Paredes",
        edad: 40,
        direccion: {
            calle: "Avenida Siempre Viva",
            numero: 125,
            ciudad: "Ciudad Caoba"
        },
        hobbies: ["cantar", "programar", "insultar"]
    }
];

const changePayroll = () => {
    openChangeModal.value = true
}

const submitChange = () => {
    console.log('Cambiado')
}
//#endregion

const fakeData = [
    {
        id: 0,
        user: '👺 Ronny Gutierrez Vitola - Técnico en Diseño y Desarrollo de Aplicaciones',
        project: 'TOP',
        task: 'TOP',
        turno: '07:00 - 16:30'
    }
]
</script>
<template>
    <div class="size-full">
        <CustomDataTable :data="fakeData" :loading :rows-default="100" :columnas="columnas" :actions="buttons"
            @approve="approvePayroll" @reject="rejectPayroll" @change="changePayroll" />
    </div>

    <!--Modal de Cambiar Planilla-->
    <CustomModal v-model:visible="openChangeModal" width="30vw">
        <template #icon>
            <i class="fa-solid fa-pencil text-white text-xl"></i>
        </template>
        <template #titulo>
            <p class="text-white">
                Cambiar Planilla de 'Inserte Nombre'
            </p>
        </template>
        <template #body>
            <div class="flex flex-col gap-4">
            </div>
        </template>
        <template #footer>
            <Button label="Cancelar" severity="danger" icon="fa fa-circle-xmark" @click="openChangeModal = false" />
            <Button label="Guardar" severity="success" icon="pi pi-save" :loading="saveLoading"
                @click="submitChange()" />
        </template>
    </CustomModal>
</template>
