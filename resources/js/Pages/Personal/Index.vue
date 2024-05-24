<script setup>
import { ref } from 'vue'
import { usePermissions } from '@/composable/permission';
import { useSweetalert } from '@/composable/sweetAlert'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import CustomModal from '@/Components/CustomModal.vue'
import Listbox from 'primevue/listbox'
import Loading from '@/Components/Loading.vue'
import NoContentToShow from '@/Components/NoContentToShow.vue'
import UserTable from '@/Components/UserTable.vue'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';

import { useToast } from "primevue/usetoast";
import Empty from '@/Components/Empty.vue';
import OverlayPanel from 'primevue/overlaypanel';
import CustomInput from '@/Components/CustomInput.vue';
const toast = useToast();
const { confirmDelete } = useSweetalert()
const { hasRole, hasPermission } = usePermissions()

const personalData = ref()
const personalActive = ref([])
const groups = ref([])
const load = ref({
    myPersonal: true,
    activePersonal: false,
    groups: false,
    personsGroups: false
})


// #region consultas
async function getMyPersonal() {
    try {
        await axios.get(route('personal.index')).then((res) => {
            personalData.value = res.data
        })
    } catch (error) {
        console.log(error)
    }
    load.value.myPersonal = false
}
getMyPersonal()

async function getPersonal() {
    load.value.activePersonal = true
    try {
        await axios.get(route('personal.activos'))
            .then((res) => {
                personalActive.value = res.data.personal
            })
    } catch (error) {
        console.error(error)
    }
    load.value.activePersonal = false
}

async function getGroups() {
    load.value.groups = true
    try {
        await axios.get(route('personal.groups'))
            .then((res) => {
                groups.value = res.data
            })
    } catch (error) {
        console.error(error)
    }
    load.value.groups = false
}
getGroups()

const personsGroups = ref()
const groupSelected = ref()
async function getPersonsGroups(option) {
    if (option == undefined) {
        load.value.personsGroups = true
    }
    try {
        if (groupSelected.value) {
            await axios.get(route('personal.personsGroups', groupSelected.value.id))
                .then((res) => {
                    personsGroups.value = Object.values(res.data)
                })
        } else {
            personsGroups.value = []
        }
    } catch (error) {
        console.error(error)
    }
    load.value.personsGroups = false
}

// #endregion

// #region modales
const modalVisible = ref(false)
const showNew = () => {
    modalVisible.value = true
    if (personalActive.value.length == 0)
        getPersonal()
}

// #endregion


// #region CRUD

const form = ref({
    users: [],
    members: [],
    fecha_devolucion: null
})

const personAdd = () => {
    axios.post(route('personal.store'), form.value)
        .then(async (res) => {
            await getMyPersonal()
            modalVisible.value = false
            toast.add({ severity: 'success', text: 'Personal añadido exitosamente', group: 'customToast', life: 5000 })
        }).catch((error) => {
            console.log(error)
        })
}

const formGroup = ref({
    load: false
})
const crudGroup = ref()
function openCrudGroup(event, data) {
    if (data?.id) {
        form.value.load = false
        formGroup.value.id = data.id
        formGroup.value.name = data.name
        formGroup.value.description = data.description
    } else {
        formGroup.value = {
            load: false
        }
    }
    crudGroup.value.toggle(event)
}
const createGroup = async () => {
    formGroup.value.load = true
    await axios.post(route('teams.store'), formGroup.value)
        .then(async (res) => {
            if (res.data.status) {
                await getGroups()
                toast.add({ severity: 'success', text: res.data.mensaje, group: 'customToast', life: 5000 })
                crudGroup.value.hide()
            }
            else {
                console.log(res)
                toast.add({ severity: 'error', text: res.data.mensaje, group: 'customToast', life: 5000 })
            }
        })
        .catch((error) => {
            console.log(error)
        })
    formGroup.value.load = false
}
const editGroup = async () => {
    formGroup.value.load = true
    await axios.put(route('teams.update', formGroup.value.id), formGroup.value)
        .then(async (res) => {
            if (res.data.status) {
                await getGroups()
                toast.add({ severity: 'success', text: res.data.mensaje, group: 'customToast', life: 5000 })
                crudGroup.value.hide()
            }
            else {
                console.log(res)
                toast.add({ severity: 'error', text: res.data.mensaje, group: 'customToast', life: 5000 })
            }
        })
        .catch((error) => {
            console.log(error)
        })
    formGroup.value.load = false
}
const deleteGroup = async (id) => {
    formGroup.value.itemDelete = id
    await axios.delete(route('teams.destroy', id))
        .then(async (res) => {
            if (res.data.status) {
                await getGroups()
                toast.add({ icon: 'fa-solid fa-trash-can', severity: 'success', text: res.data.mensaje, group: 'customToast', life: 5000 })
            }
            else {
                console.log(res)
                toast.add({ severity: 'error', text: res.data.mensaje, group: 'customToast', life: 5000 })
            }
        })
        .catch((error) => {
            console.log(error)
        })
    formGroup.value.itemDelete = undefined
}

