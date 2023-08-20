<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SimpleCRUD from '@/Components/SimpleCRUD.vue';
import { ref, onMounted } from "vue";

defineProps({
    users: Array,
})
</script>

<template>
    <AppLayout>
        <div class="px-2">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold leading-6 text-gray-900">Usuarios</h1>
                    <p class="mt-2 text-sm text-gray-700">Lista de usuarios de la Aplicación y sus roles.</p>
                </div>

            </div>
            <div class="mt-8 flow-root">
                <div class="md:mx-4 overflow-x-auto">
                    <div class="inline-block min-w-full py-2 align-middle">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead>
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Nombre</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cargo</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Oficina</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
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
                                                <div class="mt-1 text-gray-500">{{ person.username }}@cotecmar.com</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                        Auxiliar de Investigación y Desarrollo
                                      <!-- {{person.gerencia}} -->
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">
                                        <div class="text-gray-900">{{ person.gerencia }}</div>
                                        <div class="mt-1 text-gray-500">{{ person.oficina }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-sm text-gray-500">Super Administrador</td>
                                    <td class="relative whitespace-nowrap py-3 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, {{ person.name }}</span></a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
        </AppLayout>
</template>
