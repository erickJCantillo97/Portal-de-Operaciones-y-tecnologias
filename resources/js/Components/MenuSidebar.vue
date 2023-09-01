<template>
    <div
        class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-20 transition-all duration-300">
        <div class="bg-white  h-full">
            <div class="flex justify-between items-center px-2 py-3">
                <div class="main-logo flex items-center shrink-0">
                    <ApplicationLogo :letras="false" :width-logo="50" :height-logo="50"></ApplicationLogo>
                    <span
                        class="text-2xl ml-2  font-semibold align-middle lg:inline dark:text-white-light text-primary">COTECMAR</span>
                </div>

            </div>

            <nav class="h-[calc(100vh-80px)] relative">
                <ul role="list" class="relative font-semibold space-y-0.5 p-4 py-0">
                    <li class="nav-item">
                        <ul>
                            <li v-for="item in navigation" :key="item.name" class=" nav-item">
                                <Link v-if="!item.children" v-show="item.show" :href="route(item.href)" class="group">
                                <div class="flex items-center group-hover:!text-primary">
                                    <component :is="item.icon" class="h-6 w-6 shrink-0 text-gray-400" aria-hidden="true" />
                                    <span class="pl-3 text-black dark:text-[#506690] dark:group-hover:text-white">{{
                                        item.name
                                    }}</span>
                                </div>
                                </Link>
                                <button v-else type="button" class="nav-link group w-full"
                                    :class="{ active: activeDropdown === item.name }"
                                    @click="activeDropdown === item.name ? (activeDropdown = null) : (activeDropdown = item.name)">
                                    <div class="flex items-center">
                                        <component :is="item.icon" class="h-6 w-6 shrink-0 text-gray-400"
                                            aria-hidden="true" />

                                        <span
                                            class="pl-3  text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                item.name
                                            }}</span>
                                    </div>
                                    <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === item.name }">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </button>
                                <vue-collapsible :isOpen="activeDropdown === item.name">
                                    <ul class="sub-menu text-gray-500" v-for="children of item.children">
                                        <li>
                                            <Link :href="route(children.href)">{{ children.name }}</Link>
                                        </li>
                                    </ul>
                                </vue-collapsible>

                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { usePermissions } from '@/composable/permission';

import VueCollapsible from 'vue-height-collapsible/vue3';
import {
    CalendarIcon,
    ChartPieIcon,
    DocumentDuplicateIcon,
    FolderIcon,
    HomeIcon,
    UsersIcon,
} from '@heroicons/vue/24/outline'
const activeDropdown = ref();
const { hasRole } = usePermissions();
const navigation = [
    { name: 'Dashboard', href: 'dashboard', icon: HomeIcon, current: true, show: true },
    {
        name: 'Gestion de Personal',
        icon: UsersIcon,
        current: false,
        children: [
            // { name: 'Planilla', href: '#' },
            // { name: 'Programación', href: '#' },
            // { name: 'Parte Diario', href: '#' },
            // { name: 'Informes', href: '#' },
        ],
    },
    {
        name: 'Gestión de Proyectos',
        icon: FolderIcon,
        current: false,
        children: [
            // { name: 'Proyectos', href: '#' }, //gerencia (auth()->user()gerencia)
            // { name: 'Informes', href: '#' },
            { name: 'Cronograma', href: 'showGantt' },
            // { name: 'Buques', href: '#' },
        ],
    },
    {
        name: 'Gestión Comercial',
        icon: FolderIcon,
        current: false,
        children: [
            { name: 'Contratos', href: 'contracts.index' },
            // { name: 'Autorizaciones', href: '#' },
            // { name: 'Estimaciones', href: '#' },
            { name: 'Clientes', href: 'customers.index' },
        ],
    },
    // { name: 'Calendar', href: '#', icon: CalendarIcon, current: false },
    // { name: 'Documents', href: '#', icon: DocumentDuplicateIcon, current: false },
    // { name: 'Reports', href: '#', icon: ChartPieIcon, current: false },
]
</script>
