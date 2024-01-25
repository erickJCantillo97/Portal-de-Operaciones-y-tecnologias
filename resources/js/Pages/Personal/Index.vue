<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import '../../../sass/dataTableCustomized.scss'
import { FilterMatchMode, FilterOperator } from 'primevue/api'
import { useSweetalert } from '@/composable/sweetAlert'
import Textarea from 'primevue/textarea'
import InputText from 'primevue/inputtext'
import UserTable from '@/Components/UserTable.vue'
import Listbox from 'primevue/listbox'
import CustomModal from '@/Components/CustomModal.vue'
import Loading from '@/Components/Loading.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue';
import Dropdown from 'primevue/dropdown';
const { toast } = useSweetalert()
const { confirmDelete } = useSweetalert()

const props = defineProps({
    miPersonal: Array,
    group: Object,
    groups: Array,

})

const personal = ref([])

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

let form = useForm({
    users: [],
    fecha_devolucion: null
})

const submit = () => {
    router.post(route('personal.store'), form, {
        onSuccess: () => {
            form.users = []
            modalVisible.value = false
            toast('personal añadido exitosamente', 'success')
        }
    })
}

const getPersonal = () => {
    axios.get(route('personal.activos')).then((res) => {
        personal.value = res.data.personal
    })
}



const modalVisible = ref(false)
const showNew = () => {
    modalVisible.value = true
    if (personal.value.length == 0)
        getPersonal()
}

//#region Modal Crear Grupo
const showGroupDialog = ref(false)
const teamwork = ref()

const descriptionValue = ref('')

const openGroupDialog = () => {
    showGroupDialog.value = true
    if (personal.value.length == 0)
        getPersonal()
}

const clearModal = () => {
    showGroupDialog.value = false
    if (showGroupDialog.value == false) {
        teamwork.value = ''
        descriptionValue.value = ''
        form.users = []
    }
}

const createGroup = () => {
    router.post(router('teams.store'), {
        users: form.users,
    })
}

const getPersonalFilter = (event) => {
    loading.value = true
    router.get(route('personal.index'), { id: event.value ? event.value.id : null })
}
//#endregion


const quitar = (persona) => {
    form.users = form.users.filter(object => object.Num_SAP !== persona.Num_SAP);
}

const del = (event, data) => {
    confirmDelete(data.Num_SAP, 'Empleado', 'personal')
}

const columnas = [
    {
        field: 'Nombres_Apellidos', header: 'Nombre', filter: true, sortable: true, type: 'object', objectRows: {
            photo: { field: 'photo' },
            primary: { field: 'Nombres_Apellidos' },
            secundary: { field: 'Cargo' }
        }
    },
    { field: 'Fecha_Final', header: 'Fin contrato', filter: true, sortable: true, type: 'date' },
    { field: 'Costo_Hora', header: 'Costo hora', filter: true, sortable: true, type: 'currency', class: "w-48" },
    { field: 'Costo_Mes', header: 'costo mes', filter: true, sortable: true, type: 'currency', class: "w-52" },
]
const buttons = [
    { event: 'delete', severity: 'danger', class: '', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false },
]

const groupSelect = ref(props.group)
const loading = ref(false)


</script>

