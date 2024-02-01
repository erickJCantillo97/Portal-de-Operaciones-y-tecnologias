<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue';
import Button from 'primevue/button';
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
    { field: 'roles', header: 'Roles',type:'array', filter:true, itemClass:'border px-2 border-primary rounded-lg' },
    { field: 'gerencia', header: 'Gerencia' },
    // { field: 'status', header: 'Estado' }, 
]
const buttons = [
    { event: 'userDetalis', severity: 'info', icon: 'fa-solid fa-user-tag', text: true },
]

const userDetalis = (event, data) => {
    console.log(data)
}
</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <div class="px-4 space-y-2">
                <h1 class="text-3xl font-bold text-primary">Roles</h1>
                <p class="text-md text-gray-700 italic w-1/2 text-justify">
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
</template>