const addMemberToGroup = async (user) => {
    // console.log(user)
    user.load = true
    if (groupSelected.value) {
        await axios.post(route('add.person.team', groupSelected.value.id), { Num_SAP: user.Num_SAP })
            .then((res) => {
                if (res.data.status) {
                    getPersonsGroups('fast')
                    toast.add({ severity: 'success', text: res.data.mensaje, group: 'customToast', life: 5000 })
                    // crudGroup.value.hide()
                }
                else {
                    console.log(res)
                    toast.add({ severity: 'error', text: res.data.mensaje, group: 'customToast', life: 5000 })
                }
            })
            .catch((error) => {
                console.log(error)
            })
    }
    user.load = false
}
const removeMemberToGroup = async (user) => {
    // console.log(user)
    user.load = true
    if (groupSelected.value) {
        await axios.post(route('remove.person.team', groupSelected.value.id), { Num_SAP: user.Num_SAP })
            .then(async (res) => {
                if (res.data.status) {
                    personsGroups.value = personsGroups.value.filter(person => person.Num_SAP != user.Num_SAP)
                    toast.add({ severity: 'success', text: res.data.mensaje, group: 'customToast', life: 5000 })
                }
                else {
                    console.log(res)
                    toast.add({ severity: 'error', text: res.data.mensaje, group: 'customToast', life: 5000 })
                }
            })
            .catch((error) => {
                console.log(error)
            })
    }
    user.load = false
}

// #endregion


// const groupSelected = ref()
const loading = ref(false)

