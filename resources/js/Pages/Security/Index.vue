<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue';
import Button from 'primevue/button';
import CustomModal from '@/Components/CustomModal.vue';
import { ref } from 'vue';
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
            { text: 'Sin rol', severity: 'primary', class: 'border border-danger rounded-lg px-2 text-danger text-sm uppercase' },
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
                <div class="shadow-lg rounded-lg p-1 bg-gray-50" v-for="rol in  roles ">
                    <div class="flex px-2">
                        <div class="flex justify-between w-full">
                            <span class="text-sm text-gray-600">Total {{ rol.permissions.length }} Permisos</span>
                            <span class="flex">
                                <img :src="user.photo" v-for="user of rol.users" alt="" :title="user.short_name"
                                    class="rounded-full size-8 object-cover -mr-2 hover:mr-0 hover:scale-150 border-2 border-white">
                            </span>
                        </div>
                    </div>
                    <div class="text-gray-900 font-extrabold text-xl mt-2 flex justify-between">
                        <div class="flex justify-between">
                            <span>{{ rol.name }}</span>
                        </div>
                        <div>
                            <Button icon="fa fa-pencil" v-tooltip.bottom="'Editar Rol'" text></Button>
                            <Button severity="danger" v-tooltip.bottom="'Eliminar Rol'" icon="fa fa-trash-can"
                                text></Button>
                        </div>
                    </div>
                </div>
                <div class="shadow-lg rounded-lg p-4 bg-gray-50 flex space-x-10">
                    <img src="/images/men.gif" alt="" class="h-12">
                    <span>
                        <Button label="Nuevo Rol" icon="fa fa-plus" />
                        <!-- <p class="mt-2 text-sm text-gray-700 italic">Añadir si no Existe el Rol</p> -->
                    </span>
                </div>
            </div>
            <CustomDataTable :data="users" :rowsDefault="20" :actions="buttons" title="Usuarios de la Aplicación"
                :columnas="columnas" @userDetalis="userDetalis">
            </CustomDataTable>
        </div>
    </AppLayout>
    <CustomModal v-model:visible="modalUserDetails" titulo="Detalles de usuario" icon="fa-solid fa-user-tag">
        <template #body>
            <span class="grid grid-cols-2">
                <div class="flex items-center space-x-2">
                    <img class="inline-block w-32 h-32 rounded-md"
                        :src="userSelect.photo != null ? userSelect.photo : 'https://ui-avatars.com/api/?name=' + userSelect.name">
                    <div class="w-full p-2">
                        <p class="font-bold text-lg">
                            {{ userSelect.name }}
                        </p>
                        <p class="text-sm">
                            {{ userSelect.cargo }}
                        </p>
                        <p class="text-sm">
                            {{ userSelect.gerencia }}
                        </p>
                        <div class="grid grid-cols-2 gap-2 w-full">
                            <span v-for="role in userSelect.rolesObj" :class="rolSelect == role? 'bg-primary text-white' : '' "
                                class="border border-primary pl-2 rounded-lg flex items-center w-full justify-between h-min">
                                <p class="cursor-pointer w-full h-full font-bold text-sm uppercase"
                                    @click="(rolSelect || rolSelect == role) ? rolSelect = null : rolSelect = role">
                                    {{
                                        role.name
                                    }}
                                </p>
                                <Button rounded severity="danger" @click="console.log(role)" text
                                    icon="fa-solid fa-trash-can" />
                            </span>
                        </div>
                    </div>
                </div>
                <span class="grid grid-cols-2 gap-x-3">
                    <div class="space-y-1 border text-center rounded-lg">
                        <p class="w-full text-center font-bold border-b">
                            {{
                                rolSelect ? 'Permisos del rol ' + rolSelect.name : 'Permisos del usuario'
                            }}
                        </p>
                        <span v-if="rolSelect">
                            <p class="text-sm uppercase" v-for="permission in rolSelect.permissions">
                                {{ permission.name }}
                            </p>
                        </span>
                        <span v-else v-for="role in userSelect.rolesObj">
                            <p class="text-sm uppercase" v-for="permission in role.permissions">
                                {{ permission.name }}
                            </p>
                        </span>
                    </div>
                </span>
            </span>
        </template>
    </CustomModal>
</template>
