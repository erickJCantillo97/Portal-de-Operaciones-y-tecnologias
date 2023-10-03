<template>
    <div class="collapsible-vertical" :class="menu ? 'toggle-sidebar' : ''">
        <div class="bg-GECON hiden"></div>


        <main class="flex-1">
            <div class="fixed inset-0 bg-[black]/60 z-10" :class="{ hidden: !menu }" @click="menu = !menu"></div>

            <MenuSidebar class="lg:block " :class="{ hidden: !menu }"></MenuSidebar>
            <div
                class="sticky z-10 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white pl-8 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 justify-between md:ml-16">
                <div>
                    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="menu = !menu">
                        <span class="sr-only">Open sidebar</span>
                        <Bars3CenterLeftIcon class="h-6 w-6" aria-hidden="true" />
                    </button>
                    <h1 class="hidden lg:block text-primary font-extrabold text-xl">Portal de Operaciones Tecnológicas e
                        Inteligencia Artificial</h1>
                </div>
                <div class="flex ">
                    <div class="flex items-center ">
                        <button type="button" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">View notifications</span>
                            <div class="relative w-6 h-6" title="Notificaciones">
                                <BellIcon aria-hidden="true" />
                                <span class="flex h-3 w-3">
                                    <span
                                        class="absolute inline-flex rounded-full h-3 w-3 -mt-7 ml-3 bg-red-500 animate-pulse"></span>
                                </span>
                            </div>
                        </button>
                        <DropdownSetting title="Utilidades"></DropdownSetting>
                        <Menu as="div" class="relative inline-block text-left">
                            <div title="Perfil">
                                <MenuButton
                                    class="inline-flex w-full items-center justify-center rounded-md  px-4 py-2 text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full bg-gray-50" :src="$page.props.auth.user.photo"
                                        alt="" />
                                    <span class="hidden lg:flex lg:items-center">
                                        <span class="ml-4 text-sm font-semibold leading-6 text-gray-900"
                                            aria-hidden="true">{{ $page.props.auth.user.short_name }}</span>
                                    </span>
                                    <ChevronDownIcon class="ml-2 -mr-1 h-5 w-5 text-violet-200 hover:text-violet-100"
                                        aria-hidden="true" />
                                </MenuButton>
                            </div>

                            <transition enter-active-class="transition duration-100 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0">
                                <MenuItems
                                    class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="px-1 py-1">
                                        <MenuItem v-slot="{ active }">
                                        <button @click="logout" :class="[
                                            active ? 'bg-primary text-white' : 'text-gray-900',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',]">
                                            <ArrowLeftCircleIcon :active="active" class="mr-2 h-5 w-5 text-violet-400"
                                                aria-hidden="true" />
                                            Salir
                                        </button>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </div>
            <div class=" sm:ml-20 sm:mr-1 mt-1 bg-white shadow-2xl rounded-lg mb-14 ">

                <slot />

                <!-- <div class="pointer-events-none fixed inset-x-0 bottom-0 sm:flex sm:justify-center sm:px-6 sm:pb-5 lg:px-8">
                    <p class="text-sm leading-6 text-white">
                        <button @click="sugerenciaVisible = true">
                            <strong class="font-semibold">¡Bienvenido!</strong><svg viewBox="0 0 2 2"
                                class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
                                <circle cx="1" cy="1" r="1" />
                            </svg>Estas en el portal de pruebas, si tienes sugerencias o fallas reportalas aqui&nbsp;<span
                                aria-hidden="true">&rarr;</span>
                        </button>
                    </p>
                    <div class="flex flex-1 justify-end">

                    </div>
                </div> -->
            </div>
            <div class="pointer-events-none fixed inset-x-0 bottom-0 sm:flex sm:justify-center sm:px-6 sm:pb-5 lg:px-8">
                    <div
                        class="pointer-events-auto flex items-center justify-between gap-x-6 bg-gray-900 px-6 py-2.5 sm:rounded-xl sm:py-3 sm:pl-4 sm:pr-3.5">
                        <p class="text-sm leading-6 text-white">
                            <button @click="sugerenciaVisible = true">
                                <strong class="font-semibold">POT 2023</strong><svg viewBox="0 0 2 2"
                                    class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
                                    <circle cx="1" cy="1" r="1" />
                                </svg>Estas en el portal de pruebas, si tienes sugerencias o fallas reportalas
                                aqui&nbsp;<span aria-hidden="true">&rarr;</span>
                            </button>
                        </p>
                    </div>
                </div>
        </main>

    </div>

    <div class="fixed right-0 w-12 top-1/2 z-50">
        <button v-tooltip="'¿Alguna sugerencia?'" @click="sugerenciaVisible = true" v-if="!sugerenciaVisible"
            class="bg-blue-200 opacity-80 flex-col p-2 rounded-tl-3xl rounded-bl-3xl">
            <QuestionMarkCircleIcon class="-rotate-90 w-6" />
            <p class="rotate-180" style="writing-mode: vertical-lr;">Alguna sugerencia</p>
        </button>
    </div>
    <transition leave-active-class="transition ease-in duration-300" leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div class="chat-popup" v-if="sugerenciaVisible">
            <form class="form-container shadow-2xl border-blue-200 border">
                <div class="flex space-x-5 items-center text-xl font-semibold">
                    <ApplicationLogo class="justify-center" :letras="true" :width-logo="50" :height-logo="50">
                    </ApplicationLogo>
                    <h1>¡Bienvenido!</h1>
                </div>
                <label class="mt-2 text-center" for="msg"><b>¿Tienes algo que comentar?</b></label>
                <textarea class="bg-slate-200 rounded-2xl" v-model=sugerencia
                    placeholder="Escribe comentarios, sugerencias, fallas que tengas dentro del portal. ¡Tu ayuda es muy importante para nosotros!"
                    required></textarea>

                <div class="space-x-3 flex">
                    <div class="w-1/2" v-tooltip.top="{ value: 'Enviar', showDelay: 1000, hideDelay: 300 }">
                        <Button type="button" severity="success" @click="enviaSugerencia()">
                            <CheckIcon class="h-6" />
                        </Button>
                    </div>
                    <div class="w-1/2" v-tooltip.top="{ value: 'Cancelar', showDelay: 1000, hideDelay: 300 }">
                        <Button type="button" severity="danger" @click="sugerenciaVisible = false">
                            <XCircleIcon class="h-6" />
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </transition>
</template>

<script setup>
import { ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { router } from '@inertiajs/vue3'
import {
    Bars3CenterLeftIcon
} from '@heroicons/vue/24/outline'
import {
    BellIcon, ChevronDownIcon, ArrowLeftCircleIcon,
    QuestionMarkCircleIcon,
    XCircleIcon, XMarkIcon,
    CheckIcon
} from '@heroicons/vue/20/solid'
import MenuSidebar from '@/Components/MenuSidebar.vue';
import DropdownSetting from '@/Components/DropdownSetting.vue';
import Button from '@/Components/Button.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import html2canvas from 'html2canvas';
import { useSweetalert } from '@/composable/sweetAlert';
const { toast } = useSweetalert();
const menu = ref(false)

const sugerenciaVisible = ref(false)

const logout = () => {
    router.post(route('logout'));
};

const sugerencia = ref('')
const captura = ref()

const enviaSugerencia = () => {
    console.log(window.location.href)
    console.log(sugerencia.value)
    html2canvas(document.body).then(canvas => {
        captura.value = canvas.toDataURL()
    })
    toast('¡Se ha enviado con exito!', 'success');
    sugerenciaVisible.value = false
    sugerencia.value = null
}
</script>