<template>
    <AppLayout>
        <div class="w-full overflow-y-auto custom-scroll">
            <!--DATATABLE-->
            <div class="h-[89vh] overflow-y-auto">
                <CustomDataTable title="Mi personal" :loading :rowsDefault="20" :data="miPersonal" :columnas="columnas"
                    :actions="buttons" @delete="del">
                    <template #buttonHeader>
                        <span v-if="groupSelect" class="mr-10">Filtrado por grupo "{{ groupSelect.name }}"</span>
                        <Dropdown v-model="groupSelect" @change="getPersonalFilter" :options="groups" showClear
                            optionLabel="name" placeholder="Mis grupos" :pt="{
                                root: '!h-8',
                                input: '!py-0 !flex !items-center',
                                item: '!p-1 w-full'
                            }" />
                        <Button severity=" success" type="button" outlined label="Agregar Personal" icon="fa-solid fa-plus"
                            @click="showNew()" />
                        <Button icon="pi pi-plus" severity="primary" outlined label="Crear Grupo"
                            @click="openGroupDialog()" />
                    </template>
                </CustomDataTable>
            </div>
        </div>

        <CustomModal v-model:visible="modalVisible">
            <template #icon>
                <span class="text-white material-symbols-outlined text-4xl">
                    engineering
                </span>
            </template>
            <template #titulo>
                <span class="text-xl font-bold text-white white-space-nowrap">Añadir Personal</span>
            </template>
            <template #body>
                <div class="flex py-2 space-x-4">
                    <div class="w-1/2 space-y-8">
                        <div class="p-fluid border-0 p-2 ">
                            <label for="">Seleccionar Personal</label>
                            <Listbox multiple v-model="form.users" listStyle="height:230px"
                                :filterFields="['Nombres_Apellidos', 'Cargo', 'Identificacion', 'Oficina']" :filter="true"
                                :options="personal" filter optionLabel="name" class="w-full md:w-14rem"
                                :loading="personal.length == 0">
                                <template #option="slotProps">
                                    <UserTable :user="slotProps.option"></UserTable>
                                </template>
                                <template #empty>
                                    <Loading message="Cargando Personal" />
                                </template>
                            </Listbox>
                        </div>
                    </div>
                    <div class="w-1/2 ">
                        <div class="bg-blue-900 rounded-t-lg">
                            <h3 class="text-center font-bold text-lg text-white">Personal Seleccionado</h3>
                        </div>
                        <div class="block space-y-4 h-[320px] custom-scroll overflow-y-auto shadow-lg rounded-lg p-2">
                            <div v-for="persona of form.users" class="flex justify-between">
                                <UserTable :user="persona" :photo="true">
                                </UserTable>
                                <div>
                                    <Button severity="danger" @click="quitar(persona)" icon="fa-regular fa-trash-can" text>
                                    </Button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <Button severity="success" v-if="form.users.length > 0" @click="submit()">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <p class="pl-2">Guardar</p>
                </Button>
                <Button severity="danger" @click="modalVisible = false" class="hover:bg-danger">
                    <i class="fa-regular fa-circle-xmark"></i>
                    <p class="pl-2">Cancelar</p>
                </Button>
            </template>
        </CustomModal>

        <!--MODAL CREACIÓN DE GRUPOS-->
        <CustomModal v-model:visible="showGroupDialog">
            <template #icon>
                <div class="text-white material-symbols-outlined">
                    <span class="material-symbols-outlined text-4xl">
                        group_add
                    </span>
                </div>
            </template>
            <template #titulo>
                <span class="text-xl font-bold text-white white-space-nowrap">Crear Grupo</span>
            </template>
            <template #body>
                <div class="flex py-2 space-x-4">
                    <div class="w-1/2 space-y-4">
                        <div class="border border-gray-300 p-2 max-w-full w-full rounded-lg">
                            <div class="mb-2">
                                <label>Nombre del Grupo</label>
                                <InputText type="text" v-model="teamwork" :pt="{
                                    root: '!w-full'
                                }" />
                            </div>

                            <div>
                                <label>Descripción</label>
                                <Textarea v-model="descriptionValue" rows="1" col="60"
                                    placeholder="Agregue una breve descripción al grupo" autoResize :pt="{
                                        root: '!w-full !text-sm'
                                    }" />
                            </div>
                        </div>
                        <div class="border-0">
                            <label for="">Seleccionar Personal</label>
                            <Listbox multiple v-model="form.users" listStyle="height:230px"
                                :filterFields="['Nombres_Apellidos', 'Cargo', 'Identificacion', 'Oficina']" :filter="true"
                                :options="miPersonal" filter optionLabel="name" class="w-full md:w-14rem"
                                :loading="personal.length == 0">
                                <template #option="slotProps">
                                    <UserTable :user="slotProps.option"></UserTable>
                                </template>
                                <template #empty>
                                    <Loading message="Cargando Personal" />
                                </template>
                            </Listbox>
                        </div>
                    </div>
                    <div class="w-1/2 ">
                        <div class="bg-blue-900 rounded-t-lg">
                            <h3 class="text-center font-bold text-lg text-white">Personal Seleccionado</h3>
                        </div>
                        <div class="block space-y-4 h-[475px] custom-scroll overflow-y-auto shadow-lg rounded-lg p-2">
                            <div v-for="persona of form.users" class="flex justify-between">
                                <UserTable :user="persona" :photo="true" />
                                <div>
                                    <Button severity="danger" @click="quitar(persona)" icon="fa-regular fa-trash-can" text>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <Button severity="success" label="Guardar" icon="fa-solid fa-floppy-disk" @click="createGroup()" />
                <Button severity="danger" label="Cancelar" icon="fa-regular fa-circle-xmark" @click="clearModal()" />
            </template>
        </CustomModal>
    </AppLayout>
</template>
