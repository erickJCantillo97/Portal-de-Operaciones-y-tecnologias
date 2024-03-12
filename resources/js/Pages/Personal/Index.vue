<script setup>
import '../../../sass/dataTableCustomized.scss'
import { FilterMatchMode, FilterOperator } from 'primevue/api'
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext'
import Listbox from 'primevue/listbox'
import Loading from '@/Components/Loading.vue'
import NoContentToShow from '@/Components/NoContentToShow.vue'
import Textarea from 'primevue/textarea'
import UserTable from '@/Components/UserTable.vue'

const { confirmDelete } = useSweetalert()
const { hasRole, hasPermission } = usePermissions()
const { toast } = useSweetalert()

const props = defineProps({
    miPersonal: Array,
    group: Object,
    groups: Array,
})

const personal = ref([])
const filterGroup = ref()
const groupSelected = ref([])
const loading = ref(false)
const loadingPersonal = ref(false)
const loadingGroups = ref(false)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
})

const showCreateGroupSection = ref(false)
const showButtonsGroupsCrud = ref(true)
const teamworkName = ref()
const descriptionValue = ref()
const noContentToShow = ref(false)

const form = useForm({
    users: [],
    members: [],
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

const getPersonal = async () => {
    loadingPersonal.value = true
    try {
        await axios.get(route('personal.activos'))
            .then((res) => {
                personal.value = res.data.personal
                loadingPersonal.value = false
                noContentToShow.value = true
            })
    } catch (error) {
        console.error(error)
    }
}

const groupPersonal = ref([])
const noMembersToShow = ref(false)

const getPersonalUser = async () => {
    loadingGroups.value = true
    try {
        if (groupSelected.value.id != null) {
            await axios.get(route('get.personal.user', groupSelected.value.id))
                .then((res) => {
                    groupPersonal.value = res.data.personal
                    loadingGroups.value = false
                    noContentToShow.value = true
                })
        } else {
            noMembersToShow.value = true
            console.log('Por favor seleccione un grupo')
        }
    } catch (error) {
        console.error(error)
    }
}

const modalVisible = ref(false)

const showNew = () => {
    modalVisible.value = true
    if (personal.value.length == 0)
        getPersonal()
}

//#region Modal Crear Grupo
const showGroupDialog = ref(false)

const openGroupDialog = () => {
    showGroupDialog.value = true
    if (personal.value.length != 0) {
        getPersonal()
    }
}

const clearModal = () => {
    showGroupDialog.value = false
    if (showGroupDialog.value == false) {
        showCreateGroupSection.value = false
        teamworkName.value = ''
        descriptionValue.value = ''
        form.members = []
    }
}

const createGroup = () => {
    router.post(route('teams.store'),
        { name: teamworkName.value, description: descriptionValue.value, personal: form.members }, {
        onSuccess: () => {
            teamworkName.value = ''
            descriptionValue.value = ''
            // form.members = []
            // showGroupDialog.value = false
            showCreateGroupSection.value = false
            toast(`¡Grupo "${teamworkName.value}" creado exitosamente!`, 'success')
        },
        onError: (error) => {
            toast(`Ha ocurrido un error al crear el grupo \n ${teamworkName.value}; ERROR: ${error}`, 'error')
        }
    })
}

const editGroup = () => {
    router.put(route('teams.update'),
        { name: teamworkName.value, description: descriptionValue.value }, {
        onSuccess: () => {
            teamworkName.value = ''
            descriptionValue.value = ''
            // form.members = []
            // showGroupDialog.value = false
            showCreateGroupSection.value = false
            toast(`¡Grupo "${teamworkName.value}" creado exitosamente!`, 'success')
        },
        onError: (error) => {
            toast(`Ha ocurrido un error al crear el grupo \n ${teamworkName.value}; ERROR: ${error}`, 'error')
        }
    })
}

const deleteGroup = () => {
    router.delete(route('teams.destroy'),
        { name: teamworkName.value, description: descriptionValue.value, personal: form.members }, {
        onSuccess: () => {
            teamworkName.value = ''
            descriptionValue.value = ''
            // form.members = []
            // showGroupDialog.value = false
            showCreateGroupSection.value = false
            toast(`¡Grupo "${teamworkName.value}" creado exitosamente!`, 'success')
        },
        onError: (error) => {
            toast(`Ha ocurrido un error al crear el grupo \n ${teamworkName.value}; ERROR: ${error}`, 'error')
        }
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

const addMemberToGroup = (member) => {
    console.log('Agregando miembro... ¿o no?')
    // router.put(route('teams.update', member),
    //     { name: teamworkName.value, description: descriptionValue.value, personal: form.members }, {
    //     onSuccess: () => {
    //         teamworkName.value = ''
    //         descriptionValue.value = ''
    //         // form.members = []
    //         // showGroupDialog.value = false
    //         showCreateGroupSection = false
    //         toast(`¡Grupo "${teamworkName.value}" creado exitosamente!`, 'success')
    //     },
    //     onError: (error) => {
    //         toast(`Ha ocurrido un error al crear el grupo \n ${teamworkName.value}; ERROR: ${error}`, 'error')
    //     }
    // })
}

const removeMemberOfGroup = (member) => {
    form.members = form.members.filter(object => object.Num_SAP !== member.Num_SAP);
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
    { event: 'delete', severity: 'danger', class: '', icon: 'fa-regular fa-trash-can', text: true, outlined: false, rounded: false, show: hasPermission('programming delete') },
]
</script>
<template>
    <AppLayout>
        <div class="w-full overflow-y-auto custom-scroll">
            <!--DATATABLE-->
            <div class="h-[89vh] overflow-y-auto">
                <CustomDataTable title="Mi personal" :loading :rowsDefault="20" :data="miPersonal" :columnas="columnas"
                    :actions="buttons" @delete="del">
                    <template #buttonHeader>
                        <span v-if="filterGroup" class="mr-10">Filtrado por grupo "{{ filterGroup.name }}"</span>
                        <Dropdown v-model="filterGroup" @change="getPersonalFilter" :options="groups" showClear
                            optionLabel="name" placeholder="Mis grupos" :pt="{
                    root: '!h-8',
                    input: '!py-0 !flex !items-center',
                    item: '!p-1 w-full'
                }" />
                        <Button severity=" success" type="button" outlined label="Agregar Personal"
                            icon="pi pi-user-plus" @click="showNew()" v-if="hasPermission('programming create')" />
                        <Button icon="pi pi-users" severity="primary" outlined label="Crear Grupo"
                            @click="openGroupDialog()" v-if="hasPermission('programming create')" />
                    </template>
                </CustomDataTable>
            </div>
        </div>

        <CustomModal v-model:visible="modalVisible" :closable="false" :closeOnEscape="false">
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
                                :filterFields="['Nombres_Apellidos', 'Cargo', 'Identificacion', 'Oficina']"
                                :filter="true" :options="personal" filter optionLabel="name" class="w-full md:w-14rem"
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
                                <UserTable :user="persona" :photo="false">
                                </UserTable>
                                <div>
                                    <Button severity="danger" @click="quitar(persona)" icon="fa-regular fa-trash-can"
                                        text>
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
        <CustomModal v-model:visible="showGroupDialog" :closable="false" :closeOnEscape="false">
            <template #icon>
                <div class="text-white material-symbols-outlined">
                    <span class="material-symbols-outlined text-4xl">
                        group_add
                    </span>
                </div>
            </template>
            <template #titulo>
                <span class="text-xl font-bold text-white white-space-nowrap">Grupos</span>
            </template>
            <template #body>
                <div class="flex py-2 space-x-4">
                    <div class="grid max-w-full w-full space-x-2 grid-cols-2">
                        <!--COLUMNA CREACIÓN DEL GRUPO-->
                        <div v-if="showCreateGroupSection"
                            class=" border border-gray-300 p-2 max-w-full w-full rounded-lg">
                            <div class="mb-2">
                                <label>Nombre del Grupo</label>
                                <InputText type="text" v-model="teamworkName" placeholder="Escriba nombre del grupo"
                                    :pt="{
                    root: '!w-full'
                }" />
                            </div>
                            <div>
                                <label>Descripción</label>
                                <Textarea v-model="descriptionValue" rows="15" col="60"
                                    placeholder="Agregue una descripción al grupo" autoResize :pt="{
                    root: '!w-full !text-sm'
                }" />
                            </div>
                            <div class="flex justify-end space-x-2 mt-2">
                                <Button severity="success" label="Guardar" size="small" @click="createGroup()" />
                                <Button severity="danger" label="Cancelar" size="small"
                                    @click="showCreateGroupSection = false" />
                            </div>
                        </div>
                        <!--COLUMNA SELECCIONAR GRUPO-->
                        <div class="border-0">
                            <div class="flex place-content-between mb-2">
                                <label class="mr-8">Seleccionar Grupo</label>
                                <Button v-if="!showCreateGroupSection" label="Nuevo" icon="pi pi-plus"
                                    @click="showCreateGroupSection = true" size="small" />
                                <Button v-if="groupSelected != 0 && !showCreateGroupSection" label="Editar"
                                    icon="pi pi-pencil" severity="warning" size="small" @click="editGroup()" :pt="{
                    label: '!text-slate-900'
                }" />
                                <Button v-if="groupSelected != 0 && !showCreateGroupSection" label="Eliminar"
                                    icon="pi pi-trash" severity="danger" size="small" @click="deleteGroup()" />
                            </div>
                            <Listbox v-model="groupSelected" listStyle="height:385px" @change="getPersonalUser()"
                                filterPlaceholder="Buscar Grupo" :filterFields="['name', 'description']" :filter="true"
                                :options="groups" filter optionLabel="name" class="w-full md:w-14rem"
                                :loading="groupSelected == 0 ? noContentToShow : groups.length">
                                <template #option="slotProps">
                                    <div class="p-1">
                                        <p class="text-md font-semibold">
                                            {{ slotProps.option.name }}
                                        </p>
                                        <p class="text-xs">
                                            {{ slotProps.option.description }}
                                        </p>
                                    </div>
                                </template>
                                <template #empty>
                                    <Loading message="Cargando Grupos" />
                                    <NoContentToShow subject="Grupos" />
                                </template>
                            </Listbox>
                        </div>
                        <!--COLUMNA PERSONAL DEL GRUPO-->
                        <div v-if="!showCreateGroupSection" class=" transition ease-in-out delay-300">
                            <div class="bg-blue-900 rounded-t-lg">
                                <h3 class="text-center font-bold text-lg text-white">
                                    Personal del Grupo:
                                    "{{ props.groups[0].name != null ? props.groups[0].name : 'No hay Grupos Creados'
                                    }}"
                                </h3>
                            </div>
                            <Accordion :activeIndex="0">
                                <!--Miembros del grupo-->
                                <AccordionTab header="Miembros del Grupo" :pt="{
                    root: '!border-0 !ring-0',
                    headerAction: '!bg-slate-200 !px-4 !py-1 mb-1',
                    headerTitle: '!text-sm',
                    // content: '!h-52'
                }">
                                    <!-- <div class="block space-y-4 h-[475px] custom-scroll overflow-y-auto shadow-lg rounded-lg p-2"> -->
                                    <div v-for="member of groupPersonal" class="flex justify-between">
                                        <UserTable :user="member" :photo="true" />
                                        <div>
                                            <Button severity="danger" @click="removeMemberOfGroup(member)"
                                                icon="fa-regular fa-trash-can" text />
                                        </div>
                                    </div>
                                    <!--TODO CONTENTS MESSAGES-->
                                    <div v-if="noMembersToShow = true" class="size-52">
                                        <p>Por favor seleccione un grupo para visualizar sus miembros.</p>
                                    </div>
                                    <!-- </div> -->
                                </AccordionTab>

                                <!--Agregar personal al grupo-->
                                <AccordionTab header="Agregar personal al grupo" :pt="{
                    root: '!border-0 !ring-0',
                    headerAction: '!bg-slate-200 !px-4 !py-1 mb-1',
                    headerTitle: '!text- sm',
                    // content: '!h-20'
                }">
                                    <div v-for="member of props.miPersonal" class="flex justify-between space-y-4">
                                        <UserTable :user="member" :photo="true" />
                                        <div>
                                            <Button severity="success" @click="addMemberToGroup(member)"
                                                icon="fa-solid fa-plus" outlined />
                                        </div>
                                    </div>
                                </AccordionTab>
                            </Accordion>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <!-- <Button severity="success" label="Guardar" icon="fa-solid fa-floppy-disk" @click="createGroup()" /> -->
                <Button severity="danger" label="Cerrar" icon="fa-regular fa-circle-xmark" @click="clearModal()" />
            </template>
        </CustomModal>
    </AppLayout>
</template>
