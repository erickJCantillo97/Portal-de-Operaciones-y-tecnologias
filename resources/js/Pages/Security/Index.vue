<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';
import Button from 'primevue/button';
import CustomModal from '@/Components/CustomModal.vue';
import { ref } from 'vue';
import Listbox from 'primevue/listbox';
import Empty from '@/Components/Empty.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { useSweetalert } from '@/composable/sweetAlert'
import { useToast } from "primevue/usetoast";
const toast = useToast();
const { confirmDelete } = useSweetalert();
const props = defineProps({
    users: Array,
    roles: Array,
    permisos: Array,
})

const columnas = [
    {
        field: 'name', header: 'Usuario', filter: true, sortable: true, type: 'object', objectRows: {
            photo: { field: 'photo' },
            primary: { field: 'name' },
            secundary: { field: 'cargo' }
        }
    },
    {
        field: 'roles', header: 'Roles', filter: true, sortable: true, type: 'array', itemClass: 'border uppercase  border-gray-800 px-2 text-sm rounded-lg',
        itemsClass: [
            { text: 'Sin rol', severity: 'danger', class: 'border border-danger rounded-lg px-2 text-danger text-sm uppercase' },
        ]
    },
    // { field: 'roles', header: 'Roles',type:'array', filter:true, itemClass:'border px-2 border-primary rounded-lg' },
    { field: 'gerencia', header: 'Gerencia' },
    // { field: 'status', header: 'Estado' }, 
]
const buttons = [
    { event: 'userDetalis', severity: 'info', icon: 'fa-solid fa-user-tag', text: true },
]

const modalUserDetails = ref(false)
const userSelect = ref({})


function userDetalis(event, data) {
    modalUserDetails.value = true
    Object.assign(userSelect.value, data)
}

const rolAdd = ref(null)
const rolDel = ref(null)
const processing = ref(false)
const userRolUpdate = (option) => {
    processing.value = true
    if (option == 'add') {
        router.post(route('assignment.role.user', userSelect.value.id), { role: rolAdd.value.name }, {
            onSuccess: (s) => {
                userSelect.value.rolesObj.push({ id: rolAdd.value.id, name: rolAdd.value.name })
                rolAdd.value = null
                processing.value = false
                toast.add({ severity: 'success', group: "customToast", text: 'Rol agregado con exito', life: 2000 })
            },
            onError: (e) => {
                processing.value = false
                toast.add({ severity: 'error', group: "customToast", text: 'Ocurrio un error, reintente', life: 2000 })
            }
        })
    } else if (option == 'del') {
        router.post(route('remove.role.user', userSelect.value.id), { role: rolDel.value.name }, {
            onSuccess: (s) => {
                userSelect.value.rolesObj.splice(userSelect.value.rolesObj.findIndex(role => role.id === rolDel.value.id && role.name === rolDel.value.name), 1)
                rolDel.value = null
                processing.value = false
                toast.add({ severity: 'success', group: "customToast", text: 'Rol removido con exito', life: 2000 })
            },
            onError: (e) => {
                processing.value = false
                toast.add({ severity: 'error', group: "customToast", text: 'Ocurrio un error, reintente', life: 2000 })
            }
        })
    }
}

const rolesFilter = () => {
    return props.roles.filter(rol => !userSelect.value.rolesObj.some(userRol => rol.name === userRol.name));
}

//#region crud Roles 
const modalRol = ref(false)
const rol = useForm({
    id: null,
    name: null,
    description: null,
    permissions: []
})
const permision = useForm({
    name: null,
})


const openModalRol = (r) => {
    rol.reset()
    if (r != null) {
        rol.id = r.id
        rol.name = r.name
        rol.description = r.description
        Object.assign(rol.permissions, r.permissions)
    }
    modalRol.value = true
}

const saveRol = () => {
    rol.transform((data) => ({
        ...data,
        name: data.name + '%TOP%' + usePage().props.auth.user.gerencia
    })
    ).post(route('roles.store'), {
        onSuccess: (s) => {
            toast.add({ severity: 'success', group: "customToast", text: 'Rol creado con exito', life: 2000 })
            rol.reset()
        },
        onError: (e) => {
            toast.add({ severity: 'error', group: "customToast", text: 'Ocurrio un error', life: 2000 })
        }
    })
}
const updateRol = () => {
    rol.transform((data) => ({
        ...data,
        name: data.name + '%TOP%' + usePage().props.auth.user.gerencia
    }))
    rol.put(route('roles.update', rol.id), {
        onSuccess: (s) => {
            toast.add({ severity: 'success', group: "customToast", text: 'Rol actualizado con exito', life: 2000 })
            rol.reset()
        },
        onError: (e) => {
            toast.add({ severity: 'error', group: "customToast", text: 'Ocurrio un error', life: 2000 })
        }
    })
}

