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

    <div class="fixed right-0 w-12 top-1/2 z-50 hover:animate-pulse">
        <button v-tooltip="'¿Alguna sugerencia?'" @click="sugerenciaVisible = true" v-if="!sugerenciaVisible"
            class="bg-blue-200 opacity-80 flex-col p-2 rounded-tl-3xl rounded-bl-3xl">
            <QuestionMarkCircleIcon class="-rotate-90 w-6" />
            <p class="rotate-180" style="writing-mode: vertical-lr;">Sugerencias</p>
        </button>
    </div>
    <TransitionRoot as="template" :show="sugerenciaVisible">
        <Dialog as="div" class="relative z-10" @close="sugerenciaVisible = false">
            <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700"
                            enter-from="translate-x-full" enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0"
                            leave-to="translate-x-full">
                            <DialogPanel class="pointer-events-auto relative w-screen max-w-md">
                                <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                                    enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
                                    leave-to="opacity-0">
                                    <div
                                        class="absolute left-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4 right-0 w-12 top-1/2 z-50">
                                        <button type="button"
                                            class="relative hover:animate-pulse focus:outline-none bg-blue-200 opacity-80 flex-col p-2 rounded-tl-3xl rounded-bl-3xl"
                                            @click="sugerenciaVisible = false">
                                            <XCircleIcon class="-rotate-90 w-6" />
                                            <p class="rotate-180" style="writing-mode: vertical-lr;">Cerrar</p>
                                        </button>
                                    </div>
                                </TransitionChild>
                                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">

                                    <!-- Your content -->
                                    <div class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                                        <div class="h-0 flex-1 overflow-y-auto">
                                            <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <DialogTitle class="text-base font-semibold leading-6 text-white">
                                                        ¡Bienvenido a POT!</DialogTitle>
                                                </div>
                                                <div class="mt-1">
                                                    <p class="text-sm text-indigo-300">En este espacio podras informarnos
                                                        tus opiniones, sugerencias y errores que encuentes en el portal</p>
                                                </div>
                                            </div>
                                            <div
                                                class="h-[60%] p-6 shadow-xl space-y-4 custom-scroll overflow-y-auto bg-white">
                                                <div class="w-full">
                                                    <div class="p-2 rounded-md border w-4/5 shadow-lg">
                                                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                            odit tempora delectus sint repudiandae sed! Sapiente eaque ab
                                                            dicta eos cum minima amet cupiditate delectus vero?
                                                            Reprehenderit rem voluptatibus optio?</p>
                                                        <p class="text-xs text-right">DD/MM/AAAA</p>
                                                    </div>
                                                </div>
                                                <div class="w-full flex justify-end">
                                                    <div class="p-2 rounded-md border w-4/5 shadow-lg">
                                                        <p class="text-right">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                            odit tempora delectus sint repudiandae sed! Sapiente eaque ab
                                                            dicta eos cum minima amet cupiditate delectus vero?
                                                            Reprehenderit rem voluptatibus optio?</p>
                                                        <p class="text-xs">DD/MM/AAAA</p>
                                                    </div>
                                                </div>
                                                <div class="w-full">
                                                    <div class="p-2 rounded-md border w-4/5 shadow-lg">
                                                        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                            odit tempora delectus sint repudiandae sed! Sapiente eaque ab
                                                            dicta eos cum minima amet cupiditate delectus vero?
                                                            Reprehenderit rem voluptatibus optio?</p>
                                                        <p class="text-xs text-right">DD/MM/AAAA</p>
                                                    </div>
                                                </div>
                                                <div class="w-full flex justify-end">
                                                    <div class="p-2 rounded-md border w-4/5 shadow-lg">
                                                        <p class="text-right">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                            odit tempora delectus sint repudiandae sed! Sapiente eaque ab
                                                            dicta eos cum minima amet cupiditate delectus vero?
                                                            Reprehenderit rem voluptatibus optio?</p>
                                                        <p class="text-xs">DD/MM/AAAA</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-1 flex-col justify-between">
                                                <div class="divide-y divide-gray-200 px-4 sm:px-6">
                                                    <div class="pt-2">
                                                        <fieldset>
                                                            <legend
                                                                class="text-base font-bold leading-6 mb-2 text-gray-900">
                                                                Tipo de reporte</legend>
                                                            <div
                                                                class="flex flex-wrap gap-3 text-sm align-middle justify-between">
                                                                <div class="flex align-items-center">
                                                                    <RadioButton v-model="tipoReporte" inputId="tipo1"
                                                                        name="sugerencia" value="Sugerencia" />
                                                                    <label for="tipo1" class="ml-2">Sugerencia</label>
                                                                </div>
                                                                <div class="flex align-items-center">
                                                                    <RadioButton v-model="tipoReporte" inputId="tipo2"
                                                                        name="opinion" value="Opinion" />
                                                                    <label for="tipo2" class="ml-2">Opinión</label>
                                                                </div>
                                                                <div class="flex align-items-center">
                                                                    <RadioButton v-model="tipoReporte" inputId="tipo3"
                                                                        name="error" value="Error" />
                                                                    <label for="tipo3" class="ml-2">Error</label>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="pt-3">
                                                        <div>
                                                            <label for="description"
                                                                class="block text-base font-bold leading-6 text-gray-900">Descripción</label>
                                                            <div class="mt-2">
                                                                <textarea id="description" name="description" rows="4"
                                                                    v-model="sugerencia"
                                                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-shrink-0 justify-end px-4 py-4">
                                            <button type="button"
                                                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                @click="sugerenciaVisible = false">Cancelar</button>
                                            <button type="submit" @click="enviaSugerencia()"
                                                class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Guardar</button>
                                        </div>
                                    </div>

                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
    <!-- <img :src="captura" alt=""> -->
</template>

<script setup>
import { ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
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
import RadioButton from 'primevue/radiobutton';

import { useSweetalert } from '@/composable/sweetAlert';
const { toast } = useSweetalert();
const menu = ref(false)

const sugerenciaVisible = ref(false)

const logout = () => {
    router.post(route('logout'));
};

const sugerencia = ref('')
const tipoReporte = ref('Sugerencia')
const captura = ref()

const enviaSugerencia = () => {
    console.log(window.location.href)
    console.log(sugerencia.value)
    console.log(tipoReporte.value)
    // html2canvas(document.body).then(canvas => {
    //     captura.value = canvas.toDataURL()
    // })
    toast('¡Se ha enviado con exito! Gracias por su reporte', 'success');
    sugerenciaVisible.value = false
    sugerencia.value = null
}
</script>
<style>
.fondodiv {
    background-image: url(/svg/cotecmar-logo.svg);
    background-size: 40% 40%;
    background-repeat: no-repeat;
    background-position: center;
    /* background-origin: content-box; */
    /* background-attachment: fixed; */
    /* background-clip: padding-box; */
}
</style>
