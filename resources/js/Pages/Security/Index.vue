<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';
import Button from 'primevue/button';
import CustomModal from '@/Components/CustomModal.vue';
import { ref } from 'vue';
import Listbox from 'primevue/listbox';
import Empty from '@/Components/Empty.vue';
import { useForm } from '@inertiajs/vue3';
import { useSweetalert } from '@/composable/sweetAlert'
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
const userSelect = ref()
const userDetalis = (event, data) => {
    modalUserDetails.value = true
    userSelect.value = data
}
const rolSelect = ref(null)
const rolSelectUser = ref(null)

const modalRol = ref(false)
const rol = useForm({
    id: null,
    name: null,
    details: null,
    permissions: []
})

const openModalRol = (r) => {
    rol.reset()
    console.log(r)
    if (r != null) {
        rol.id = r.id
        rol.name = r.name
        rol.details = r.details
        Object.assign(rol.permissions, r.permissions)
    }
    modalRol.value = true
}

const saveRol = () => {

    rol.post(route(''), {
        onSuccess: (s) => {
            console.log(s)
            rol.reset()
        },
        onError: (e) => {
            console.log(e)
        }
    })
}

const deleteRol = async (id) => {
    rol.reset()
    rol.processing = true
    await confirmDelete(id, 'rol', 'ruta')
    rol.processing = false
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

</script>
<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <div class="px-4 space-y-2">
                <h1 class="text-3xl font-bold text-primary">Roles</h1>
                <p class="text-sm text-gray-700 italic w-1/2 text-justify">
                    Un rol proporciona acceso a menús y funciones predefinidos para que, dependiendo del rol asignado, un
                    administrador pueda tener acceso de lo que cada usuario necesita.
                </p>
            </div>
            <div class="grid grid-cols-4 gap-8 m-4">
                <div class="shadow-lg rounded-lg p-1 bg-gray-50" v-for="rol in   roles  ">
                    <div class="flex px-2">
                        <div class="flex justify-between w-full">
                            <span class="text-sm text-gray-600">Total {{ rol.permissions.length }} Permisos</span>
                            <span class="flex">
                                <img :src="user.photo" v-for="(user, index) of  rol.users " alt="" :title="user.short_name"
                                    class="rounded-full size-6 object-cover -mr-2 hover:mr-0 hover:scale-150 border-2 border-white"
                                    :class="index > 1 ? 'hidden' : ''">
                                <span v-if="rol.users.length > 1"
                                    class="rounded-full size-6 object-cover cursor-default text-center items-center hover:mr-0 hover:scale-150 border-2 border-white bg-gray-700 text-white">
                                    +{{ rol.users.length - 2 }} </span>
                            </span>
                        </div>
                    </div>
                    <div class="text-gray-900 font-extrabold text-xl mt-2 flex justify-between ml-2">
                        <span>{{ rol.name }}</span>
                        <div>
                            <Button icon="fa fa-pencil" v-tooltip.bottom="'Editar Rol'" :loading="rol.processing" @click="openModalRol(rol)"
                                text></Button>
                            <Button severity="danger" v-tooltip.bottom="'Eliminar Rol'" :loading="rol.processing" @click="deleteRol(rol)"
                                icon="fa fa-trash-can" text></Button>
                        </div>
                    </div>
                </div>
                <div class="shadow-lg rounded-lg p-4 bg-gray-50 flex space-x-10">
                    <img src="/images/men.gif" alt="" class="h-12">
                    <span class="flex items-center" >
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
                        <Listbox v-model="rolSelectUser" :options="userSelect.rolesObj" filter optionLabel="name"
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
                    <Button v-tooltip.top="'Agregar rol'" icon="fa-solid fa-angle-left" severity="success" text outlined />
                    <Button v-tooltip.top="'Agregar todos los roles'" icon="fa-solid fa-angles-left" severity="success" text
                        outlined />
                    <Button v-tooltip.top="'Quitar rol'" icon="fa-solid fa-angle-right" severity="danger" text outlined />
                    <Button v-tooltip.top="'Quitar todos los roles'" icon="fa-solid fa-angles-right" severity="danger" text
                        outlined />
                </span>
                <div class="border w-full text-center rounded-lg">
                    <p class="w-full text-center font-bold border-b">
                        Roles para agregar
                    </p>
                    <div class="p-1">
                        <Listbox v-model="rolSelect" filter optionLabel="name" class="w-full md:w-14rem"
                            :options="roles.filter((rol) => { return !userSelect.rolesObj.some((userRol) => { return rol.name === userRol.name }) })"
                            :pt="{
                                list: '!h-[25vh]',
                                header: '!p-1',
                                filterInput: '!h-8',
                                item: '!h-8 !p-1 !flex !justify-center'
                            }" />
                    </div>
                </div>
                <!-- <div class="space-y-1 border text-center rounded-lg">
                    <p class="w-full text-center font-bold border-b">
                        {{
                            rolSelectUser ? 'Permisos del rol ' + rolSelectUser.name : 'Permisos del usuario'
                        }}
                    </p>
                    <div v-if="rolSelectUser" class="p-1">
                        <Listbox v-model="rolSelectUser" :options="rolSelectUser.permissions" filter optionLabel="name"
                            class="w-full h-full md:w-14rem" :pt="{
                                list: '!h-[25vh]',
                                header: '!p-1',
                                filterInput: '!h-8',
                                item: '!h-8 !p-1 !flex !justify-center'
                            }">
                        </Listbox>
                    </div>
                    <div v-else v-for="role in userSelect.rolesObj" class="h-[30vh] overflow-y-auto">
                        <p class="text-sm uppercase" v-for="permission in role.permissions">
                            {{ permission.name }}
                        </p>
                    </div>
                </div> -->
            </div>
            <!-- <span class="grid grid-cols-2 gap-3 py-0.5">
                <span class="flex justify-center gap-2">
                    <Button icon="fa-solid fa-angle-up" severity="success" text outlined />
                    <Button icon="fa-solid fa-angles-up" severity="success" text outlined />
                    <Button icon="fa-solid fa-angle-down" severity="danger" text outlined />
                    <Button icon="fa-solid fa-angles-down" severity="danger" text outlined />
                </span>
                <span class="flex justify-center gap-2">
                    <Button icon="fa-solid fa-angle-up" severity="success" text outlined />
                    <Button icon="fa-solid fa-angles-up" severity="success" text outlined />
                    <Button icon="fa-solid fa-angle-down" severity="danger" text outlined />
                    <Button icon="fa-solid fa-angles-down" severity="danger" text outlined />
                </span>
            </span> -->
            <!-- <div class="grid grid-cols-2 gap-3 mb-1">
                <div class="border text-center rounded-lg">
                    <p class="w-full text-center font-bold border-b">
                        Roles para agregar
                    </p>
                    <div class="p-1">
                        <Listbox v-model="rolSelect" :options="roles" filter optionLabel="name" class="w-full md:w-14rem"
                            :pt="{
                                list: '!h-[25vh]',
                                header: '!p-1',
                                filterInput: '!h-8',
                                item: '!h-8 !p-1 !flex !justify-center'
                            }" />
                    </div>
                </div>
                <div class="border text-center rounded-lg">
                    <p class="w-full text-center font-bold border-b">
                        {{
                            rolSelect ? 'Permisos del rol ' + rolSelect.name : 'Todos los permisos'
                        }}
                    </p>
                    <div class="p-1">
                        <Listbox v-model="rolSelect" :options="rolSelect ? rolSelect.permissions : permisos" filter
                            optionLabel="name" class="w-full md:w-14rem" :pt="{
                                list: '!h-[25vh]',
                                header: '!p-1',
                                filterInput: '!h-8',
                                item: '!h-8 !p-1 !flex !justify-center'
                            }" />
                    </div>
                </div>
            </div> -->
        </template>
    </CustomModal>
    <!-- #endregion -->
    <!-- #region añadir rol -->
    <CustomModal v-model:visible="modalRol" width="70vw" :titulo="rol.id ? 'Actualizar rol' : 'Guardar nuevo rol'"
        icon="fa-solid fa-user-tag">
        <template #body>
            <CustomInput v-model:input="rol.name" class="w-full" label="Nombre del rol"
                :invalid="rol.errors.name ? true : false" :errorMessage="rol.errors.name" />
            <CustomInput v-model:input="rol.details" :rowsTextarea="2" class="w-full" label="Descripcion"
                :invalid="rol.errors.details ? true : false" :errorMessage="rol.errors.details" type="textarea" />
            <!-- {{ rol.permissions }} -->
            <div class="mt-3 border p-1 rounded-md">
                <span class="flex items-center space-x-3">
                    <p class="font-bold">Permisos</p>
                    <CustomInput v-model:input="permissionsfilter" icon="fa-solid fa-magnifying-glass" :rowsTextarea="2"
                        class="w-40" type="search" />
                </span>
                <div class="p-1 grid grid-cols-4 gap-1 h-[30vh] overflow-y-auto">
                    <span v-if="filter().length > 0"
                        class="border h-6 text-center capitalize border-success flex justify-center items-center rounded-md cursor-pointer"
                        :class="rol.permissions.find((permission) => permission.name === permiso.name) ? 'bg-success fa-regular fa-circle-check text-white' : 'bg-success-light'"
                        v-for="permiso in filter()" @click="permissionsClic(permiso)">
                        <p class="font-sans ml-1">{{ permiso.name }}</p>
                    </span>
                    <div v-else class="w-full col-span-4">
                        <Empty message="Sin resultados" />
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <Button severity="danger" label="Cancelar" :disabled="rol.processing" />
            <Button severity="success" :label="rol.id ? 'Actualizar' : 'Guardar'" @click="saveRol()"
                :loading="rol.processing" />
        </template>
    </CustomModal>
    <!-- endregion -->
</template>
