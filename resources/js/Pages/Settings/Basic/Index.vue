<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronUpIcon } from '@heroicons/vue/20/solid'
import ConstructiveGrupo from './../Components/SWBS/ConstructiveGrupo.vue';
import Systems from './../Components/SWBS/Systems.vue';
import SubSystems from './../Components/SWBS/SubSystems.vue';
import Processes from './../Components/SWBS/Processes.vue';
import BaseActivities from './../Components/SWBS/BaseActivities.vue';
import { CreditCardIcon, KeyIcon, SquaresPlusIcon, UserCircleIcon, UserGroupIcon } from '@heroicons/vue/24/outline'
import Gerencias from './../Components/Gerencias.vue';
const menu = ref('VB')
const navigation = [
    { name: 'Variables Basicas', value: 'VB', icon: SquaresPlusIcon, current: true },
    { name: 'SWBS', value: 'SW', icon: KeyIcon, current: false },
//   { name: 'Plan & Billing', value: '#', icon: CreditCardIcon, current: false },
//   { name: 'Team', value: '#', icon: UserGroupIcon, current: false },
//   { name: 'Integrations', value: '#', icon: SquaresPlusIcon, current: false },
]
</script>

<template>
    <AppLayout>
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-5 p-2 -mt-4">
            <aside class="px-2 py-6 sm:px-6 lg:col-span-3 lg:px-0 lg:py-0">
              <nav class="space-y-1">
                <a v-for="item in navigation" :key="item.name"  :class="[menu == item.value ? 'bg-gray-50 text-indigo-700 hover:bg-white hover:text-indigo-700' : 'text-gray-900 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center rounded-md px-3 py-2 text-sm font-medium cursor-pointer']" :aria-current="menu == item.value ? 'page' : undefined" @click="menu = item.value">
                  <component :is="item.icon" :class="[menu == item.value ? 'text-indigo-500 group-hover:text-indigo-500' : 'text-gray-400 group-hover:text-gray-500', '-ml-1 mr-3 h-6 w-6 flex-shrink-0']" aria-hidden="true" />
                  <span class="truncate">{{ item.name }}</span>
                </a>
              </nav>
            </aside>

            <div class="space-y-6 sm:px-6 lg:col-span-9 lg:px-0 overflow-y-auto max-h-screen custom-scroll">
                <div class="w-full">
                    <div class="mx-auto w-full  rounded-2xl bg-white p-2" v-if="menu == 'VB'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Variables de Sistema</h3>
                            <p class="mt-1 text-sm text-gray-500 text-justify">Aqui podras encontrar las variables donde se sustenta la aplicación, <strong class="text-red-800">
                                procede con precausión
                            </strong> ó ponte en contacto con los administradores del sistema.</p>
                        </div>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75 uppercase">
                                <span>Gerencias</span>
                                <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-purple-500" />
                            </DisclosureButton>
                            <DisclosurePanel class="pt-4 pb-2 text-sm text-gray-500">
                                    <Gerencias></Gerencias>
                            </DisclosurePanel>
                        </Disclosure>
                    </div>
                    <div class="mx-auto w-full  rounded-2xl bg-white p-2" v-if="menu == 'SW'">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">SWBS</h3>
                            <p class="mt-1 text-sm text-gray-500 text-justify">Aqui podras encontrar la base de datos que soporta el SWBS en el sistema, añade nuevas actividades, sistemas, subsistemas y grupos constructivos.</p>
                        </div>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Grupos Constructivos</span>
                                <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-purple-500" />
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                    <ConstructiveGrupo></ConstructiveGrupo>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Sistemas</span>
                                <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-purple-500" />
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                    <Systems></Systems>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Sub-Sistemas</span>
                                <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-purple-500" />
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                    <SubSystems></SubSystems>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Proceso</span>
                                <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-purple-500" />
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                    <Processes></Processes>
                            </DisclosurePanel>
                        </Disclosure>
                        <Disclosure as="div" class="mt-2" v-slot="{ open }">
                            <DisclosureButton class="flex w-full justify-between rounded-lg bg-purple-100 px-4 py-2 text-left text-sm font-medium text-purple-900 hover:bg-purple-200 focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
                                <span>Actividades basicas</span>
                                <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-purple-500" />
                            </DisclosureButton>
                            <DisclosurePanel class="pb-2 text-sm text-gray-500">
                                    <BaseActivities></BaseActivities>
                            </DisclosurePanel>
                        </Disclosure>
                    </div>
                </div>
            </div>
        </div>
        <!--  -->
    </AppLayout>
</template>