const deleteRol = async (id) => {
    rol.reset()
    rol.processing = true
    await confirmDelete(id, 'Rol', 'roles')
    rol.processing = false
}

const savePermiso = () => {
    permision.post(route('permissions.store'), {
        onSuccess: (s) => {
            toast.add({ severity: 'success', group: "customToast", text: 'Rol creado con exito', life: 2000 })
            rol.reset()
        },
        onError: (e) => {
            toast.add({ severity: 'error', group: "customToast", text: 'Ocurrio un error', life: 2000 })
        }
    })
}


const permissionsClic = async (permiso) => {
    const indice = await rol.permissions.findIndex((permission) => permission.name === permiso.name)
    if (indice !== -1) {
        rol.permissions.splice(indice, 1)
    } else (

        rol.permissions.push(permiso)
    )
}
const permissionsfilter = ref('')

function filter() {
    if (permissionsfilter == '') {
        return props.permisos
    } else {
        let permisosFiltrados
        permisosFiltrados = props.permisos.filter(p => p.name.toLowerCase().includes(permissionsfilter.value.toLowerCase().trim()))
        return permisosFiltrados
    }
}

const permissionModal = ref(false)
//#endregion

</script>
<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <div class="px-4 space-y-2 flex ">
                <div>
                    <h1 class="text-3xl font-bold text-primary">Roles</h1>
                    <p class="text-sm text-gray-700 italic w-1/2 text-justify">
                        Un rol proporciona acceso a menús y funciones predefinidos para que, dependiendo del rol
                        asignado,
                        un
                        administrador pueda tener acceso de lo que cada usuario necesita.
                    </p>
                </div>
                <div>

                    <Button label="Nuevo Permiso" icon="fa fa-plus" @click="permissionModal = true"
                        v-if="$page.props.auth.user.id == 2" />
                </div>
            </div>
            <div class="grid grid-cols-4 gap-8 m-4">
                <div v-tooltip.top="rol.description" class="cursor-default shadow-lg rounded-lg p-1 bg-gray-50"
                    v-for="rol in roles">
                    <div class="flex px-2">
                        <div class="flex justify-between w-full">
                            <span class="text-sm text-gray-600">Total {{ rol.permissions.length }} Permisos</span>
                            <span class="flex">
                                <img :src="user.photo" v-for="(user, index) of  rol.users " alt=""
                                    :title="user.short_name"
                                    class="rounded-full size-6 object-cover -mr-2 hover:mr-0 hover:scale-150 border-2 border-white"
                                    :class="index > 4 ? 'hidden' : ''">
                                <span v-if="rol.users.length > 4"
                                    class="rounded-full size-6 object-cover cursor-default text-center items-center hover:mr-0 hover:scale-150 border-2 border-white bg-gray-700 text-white">
                                    +{{ rol.users.length - 4 }} </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-gray-900 font-extrabold text-xl mt-2 flex justify-between ml-2">
                        <span>{{ rol.name }}</span>
                        <div v-if="!(rol.name == 'Super Admin')">
                            <Button icon="fa fa-pencil" v-tooltip.bottom="'Editar Rol'" :loading="rol.processing"
                                @click="openModalRol(rol)" text></Button>
                            <Button severity="danger" v-tooltip.bottom="'Eliminar Rol'" :loading="rol.processing"
                                @click="deleteRol(rol)" icon="fa fa-trash-can" text></Button>
                        </div>
                    </div>
                </div>
                <div class="shadow-lg rounded-lg p-4 bg-gray-50 flex space-x-10">
                    <img src="/images/men.gif" alt="" class="h-12">
                    <span class="flex items-center">
                        <Button label="Nuevo Rol" icon="fa fa-plus" @click="openModalRol()" />
                        <!-- <p class="mt-2 text-sm text-gray-700 italic">Añadir si no Existe el Rol</p> -->
                    </span>
                </div>
            </div>
            <CustomDataTable :data="users" :rowsDefault="20" :actions="buttons" title="Usuarios de la Aplicación"
                :columnas="columnas" @userDetalis="userDetalis">
            </CustomDataTable>
        </div>
    </AppLayout>

    <!--#region modal de usuario -->
    <CustomModal v-model:visible="modalUserDetails" :footer="false" :titulo="userSelect?.name ?? ''"
        icon="fa-solid fa-user-tag">
        <template #body>
            <div class="flex justify-between gap-3 pb-2">
                <div class="border w-full text-center rounded-lg">
                    <p class="w-full text-center font-bold border-b">
                        Roles actuales
                    </p>
                    <div class="p-1">
                        <Listbox v-model="rolDel" :options="userSelect.rolesObj" filter optionLabel="name"
                            class="w-full h-full md:w-14rem" :pt="{
                            list: '!h-[25vh]',
                            header: '!p-1',
                            filterInput: '!h-8',
                            item: '!h-8 !p-1 !flex !justify-center'
                        }">
                        </Listbox>
                    </div>
                </div>
                <span class="flex flex-col w-10 justify-center gap-2">
                    <Button :loading="processing" :disabled="rolAdd == null" v-tooltip.top="'Agregar rol'"
                        @click="userRolUpdate('add')" icon="fa-solid fa-angle-left" severity="success" text outlined />
                    <!-- <Button v-tooltip.top="'Agregar todos los roles'" icon="fa-solid fa-angles-left" severity="success" text
                        outlined /> -->
                    <Button :loading="processing" :disabled="rolDel == null" v-tooltip.top="'Quitar rol'"
                        @click="userRolUpdate('del')" icon="fa-solid fa-angle-right" severity="danger" text outlined />
                    <!-- <Button v-tooltip.top="'Quitar todos los roles'" icon="fa-solid fa-angles-right" severity="danger" text
                        outlined /> -->
                </span>
                <div class="border w-full text-center rounded-lg">
                    <p class="w-full text-center font-bold border-b">
                        Roles para agregar
                    </p>
                    <div class="p-1">
                        <Listbox v-model="rolAdd" filter optionLabel="name" class="w-full md:w-14rem"
                            :options="rolesFilter()" :pt="{
                            list: '!h-[25vh]',
                            header: '!p-1',
                            filterInput: '!h-8',
                            item: '!h-8 !p-1 !flex !justify-center'
                        }" />
                    </div>
                </div>
            </div>
        </template>
    </CustomModal>
    <!-- #endregion -->

    <!-- #region añadir rol -->
    <CustomModal v-model:visible="modalRol" width="70vw" :titulo="rol.id ? 'Actualizar rol' : 'Guardar nuevo rol'"
        icon="fa-solid fa-user-tag">
        <template #body>
            <CustomInput v-model:input="rol.name" class="w-full" label="Nombre del rol"
                :invalid="rol.errors.name ? true : false" :errorMessage="rol.errors.name" />
            <CustomInput v-model:input="rol.description" :rowsTextarea="2" class="w-full" label="Descripcion"
                :invalid="rol.errors.description ? true : false" :errorMessage="rol.errors.description"
                type="textarea" />
            <!-- {{ rol.permissions }} -->
            <div class="mt-3 border rounded-md">
                <div class="flex items-center space-x-3 border-b shadow-md py-1">
                    <p class="font-bold ml-2">Permisos</p>
                    <CustomInput v-model:input="permissionsfilter" icon="fa-solid fa-magnifying-glass" :rowsTextarea="2"
                        class="w-40" type="search" />
                </div>
                <li class="grid p-1 grid-cols-4 gap-1 h-[35vh] overflow-y-auto">
                    <ul v-if="filter().length > 0"
                        class="border h-6 text-center  border-success flex justify-center items-center rounded-md cursor-pointer"
                        :class="rol.permissions.find((permission) => permission.name === permiso.name) ? 'bg-success  text-white' : 'bg-success-light'"
                        v-for="permiso in filter()" @click="permissionsClic(permiso)">
                        <i v-if="rol.permissions.find((permission) => permission.name === permiso.name)"
                            class="fa-regular fa-circle-check"></i>
                        <p class="font-sans ml-1">{{ permiso.name }}</p>
                    </ul>
                    <div v-else class="w-full col-span-4">
                        <Empty message="Sin resultados" />
                    </div>
                </li>
            </div>
        </template>
        <template #footer>
            <Button severity="danger" label="Cancelar" :disabled="rol.processing" @click="modalRol = false" />
            <Button severity="success" :label="rol.id ? 'Actualizar' : 'Guardar'"
                @click="rol.id ? updateRol() : saveRol()" :loading="rol.processing" />
        </template>
    </CustomModal>
    <!-- endregion -->
    <!-- #region permiso rol -->
    <CustomModal v-model:visible="permissionModal" width="70vw" :titulo="'Agregar Permiso'" icon="fa-solid fa-user-tag">
        <template #body>
            <CustomInput v-model:input="permision.name" class="w-full" label="Nombre del Permiso"
                :invalid="permision.errors.name ? true : false" :errorMessage="permision.errors.name" />

        </template>
        <template #footer>
            <Button severity="danger" label="Cancelar" :disabled="permision.processing"
                @click="permissionModal = false" />
            <Button severity="success" :label="'Guardar'" @click="savePermiso()" :loading="permision.processing" />
        </template>
    </CustomModal>
    <!-- endregion -->
</template>
