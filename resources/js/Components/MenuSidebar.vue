<template>
    <div
        class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[200px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-20 transition-all duration-300">
        <div class="h-full bg-white dark:bg-dark">
            <div class="flex items-center justify-center px-2 py-3">
                <div class="flex items-center main-logo shrink-0">
                    <ApplicationLogo class="justify-center" :letras="true" :width-logo="40" :height-logo="40" />
                </div>
            </div>

            <nav class="h-[calc(100vh-80px)] relative overflow-y-auto">
                <ul role="list" class="relative font-semibold space-y-0.5 p-4 py-0">
                    <li class="">
                        <ul>
                            <li v-for="item in  navigation " :key="item.name" class=" nav-item">
                                <Link v-if="!item.children" v-show="item.show" :href="route(item.href)" class="group">
                                <div class="flex pl-3 space-x-2 items-center group-hover:!text-primary">
                                    <component :is="item.icon" class="w-8 h-8 text-gray-400 shrink-0"
                                        aria-hidden="true" />
                                    <h3 class="pl-3 text-black dark:text-[#506690] dark:group-hover:text-white">
                                        {{ item.name }}
                                    </h3>
                                </div>
                                </Link>
                                <button v-else type="button" v-show="item.show" class="w-full nav-link group"
                                    :class="{ active: activeDropdown === item.name }"
                                    @click="activeDropdown === item.name ? (activeDropdown = null) : (activeDropdown = item.name)">
                                    <div class="pl-3 space-x-2 flex items-center">
                                        <component :is="item.icon"
                                            class="w-8 h-8 text-gray-100 shrink-0 dark:text-white" aria-hidden="true" />
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
                                        <span v-for="children of  item.children ">
                                            <li v-if="children.show && !children.children">
                                                <Link :href="!children.dev ? route(children.href) : route('dashboard')">
                                                <span class="">
                                                    <p :class="children.dev ? '-mb-2' : ''">{{ children.name }}</p>
                                                    <small v-if="children.dev"
                                                        class="text-xs text-red-300 animate-pulse">
                                                        En desarrollo
                                                    </small>
                                                </span>
                                                </Link>
                                            </li>
                                            <li class="w-full nav-link group" v-if="children.children">
                                                <button type="button" class="w-full nav-link group"
                                                    @click="subActive === children.name ? (subActive = null) : (subActive = children.name)">
                                                    <span>
                                                        {{ children.name }}
                                                    </span>
                                                    <div class=""
                                                        :class="{ '!rotate-90': subActive === children.name }">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.5"
                                                                d="M6.25 19C6.25 19.3139 6.44543 19.5946 6.73979 19.7035C7.03415 19.8123 7.36519 19.7264 7.56944 19.4881L13.5694 12.4881C13.8102 12.2073 13.8102 11.7928 13.5694 11.5119L7.56944 4.51194C7.36519 4.27364 7.03415 4.18773 6.73979 4.29662C6.44543 4.40551 6.25 4.68618 6.25 5.00004L6.25 19Z"
                                                                fill="currentColor" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M10.5119 19.5695C10.1974 19.2999 10.161 18.8264 10.4306 18.5119L16.0122 12L10.4306 5.48811C10.161 5.17361 10.1974 4.70014 10.5119 4.43057C10.8264 4.161 11.2999 4.19743 11.5695 4.51192L17.5695 11.5119C17.8102 11.7928 17.8102 12.2072 17.5695 12.4881L11.5695 19.4881C11.2999 19.8026 10.8264 19.839 10.5119 19.5695Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </div>
                                                </button>

                                                <vue-collapsible :isOpen="subActive === children.name">
                                                    <ul :unmount="false" class="sub-menu text-gray-500 px-10">
                                                        <Link v-for="c in children.children" :href="route(c.href)">
                                                        <li @click="toggleMobileMenu">
                                                            {{ c.name }}
                                                        </li>
                                                        </Link>
                                                    </ul>
                                                </vue-collapsible>
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
const { hasRole, hasPermission } = usePermissions()
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import { usePage } from "@inertiajs/vue3"
import { usePermissions } from '@/composable/permission';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import VueCollapsible from 'vue-height-collapsible/vue3';

const debug = import.meta.env.VITE_APP_DEBUG
const activeDropdown = ref();

const subActive = ref();
// console.log(hasPermission(['customer read']));
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
        show: hasPermission(['programming read',]),
        children: [
            {
                name: 'Mi Personal',
                href: 'personal.index',
                // dev: true
                show: true,
            },
            {
                name: 'Programación',
                href: 'programming',
                // dev: true
                show: hasPermission('programming read'),
            },
            {
                name: 'Parte Actual',
                href: 'personal.index',
                dev: true,
                show: true,
            },
            {
                name: 'Solicitudes',
                href: 'personal.index',
                dev: true,
                show: true,
            },
            {
                name: 'Personal Activo',
                href: 'personal.activos',
                dev: false,
                show: true
            },
            // { name: 'Programación', href: '#' },
            // { name: 'Parte Diario', href: '#' },
            // { name: 'Informes', href: '#' },
        ],
    },
    {
        name: 'Planillación',
        show: true,
        icon: IdentificationIcon,
        current: false,
        children: [
            {
                name: 'Parte de Personal',
                href: 'dashboard',
                dev: true,
                show: true,
            },
            {
                name: 'Planilla',
                href: 'programming',
                dev: true,
                show: true,
            },
            {
                name: 'Novedades',
                href: 'personal.index',
                dev: true,
                show: true,
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
        show: hasPermission(['projects read', 'ship read', 'typeShip read']),
        children: [
            {
                name: 'Proyectos',
                href: 'projects.index',
                // dev: true,
                show: hasPermission('projects read'),
            },
            {
                name: 'Presupuestos',
                href: 'budget.index',
                show: true,
            },
            {
                name: 'Unidades',
                href: 'ships.index',
                // dev: true,
                show: hasPermission('ship read'),
            },
            {
                name: 'Clases',
                href: 'typeShips.index',
                // dev: true,
                show: hasPermission('typeShip read'),
            },
        ],
    },
    {
        name: 'Gestión Comercial',
        icon: CreditCardIcon,
        current: false,
        show: true,
        children: [
            {
                name: 'Clientes',
                href: 'customers.index',
                // dev: true,
                show: hasPermission(['customer read']),
            },
            {
                name: 'Contratos',
                href: 'contracts.index',
                // dev: true,
                show: hasPermission(['contract read']),
            },
            {
                name: 'Autorizaciones',
                href: 'authorizations.index',
                dev: true,
                show: true,
            },
            {
                name: 'Estimaciones',
                href: 'quotes.index',
                // dev: true,
                show: hasPermission(['quote read']),
            },
        ],
    },
    {
        name: 'Sugerencias',
        icon: CalendarIcon,
        show: hasPermission('suggestion reader'),
        children: [
            {
                name: 'Ver sugerencias',
                href: 'suggestion.index',
                // dev: true,
                show: hasPermission('suggestion reader'),
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
        name: 'Materiales y Equipos',
        show: hasPermission(['category read']),
        icon: WrenchScrewdriverIcon,
        current: false,
        children: [
            {
                name: 'Almacen',
                show: true,
                children: [
                    {
                        name: 'Categorías',
                        href: 'categories.index',
                        show: hasPermission(['category read']),
                    },
                    {
                        name: 'Equipos y Herramientas',
                        href: 'tools.index',
                        dev: false,
                        show: hasPermission('tool read'),
                    },
                    {
                        name: 'Asignaciones',
                        href: 'assignmentTool.index',
                        dev: false,
                        show: hasPermission('assignmentTool read'),
                    },
                ]
            },

            {
                name: 'Requerimentos',
                dev: false,
                show: true,
                children: [
                    {
                        name: 'Requemientos Preeliminares',
                        href: 'requirements.index',
                        dev: true,
                        show: true,
                    },
                ]
            },
        ],

    },
    // { name: 'Documents', href: '#', icon: DocumentDuplicateIcon, current: false },
    // { name: 'Reports', href: '#', icon: ChartPieIcon, current: false },
]
</script>
