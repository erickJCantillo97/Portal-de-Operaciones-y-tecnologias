<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { KeyIcon, SquaresPlusIcon, ClockIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import BaseActivities from './../Components/SWBS/BaseActivities.vue';
import ConstructiveGrupo from './../Components/SWBS/ConstructiveGrupo.vue';
import CustomUpload from '@/Components/CustomUpload.vue';
import Gerencias from '../Components/Gerencias.vue';
import Processes from './../Components/SWBS/Processes.vue';
import Shifts from './../Components/Shifts.vue';
import SubSystems from './../Components/SWBS/SubSystems.vue';
import Systems from './../Components/SWBS/Systems.vue';
import Warehouses from '../Components/Warehouses.vue';

const menu = ref('VB')

const navigation = [
    { name: 'Variables Basicas', value: 'VB', icon: SquaresPlusIcon, current: true },
    { name: 'SWBS', value: 'SW', icon: KeyIcon, current: false },
    { name: 'Horarios', value: 'SC', icon: ClockIcon, current: false }, //SC->schedules
    // { name: 'Sugerencias', value: 'SU', icon: UserGroupIcon, current: false },
    // { name: 'Clases de Costo', value: 'CC', icon: SquaresPlusIcon, current: false },
]
</script>
<template>
    <AppLayout>
        <div class="p-2 lg:grid lg:grid-cols-12 lg:gap-x-2">
            <aside class="px-2 py-6 sm:px-6 lg:col-span-2 lg:px-0 lg:py-0">
                <nav class="space-y-1">
                    <a v-for="item in navigation" :key="item.name"
                        :class="[menu == item.value ? 'bg-gray-50 text-indigo-700 hover:bg-white hover:text-indigo-700' : 'text-gray-900 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center rounded-md px-3 py-2 text-sm font-medium cursor-pointer']"
                        :aria-current="menu == item.value ? 'page' : undefined" @click="menu = item.value">
                        <component :is="item.icon"
                            :class="[menu == item.value ? 'text-indigo-500 group-hover:text-indigo-500' : 'text-gray-400 group-hover:text-gray-500', '-ml-1 mr-3 h-6 w-6 flex-shrink-0']"
                            aria-hidden="true" />
                        <span class="truncate">{{ item.name }}</span>
                    </a>
                </nav>
            </aside>

            <div class="space-y-6 sm:px-6 lg:col-span-10 lg:px-0">
                <div class="w-full">
                    <div class="w-full p-2 mx-auto bg-white rounded-2xl" v-if="menu == 'VB'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Variables de Sistema</h3>
                            <p class="mt-1 text-sm text-justify text-gray-500">Aquí encontrarás las variables en la que
                                se
                                sustenta la aplicación, <strong class="text-red-800">
                                    ¡proceda con precaución!
                                </strong> o ponte en contacto con los administradores del sistema.</p>
                        </div>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between w-full items-center px-4 py-2 text-sm font-medium text-left text-white uppercase bg-primary  rounded-lg  focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Gerencias</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pt-4 pb-2 h-[50vh] text-sm text-gray-500">
                                <Gerencias></Gerencias>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between w-full px-4 py-2 items-center text-sm font-medium text-left text-white uppercase bg-primary  rounded-lg  focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Clases de Costo</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pt-4 pb-2 text-sm text-gray-500">
                                <div class="flex justify-center">
                                    <CustomUpload mode="advanced" label-button="Subir Archivo de clases de Costo"
                                        titleModal="Clases de costo" tooltip="Subir Clases" accept=".xlsx,.xls"
                                        :url="route('dashboard')" severity="success" />
                                </div>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between w-full items-center px-4 py-2 text-sm font-medium text-left text-white uppercase bg-primary  rounded-lg  focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Almacenes</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pt-4 pb-2 h-[50vh] text-sm text-gray-500">
                                <Warehouses></Warehouses>
                            </DisclosurePanel>
                        </Disclosure>
                    </div>
                    <div class="w-full p-2 mx-auto bg-white rounded-2xl" v-if="menu == 'SW'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">SWBS</h3>
                            <p class="mt-1 text-sm text-justify text-gray-500">Aquí encontrarás la base de datos que
                                soporta
                                el SWBS en el sistema, añade nuevas actividades, sistemas, subsistemas y grupos
                                constructivos.</p>
                        </div>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between items-center w-full px-4 py-2 text-sm font-medium text-left text-white bg-primary  rounded-lg hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Grupos Constructivos</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                <ConstructiveGrupo></ConstructiveGrupo>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between items-center w-full px-4 py-2 text-sm font-medium text-left text-white bg-primary  rounded-lg hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Sistemas</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                <Systems></Systems>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between items-center w-full px-4 py-2 text-sm font-medium text-left text-white bg-primary  rounded-lg hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Sub-Sistemas</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                <SubSystems></SubSystems>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between items-center w-full px-4 py-2 text-sm font-medium text-left text-white bg-primary  rounded-lg hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Proceso</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                <Processes></Processes>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between items-center w-full px-4 py-2 text-sm font-medium text-left text-white bg-primary  rounded-lg hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Actividades basicas</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                <BaseActivities></BaseActivities>
                            </DisclosurePanel>
                        </Disclosure>
                    </div>
                    <div class="w-full p-2 mx-auto bg-white rounded-2xl" v-if="menu == 'SC'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Horarios</h3>
                            <p class="mt-1 text-sm text-justify text-gray-500">Aquí encontrarás los horarios basicos
                                parametrizados</p>
                        </div>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton
                                class="flex justify-between items-center w-full px-4 py-2 text-sm font-medium text-left text-white uppercase bg-primary  rounded-lg hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Horarios</span>
                                <i class="fa-solid fa-chevron-up" :class="open ? 'rotate-180 transform' : ''"></i>
                            </DisclosureButton>
                            <DisclosurePanel class="pt-4 pb-2 text-sm text-gray-500">
                                <Shifts />
                            </DisclosurePanel>
                        </Disclosure>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
