<template>
    <div class="max-w-sm">
        <Popover v-slot="{ open }" class="relative">
            <PopoverButton :class="open ? '' : 'text-opacity-90'"
                class="group inline-flex items-center hover:bg-gray-100 p-1 rounded-full  text-base font-medium text-white hover:text-opacity-100 focus:outline-none">
                <AdjustmentsVerticalIcon :class="open ? '' : 'text-opacity-80'"
                    class="w-5 text-primary transition duration-150 ease-in-out group-hover:text-opacity-100"
                    aria-hidden="true" />
            </PopoverButton>

            <transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-1 opacity-0"
                enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-1 opacity-0">
                <PopoverPanel
                    class="absolute -left-12 lg:-left-36 z-10 mt-3 w-screen max-w-sm -translate-x-1/2 transform px-4 sm:px-0 lg:max-w-3xl">
                    <div class="overflow-hidden rounded-lg ring-1 ring-black ring-opacity-5 shadow-lg shadow-primary">
                        <div class="relative grid gap-8 bg-white p-7 lg:grid-cols-2">
                            <Link :href="route('settings.basic')"
                                class="-m-3 flex items-center rounded-lg p-2 transition duration-150 ease-in-out hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center text-white sm:h-12 sm:w-12">
                                <div>
                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect width="48" height="48" rx="8" fill="#2E3092" />
                                        <path d="M24 11L35.2583 17.5V30.5L24 37L12.7417 30.5V17.5L24 11Z" stroke="#fff"
                                            stroke-width="2" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.7417 19.8094V28.1906L24 32.3812L31.2584 28.1906V19.8094L24 15.6188L16.7417 19.8094Z"
                                            stroke="#fff" stroke-width="2" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M20.7417 22.1196V25.882L24 27.7632L27.2584 25.882V22.1196L24 20.2384L20.7417 22.1196Z"
                                            stroke="#fff" stroke-width="2" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">
                                    Variables Básicas
                                </p>
                                <p class="text-sm text-gray-500">
                                    Buscar, Agregar o Editar variables básicas del sistema
                                </p>
                            </div>
                            </Link>
                            <Link :href="route('security')"
                                class="-m-3 flex items-center rounded-lg p-2 transition duration-150 ease-in-out hover:bg-gray-200 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center text-white sm:h-12 sm:w-12">
                                <div>
                                    <svg width="48" height="48" viewBox="0 0 24 24" class="bg-secondary rounded-lg p-1"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">

                                        <circle cx="12" cy="9" r="2" stroke="#fff" stroke-width="1.5" />
                                        <path
                                            d="M16 15C16 16.1046 16 17 12 17C8 17 8 16.1046 8 15C8 13.8954 9.79086 13 12 13C14.2091 13 16 13.8954 16 15Z"
                                            stroke="#fff" stroke-width="1.5" />
                                        <path
                                            d="M3 10.4167C3 7.21907 3 5.62028 3.37752 5.08241C3.75503 4.54454 5.25832 4.02996 8.26491 3.00079L8.83772 2.80472C10.405 2.26824 11.1886 2 12 2C12.8114 2 13.595 2.26824 15.1623 2.80472L15.7351 3.00079C18.7417 4.02996 20.245 4.54454 20.6225 5.08241C21 5.62028 21 7.21907 21 10.4167C21 10.8996 21 11.4234 21 11.9914C21 14.4963 20.1632 16.4284 19 17.9041M3.19284 14C4.05026 18.2984 7.57641 20.5129 9.89856 21.5273C10.62 21.8424 10.9807 22 12 22C13.0193 22 13.38 21.8424 14.1014 21.5273C14.6796 21.2747 15.3324 20.9478 16 20.5328"
                                            stroke="#fff" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">
                                    Seguridad y Usuarios
                                </p>
                                <p class="text-sm text-gray-500">
                                    Roles, Permisos y Usuarios, aqui puedes configurar los accesos del sistema
                                </p>
                            </div>
                            </Link>
                        </div>

                        <div class="bg-gray-100 p-4">
                            <a href="##"
                                class="flow-root rounded-md px-2 py-2 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50">
                                <span class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">
                                        Documentation
                                    </span>
                                </span>
                                <span class="block text-sm text-gray-500">
                                    Start integrating products and tools
                                </span>
                            </a>
                        </div>
                    </div>
                </PopoverPanel>
            </transition>
        </Popover>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { ChevronDownIcon, AdjustmentsVerticalIcon } from '@heroicons/vue/20/solid'

const solutions = [{
    name: 'Insights',
    description: 'Measure actions your users take',
    href: '##',
    icon: `
            <svg
            width="48"
            height="48"
            viewBox="0 0 48 48"
            fill="none"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            >
            <rect width="48" height="48" rx="8" fill="#FFEDD5" />
            <path
                d="M24 11L35.2583 17.5V30.5L24 37L12.7417 30.5V17.5L24 11Z"
                stroke="#FB923C"
                stroke-width="2"
            />
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M16.7417 19.8094V28.1906L24 32.3812L31.2584 28.1906V19.8094L24 15.6188L16.7417 19.8094Z"
                stroke="#FDBA74"
                stroke-width="2"
            />
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M20.7417 22.1196V25.882L24 27.7632L27.2584 25.882V22.1196L24 20.2384L20.7417 22.1196Z"
                stroke="#FDBA74"
                stroke-width="2"
            />
            </svg>
        `,
},
{
    name: 'Automations',
    description: 'Create your own targeted content',
    href: '##',
    icon: `
            <svg
            width="48"
            height="48"
            viewBox="0 0 48 48"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            >
            <rect width="48" height="48" rx="8" fill="#FFEDD5" />
            <path
                d="M28.0413 20L23.9998 13L19.9585 20M32.0828 27.0001L36.1242 34H28.0415M19.9585 34H11.8755L15.9171 27"
                stroke="#FB923C"
                stroke-width="2"
            />
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M18.804 30H29.1963L24.0001 21L18.804 30Z"
                stroke="#FDBA74"
                stroke-width="2"
            />
            </svg>
        `,
},
{
    name: 'Reports',
    description: 'Keep track of your growth',
    href: '##',
    icon: `
            <svg
            width="48"
            height="48"
            viewBox="0 0 48 48"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            >
            <rect width="48" height="48" rx="8" fill="#FFEDD5" />
            <rect x="13" y="32" width="2" height="4" fill="#FDBA74" />
            <rect x="17" y="28" width="2" height="8" fill="#FDBA74" />
            <rect x="21" y="24" width="2" height="12" fill="#FDBA74" />
            <rect x="25" y="20" width="2" height="16" fill="#FDBA74" />
            <rect x="29" y="16" width="2" height="20" fill="#FB923C" />
            <rect x="33" y="12" width="2" height="24" fill="#FB923C" />
            </svg>
        `,
},
]
</script>