const quitar = (persona) => {
    form.value.users = form.value.users.filter(object => object.Num_SAP !== persona.Num_SAP);
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

const url = [
    {
        ruta: 'personal.index',
        label: 'Mi Personal',
        active: true
    }
]
</script>
<template>
    <AppLayout :href="url">
        <div class="w-full h-full">
            <TabView>
                <TabPanel :pt="{ root: 'w-full' }">
                    <template #header>
                        <div class="flex justify-center w-full items-center space-x-4">
                            <i class="fa-solid fa-people-arrows text-lg"></i>
                            <span class="font-bold">Mi personal</span>
                            <Button @click="showNew()" v-if="hasPermission('programming create')"
                                icon="fa-solid fa-plus" outlined v-tooltip="'Agregar una persona a mi grupo'" label="Agregar"></Button>
                        </div>
                    </template>
                    <div class="h-[80vh]">
                        <CustomDataTable :loading="load.myPersonal" :rowsDefault="20" :data="personalData"
                            :columnas="columnas" :actions="buttons" @delete="del">
                        </CustomDataTable>
                    </div>
                </TabPanel>
                <TabPanel :pt="{ root: 'w-full h-full' }">
                    <template #header>
                        <div class="h-full w-full flex justify-center space-x-3 items-center">
                            <i class="fa-solid text-lg fa-people-group"></i>
                            <p class="h-8 flex items-center">
                                Mis grupos
                            </p>
                        </div>
                    </template>
                    <div class=" h-[75vh] grid w-full space-x-2 grid-cols-2">
                        <!--COLUMNA SELECCIONAR GRUPO-->
                        <div class=" p-1 rounded-md">
                            <div class="flex justify-between items-center p-1">
                                <label class="font-bold">Seleccionar Grupo</label>
                                <Button label="Nuevo" icon="fa-solid fa-plus" @click="openCrudGroup" />
                            </div>
                            <Listbox v-model="groupSelected" listStyle="height:60vh" filterPlaceholder="Buscar Grupo"
                                :filterFields="['name', 'description']" :options="groups" filter optionLabel="name"
                                class="w-full" @change="getPersonsGroups()">
                                <template #option="slotProps">
                                    <div class="flex justify-between items-center">
                                        <div class="p-1">
                                            <p class="text-md font-semibold">
                                                {{ slotProps.option.name }}
                                            </p>
                                            <p class="text-xs">
                                                {{ slotProps.option.description }}
                                            </p>
                                        </div>
                                        <span class="space-x-2 p-1 bg-white rounded-md">
                                            <Button severity="warning" @click="openCrudGroup($event, slotProps.option)"
                                                text icon="fa-solid fa-pen" />
                                            <Button :loading="formGroup.itemDelete == slotProps.option.id"
                                                severity="danger" text icon="fa-solid fa-trash-can"
                                                @click="deleteGroup(slotProps.option.id)"></Button>
                                        </span>
                                    </div>
                                </template>
                                <template #empty>
                                    <NoContentToShow subject="No hay grupos" />
                                </template>
                            </Listbox>
                        </div>
                        <!--COLUMNA PERSONAL DEL GRUPO-->
                        <div class=" transition ease-in-out delay-300">
                            <div class="bg-blue-900 rounded-t-lg">
                                <h3 class="text-center font-bold text-lg text-white">
                                    {{
        groupSelected ?
            'Personal agregado al grupo: ' + groupSelected.name :
            'Seleccione un grupo'
    }}
                                </h3>
                            </div>
                            <Accordion :activeIndex="0">
                                <!--Miembros del grupo-->
                                <AccordionTab header="Miembros del Grupo" :pt="{
        headerAction: 'text-black !bg-slate-200 !px-4 !py-1 mb-1',
        content: '!max-h-[60vh] overflow-y-auto'
    }">
                                    <span v-if="!groupSelected">
                                        <p class="text-center">Por favor seleccione un grupo para visualizar sus
                                            miembros.</p>
                                    </span>
                                    <span v-else-if="load.personsGroups">
                                        <loading />
                                    </span>
                                    <div v-else-if="!load.personsGroups && personsGroups.length == 0" class="">
                                        <p class="text-center">No hay personas agregadas a este grupo.</p>
                                    </div>
                                    <div v-else v-for="member of personsGroups" class="flex justify-between">
                                        <UserTable :user="member" :photo="true" />
                                        <div>
                                            <Button severity="danger" :loading="member.load ? member.load : false"
                                                @click="removeMemberToGroup(member)" icon="fa-regular fa-trash-can"
                                                text />
                                        </div>
                                    </div>
                                    <!-- {{ personsGroups }} -->
                                </AccordionTab>

                                <!--Agregar personal al grupo-->
                                <AccordionTab header="Agregar personal al grupo" :pt="{
        headerAction: 'text-black !bg-slate-200 !px-4 !py-1 mb-1',
        content: '!h-[60vh] overflow-y-auto'
    }">
                                    <div v-for="member of personalData" class="flex justify-between space-y-4">
                                        <UserTable :user="member" :photo="true" />
                                        <div>
                                            <Button :disabled="!groupSelected"
                                                :loading="member.load ? member.load : false" severity="success"
                                                @click="addMemberToGroup(member)" icon="fa-solid fa-plus" outlined />
                                        </div>
                                    </div>
                                </AccordionTab>
                            </Accordion>
                        </div>
                    </div>
                </TabPanel>
            </TabView>
        </div>
    </AppLayout>

    <CustomModal titulo="Añadir Personal" icon="fa-solid fa-person-circle-plus" v-model:visible="modalVisible"
        width="80vw" :closable="true" :closeOnEscape="false">
        <template #body>
            <div class="grid grid-cols-2 gap-4 h-full overflow-hidden">
                <div class="p-1">
                    <h3 class="rounded-t-lg font-bold text-lg">
                        Seleccionar personal
                    </h3>
                    <Listbox multiple v-model="form.users" listStyle="height:60vh"
                        :filterFields="['Nombres_Apellidos', 'Cargo', 'Identificacion', 'Oficina']" :filter="true"
                        :options="personalActive" filter optionLabel="name" class="w-full md:w-14rem"
                        :loading="personalActive.length == 0">
                        <template #option="slotProps">
                            <UserTable :user="slotProps.option"></UserTable>
                        </template>
                        <template #empty>
                            <Loading message="Cargando Personal" />
                        </template>
                    </Listbox>
                </div>
                <div class="p-1">
                    <h3 class="text-center bg-blue-900 rounded-t-lg font-bold text-lg text-white">
                        Personal Seleccionado
                    </h3>
                    <Listbox listStyle="height:60vh"
                        :filterFields="['Nombres_Apellidos', 'Cargo', 'Identificacion', 'Oficina']" :filter="true"
                        :options="form.users" filter optionLabel="name" class="w-full md:w-14rem">
                        <template #option="slotProps">
                            <span class="flex justify-between items-center">
                                <UserTable :user="slotProps.option" :photo="true" />
                                <div>
                                    <Button severity="danger" @click="quitar(slotProps.option)"
                                        icon="fa-regular fa-trash-can" text />
                                </div>
                            </span>
                        </template>
                        <template #empty>
                            <Empty message="Seleccionar personas para agregar a mi personal" />
                        </template>
                    </Listbox>
                </div>
            </div>
        </template>
        <template #footer>
            <Button icon="fa-solid fa-floppy-disk" label="Guardar" severity="success" v-if="form.users.length > 0"
                @click="personAdd()" />
            <Button icon="fa-regular fa-circle-xmark" label="Cancelar" severity="danger"
                @click="modalVisible = false" />
        </template>

    </CustomModal>

    <!--CRUD DE GRUPOS-->
    <OverlayPanel ref="crudGroup">
        <p class="font-bold text-center mb-2 text-lg bg-primary-light rounded-md">
            {{ formGroup?.id ? 'Editando grupo' : 'Agregando grupo' }}
        </p>
        <CustomInput label="Nombre del Grupo" v-model:input="formGroup.name" placeholder="Escriba nombre del grupo" />
        <CustomInput label="Descripción" v-model:input="formGroup.description" type="textarea" />
        <div class="flex justify-end">
            <Button :loading="formGroup.load" :disabled="!(formGroup.name && formGroup.description)"
                icon="fa-solid fa-floppy-disk" severity="success" label="Guardar"
                @click="formGroup?.id ? editGroup() : createGroup()" />
        </div>
    </OverlayPanel>
</template>
