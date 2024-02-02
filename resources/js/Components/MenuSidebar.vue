<template>
    <div
        class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-20 transition-all duration-300">
        <div class="h-full bg-white dark:bg-dark">
            <div class="flex items-center justify-center px-2 py-3">
                <div class="flex items-center main-logo shrink-0">
                    <ApplicationLogo class="justify-center" :letras="true" :width-logo="50" :height-logo="50" />
                </div>
            </div>

            <nav class="h-[calc(100vh-80px)] relative overflow-y-auto">
                <ul role="list" class="relative font-semibold space-y-0.5 p-4 py-0">
                    <li class="">
                        <ul>
                            <li v-for="item in navigation" :key="item.name" class=" nav-item">
                                <Link v-if="!item.children" v-show="item.show" :href="route(item.href)" class="group">
                                <div class="flex items-center group-hover:!text-primary">
                                    <component :is="item.icon" class="w-6 h-6 text-gray-400 shrink-0" aria-hidden="true" />
                                    <h3 class="pl-3 text-black dark:text-[#506690] dark:group-hover:text-white">
                                        {{ item.name }}
                                    </h3>
                                </div>
                                </Link>
                                <button v-else type="button" class="w-full nav-link group"
                                    :class="{ active: activeDropdown === item.name }"
                                    @click="activeDropdown === item.name ? (activeDropdown = null) : (activeDropdown = item.name)">
                                    <div class="flex items-center">
                                        <component :is="item.icon" class="w-6 h-6 text-gray-100 shrink-0 dark:text-white"
                                            aria-hidden="true" />
                                        <h3 class="pl-3  text-black dark:text-[#506690] dark:group-hover:text-white">
                                            {{ item.name }}
                                        </h3>
                                    </div>
                                    <div class="rtl:rotate-180 dark:group-hover:text-white transition delay-150"
                                        :class="{ '!rotate-90': activeDropdown === item.name }">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </div>
                                </button>
                                <vue-collapsible :isOpen="activeDropdown === item.name">
                                    <ul class="text-gray-500 sub-menu dark:text-white">
                                        <span v-for="children of item.children">
                                            <li v-if="!children.dev || debug">
                                                <Link :href="route(children.href)">
                                                <span class="">
                                                    <p :class="children.dev ? '-mb-2' : ''">{{ children.name }}</p>
                                                    <small v-if="children.dev" class="text-xs text-red-300 animate-pulse">
                                                        En desarrollo
                                                    </small>
                                                </span>
                                                </Link>
                                            </li>
                                        </span>
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
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { usePermissions } from '@/composable/permission';
import VueCollapsible from 'vue-height-collapsible/vue3';
import {
    CalendarIcon,
    CreditCardIcon,
    DocumentDuplicateIcon,
    IdentificationIcon,
    FolderIcon,
    HomeIcon,
    UsersIcon,
    WrenchScrewdriverIcon
} from '@heroicons/vue/24/outline'
const debug = import.meta.env.VITE_APP_DEBUG
const { hasRole } = usePermissions();
const activeDropdown = ref();

const navigation = [
    {
        name: 'Dashboard',
        href: 'dashboard',
        icon: HomeIcon,
        current: true,
        show: true,
        dev: false
    },
    {
        name: 'Gestion de Personal',
        icon: UsersIcon,
        current: false,
        children: [
            {
                name: 'Mi Personal',
                href: 'personal.index',
                // dev: true
            },
            {
                name: 'Programación',
                href: 'programming',
                // dev: true
            },
            {
                name: 'Parte Actual',
                href: 'personal.index',
                dev: true
            },
            {
                name: 'Solicitudes',
                href: 'personal.index',
                dev: true
            },
            {
                name: 'Personal Activo',
                href: 'personal.activos',
                dev: false
            },
            // { name: 'Programación', href: '#' },
            // { name: 'Parte Diario', href: '#' },
            // { name: 'Informes', href: '#' },
        ],
    },
    {
        name: 'Planillación',
        icon: IdentificationIcon,
        current: false,
        children: [
            {
                name: 'Parte de Personal',
                href: 'dashboard',
                dev: true
            },
            {
                name: 'Planilla',
                href: 'programming',
                dev: true
            },
            {
                name: 'Novedades',
                href: 'personal.index',
                dev: true
            },
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
            {
                name: 'Proyectos',
                href: 'projects.index',
                // dev: true
            }, //gerencia (auth()->user()gerencia)
            {
                name: 'Unidades',
                href: 'ships.index',
                // dev: true
            },
            {
                name: 'Clases',
                href: 'typeShips.index',
                // dev: true
            },
        ],
    },
    {
        name: 'Gestión Comercial',
        icon: CreditCardIcon,
        current: false,
        children: [
            {
                name: 'Clientes',
                href: 'customers.index',
                // dev: true
            },
            {
                name: 'Contratos',
                href: 'contracts.index',
                // dev: true
            },
            {
                name: 'Autorizaciones',
                href: 'authorizations.index',
                dev: true
            },
            {
                name: 'Estimaciones',
                href: 'quotes.index',
                // dev: true
            },
        ],
    },
    {
        name: 'Sugerencias',
        icon: CalendarIcon,
        children: [
            {
                name: 'Ver sugerencias',
                href: 'suggestion.index',
                // dev: true
            },
        ],
    },
    {
        name: 'Documentos',
        href: 'get.projectsGD',
        icon: DocumentDuplicateIcon,
        current: true,
        show: true
    },
    {
        name: 'Almacén',
        children: [
            {
                name: 'Categorías',
                href: 'categories.index',
            },
            {
                name: 'Equipos y Herramientas',
                href: 'tools.index',
                dev: true
            },
            {
                name: 'Asignaciones',
                href: 'assignmentTool.index',
                dev: true
            },
        ],
        icon: WrenchScrewdriverIcon,
        current: false
    },
    // { name: 'Documents', href: '#', icon: DocumentDuplicateIcon, current: false },
    // { name: 'Reports', href: '#', icon: ChartPieIcon, current: false },
]
</script>
