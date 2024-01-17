<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SimpleCRUD from '@/Components/SimpleCRUD.vue';
import { ref, onMounted } from "vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

const props = defineProps({
    users: Array,
    roles: Array,
    permisos: Array,
})
const headerRoles = ref([
    {
        header: 'id',
        field: 'id',
        input: false,
        show: false
    },
    {
        header: 'Nombre',
        field: 'name',
        type: 'text',
    },
    {
        header: 'Permisos',
        field: 'permisos',
        input: true,
        type: 'multiple',
        options: props.permisos,
        show: false
    },
])
const headerPermisos = ref([
    {
        header: 'id',
        field: 'id',
        input: false,
        show: false
    },
    {
        header: 'Nombre',
        field: 'name',
        type: 'text',
    },
    {
        header: 'Roles',
        field: 'roles',
        input: true,
        type: 'multiple',
        options: props.roles,
        show: false
    },
])

</script>

<template>
    <AppLayout>
        <TabGroup>
            <TabList class="flex space-x-1 rounded-xl p-1 w-1/2">
                <Tab as="template" v-slot="{ selected }">
                    <button :class="[
                        'w-full  py-2.5 text-sm font-medium leading-5 text-primary',
                        selected ? 'bg-white border-b-2 border-primary' : 'text-primary hover:bg-white/[0.12] ']">
                        Usuarios
                    </button>
                </Tab>
                <Tab as="template" v-slot="{ selected }">
                    <button :class="[
                        'w-full  py-2.5 text-sm font-medium leading-5 text-primary',
                        'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none ',
                        selected ? 'bg-white border-b-2 border-primary' : 'text-primary hover:bg-white/[0.12] ',]">
                        Roles
                    </button>
                </Tab>
                <Tab as="template" v-slot="{ selected }">
                    <button :class="[
                        'w-full  py-2.5 text-sm font-medium leading-5 text-primary',
                        'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none ',
                        selected ? 'bg-white border-b-2 border-primary' : 'text-primary hover:bg-white/[0.12] ',]">
                        Permisos
                    </button>
                </Tab>
            </TabList>

            <TabPanels class="mt-1">
                <TabPanel
                    :class="['rounded-xl bg-white p-1', 'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 ']">
                    <div class="flow-root">
                        <div class="md:mx-4 overflow-x-auto">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th scope="col"
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                                Nombre</th>
                                            <th scope="col"
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cargo</th>
                                            <th scope="col"
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Oficina
                                            </th>
                                            <th scope="col"
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="person in users" :key="person.email">
                                            <td class="whitespace-nowrap py-1 pr-0 text-sm sm:pl-0">
                                                <div class="flex items-center">
                                                    <div class="h-11 w-11 flex-shrink-0">
                                                        <img class="h-11 w-11 rounded-full" :src="person.photo" alt="" />
                                                    </div>
                                                    <div class="ml-1">
                                                        <div class="font-medium text-gray-900">{{ person.name }}</div>
                                                        <div class="mt-1 text-gray-500">{{ person.username }}@cotecmar.com
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                                {{ person.cargo }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                                <div class="text-gray-900">{{ person.gerencia }}</div>
                                                <div class="mt-1 text-gray-500">{{ person.oficina }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                                <p v-for="rol in person.roles">{{ rol.name }}</p>
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-3 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span
                                                        class="sr-only">, {{ person.name }}</span></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </TabPanel>
                <TabPanel
                    :class="['rounded-xl bg-white p-1', 'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 ']">
                    <SimpleCRUD :add="true" url="roles" :edit="true" :delete="true" :headers="headerRoles" :elements="roles"
                        title="Rol"></SimpleCRUD>
            </TabPanel>
            <TabPanel
                :class="['rounded-xl bg-white p-1', 'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 ']">
                <SimpleCRUD :add="true" url="permissions" :edit="true" :delete="true" :headers="headerPermisos"
                    :elements="permisos" title="Permiso"></SimpleCRUD>
            </TabPanel>
        </TabPanels>

    </TabGroup>

</AppLayout></template>
