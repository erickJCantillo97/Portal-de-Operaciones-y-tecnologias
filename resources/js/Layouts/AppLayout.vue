<template>
    <div class="flex items-center gap-x-6 bg-orange-300 px-6 sm:before:flex-1">
        <p class="text-sm leading-6 text-white space-x-2">
            <strong class="font-semibold">Modo de pruebas</strong>
        </p>
        <div class="flex flex-1 justify-end">
            <!-- <button type="button" class="-m-3 p-3 focus-visible:outline-offset-[-4px]">
                <span class="sr-only">Dismiss</span>
                <XMarkIcon class="h-5 w-5 text-white" aria-hidden="true" />
            </button> -->
        </div>
    </div>
    <div class="max-h-screen collapsible-vertical" :class="menu ? 'toggle-sidebar' : ''">
        <main>
            <div class="fixed inset-0 bg-[black]/60 z-10" :class="{ hidden: !menu }" @click="menu = !menu"></div>

            <MenuSidebar class="lg:block " :class="{ hidden: !menu }"></MenuSidebar>
            <div
                class="z-10 flex items-center justify-between h-16 px-4 pl-8 mb-2 bg-white border-b border-gray-200 shadow-sm shrink-0 gap-x-4 sm:gap-x-6 sm:px-6 lg:px-8 md:ml-16">
                <div>
                    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="menu = !menu">
                        <span class="sr-only">Open sidebar</span>
                        <Bars3CenterLeftIcon class="w-6 h-6" aria-hidden="true" />
                    </button>
                    <div class="flex items-center space-x-12 ">
                        <h1 class="hidden text-xl font-extrabold lg:block text-primary">Portal de Operaciones Tecnológicas e
                            Inteligencia Artificial</h1>
                        <!-- <div class="hidden p-2 font-extrabold uppercase bg-orange-300 rounded-md sm:block ">
                            <div class="flex items-center px-5 py-1 space-x-2 text-primary">
                                <i class="pi pi-exclamation-circle"></i>
                                <p class="">Modo de pruebas</p>
                            </div>
                        </div> -->
                        <DolarTRM />
                    </div>
                </div>
                <div class="flex items-center ">
                    <button type="button" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">View notifications</span>
                        <div class="relative w-6 h-6" title="Notificaciones">
                            <BellIcon aria-hidden="true" />
                            <span class="flex w-3 h-3">
                                <span
                                    class="absolute inline-flex w-3 h-3 ml-3 bg-red-500 rounded-full -mt-7 animate-pulse"></span>
                            </span>
                        </div>
                    </button>
                    <DropdownSetting title="Utilidades"></DropdownSetting>
                    <Menu as="div" class="relative inline-block text-left">
                        <div title="Perfil">
                            <MenuButton
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white rounded-md hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                <span class="sr-only">Open user menu</span>
                                <img class="custom-image" :src="$page.props.auth.user.photo" alt="" />
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">{{
                                        $page.props.auth.user.short_name }}</span>
                                </span>
                                <ChevronDownIcon class="w-5 h-5 ml-2 -mr-1 text-violet-200 hover:text-violet-100"
                                    aria-hidden="true" />
                            </MenuButton>
                        </div>

                        <transition enter-active-class="transition duration-100 ease-out"
                            enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="transform scale-100 opacity-100"
                            leave-to-class="transform scale-95 opacity-0">
                            <MenuItems
                                class="z-[999999] absolute right-0 w-56 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="px-1 py-1">
                                    <MenuItem v-slot="{ active }">
                                    <button @click="logout" :class="[
                                        active ? 'bg-primary text-white' : 'text-gray-900',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',]">
                                        <ArrowLeftCircleIcon :active="active" class="w-5 h-5 mr-2 text-violet-400"
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
            <div class="h-[92vh] rounded-lg shadow-2xl g-white sm:ml-20 sm:mr-1">
                <slot />
            </div>
            <div class="fixed right-0 z-50 w-12 top-1/2 hover:animate-pulse">
                <button v-tooltip="'¿Alguna sugerencia?'" @click="sugerenciaVisible = true" v-if="!sugerenciaVisible"
                    class="flex-col p-2 bg-blue-200 opacity-80 rounded-tl-3xl rounded-bl-3xl">
                    <QuestionMarkCircleIcon class="w-6 -rotate-90" />
                    <p class="rotate-180" style="writing-mode: vertical-lr;">Sugerencias</p>
                </button>
            </div>
            <TransitionRoot as="template" :show="sugerenciaVisible">
                <Dialog as="div" class="relative z-10" @close="sugerenciaVisible = false">
                    <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" />
                    </TransitionChild>

                    <div class="fixed inset-0 overflow-hidden">
                        <div class="absolute inset-0 overflow-hidden">
                            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10 pointer-events-none">
                                <TransitionChild as="template"
                                    enter="transform transition ease-in-out duration-500 sm:duration-700"
                                    enter-from="translate-x-full" enter-to="translate-x-0"
                                    leave="transform transition ease-in-out duration-500 sm:duration-700"
                                    leave-from="translate-x-0" leave-to="translate-x-full">
                                    <DialogPanel class="relative w-screen max-w-md pointer-events-auto">
                                        <TransitionChild as="template" enter="ease-in-out duration-500"
                                            enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500"
                                            leave-from="opacity-100" leave-to="opacity-0">
                                            <div
                                                class="absolute left-0 right-0 z-50 flex w-12 pt-4 pr-2 -ml-8 sm:-ml-10 sm:pr-4 top-1/2">
                                                <button type="button"
                                                    class="relative flex-col p-2 bg-blue-200 hover:animate-pulse focus:outline-none opacity-80 rounded-tl-3xl rounded-bl-3xl"
                                                    @click="sugerenciaVisible = false">
                                                    <XCircleIcon class="w-6 -rotate-90" />
                                                    <p class="rotate-180" style="writing-mode: vertical-lr;">Cerrar</p>
                                                </button>
                                            </div>
                                        </TransitionChild>
                                        <div class="flex flex-col h-full overflow-y-scroll bg-white shadow-xl">

                                            <!-- Your content -->
                                            <div class="flex flex-col h-full bg-white divide-y divide-gray-200 shadow-xl">
                                                <div class="flex-1 h-0 overflow-y-auto">
                                                    <div class="px-4 py-6 bg-indigo-700 sm:px-6">
                                                        <div class="flex items-center justify-between">
                                                            <DialogTitle
                                                                class="text-base font-semibold leading-6 text-white">
                                                                ¡Bienvenido a POT!</DialogTitle>
                                                        </div>
                                                        <div class="mt-1">
                                                            <p class="text-sm text-indigo-300">En este espacio podras
                                                                informarnos
                                                                tus opiniones, sugerencias y errores que encuentes en el
                                                                portal
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="h-[60%] p-6 shadow-xl space-y-4 custom-scroll overflow-y-auto bg-white">

                                                        <div class="w-full p-2 border rounded-md shadow-lg">
                                                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                                odit tempora delectus sint repudiandae sed! Sapiente eaque
                                                                ab
                                                                dicta eos cum minima amet cupiditate delectus vero?
                                                                Reprehenderit rem voluptatibus optio?</p>
                                                            <p class="mt-1 text-xs">DD/MM/AAAA</p>
                                                        </div>
                                                        <div class="w-full p-2 border rounded-md shadow-lg">
                                                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                                odit tempora delectus sint repudiandae sed! Sapiente eaque
                                                                ab
                                                                dicta eos cum minima amet cupiditate delectus vero?
                                                                Reprehenderit rem voluptatibus optio?</p>
                                                            <p class="mt-1 text-xs">DD/MM/AAAA</p>
                                                        </div>
                                                        <div class="w-full p-2 border rounded-md shadow-lg">
                                                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                                odit tempora delectus sint repudiandae sed! Sapiente eaque
                                                                ab
                                                                dicta eos cum minima amet cupiditate delectus vero?
                                                                Reprehenderit rem voluptatibus optio?</p>
                                                            <p class="mt-1 text-xs">DD/MM/AAAA</p>
                                                        </div>
                                                        <div class="w-full p-2 border rounded-md shadow-lg">
                                                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Qui
                                                                odit tempora delectus sint repudiandae sed! Sapiente eaque
                                                                ab
                                                                dicta eos cum minima amet cupiditate delectus vero?
                                                                Reprehenderit rem voluptatibus optio?</p>
                                                            <p class="mt-1 text-xs">DD/MM/AAAA</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col justify-between flex-1">
                                                        <div class="px-4 divide-y divide-gray-200 sm:px-6">
                                                            <div class="pt-2">
                                                                <fieldset>
                                                                    <legend
                                                                        class="mb-2 text-base font-bold leading-6 text-gray-900">
                                                                        Tipo de reporte</legend>
                                                                    <div
                                                                        class="flex flex-wrap justify-between gap-3 text-sm align-middle">
                                                                        <div class="flex align-items-center">
                                                                            <RadioButton v-model="tipoReporte"
                                                                                inputId="tipo1" name="sugerencia"
                                                                                value="Sugerencia" />
                                                                            <label for="tipo1"
                                                                                class="ml-2">Sugerencia</label>
                                                                        </div>
                                                                        <div class="flex align-items-center">
                                                                            <RadioButton v-model="tipoReporte"
                                                                                inputId="tipo2" name="opinion"
                                                                                value="Opinion" />
                                                                            <label for="tipo2" class="ml-2">Opinión</label>
                                                                        </div>
                                                                        <div class="flex align-items-center">
                                                                            <RadioButton v-model="tipoReporte"
                                                                                inputId="tipo3" name="error"
                                                                                value="Error" />
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
                                                                        <textarea id="description" name="description"
                                                                            placeholder="Buzon de sugerencias en desarrollo"
                                                                            rows="4" v-model="sugerencia" readonly
                                                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end flex-shrink-0 px-4 py-4">
                                                    <button type="button"
                                                        class="px-3 py-2 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                        @click="sugerenciaVisible = false">Cancelar</button>
                                                    <button type="submit"
                                                        class="inline-flex justify-center px-3 py-2 ml-4 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Guardar</button>
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
        </main>
        <!-- <img :src="captura" alt=""> -->
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { router } from '@inertiajs/vue3'
import {
    Bars3CenterLeftIcon
} from '@heroicons/vue/24/outline'
import {
    BellIcon, ChevronDownIcon, ArrowLeftCircleIcon,
    QuestionMarkCircleIcon,
    XCircleIcon, XMarkIcon,
    CheckIcon,
ExclamationTriangleIcon
} from '@heroicons/vue/20/solid'
import MenuSidebar from '@/Components/MenuSidebar.vue';
import DropdownSetting from '@/Components/DropdownSetting.vue';
import Button from '@/Components/Button.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import html2canvas from 'html2canvas';
import RadioButton from 'primevue/radiobutton';
import Tag from 'primevue/tag';
import DolarTRM from "@/Components/DolarTRM.vue";
import { useSweetalert } from '@/composable/sweetAlert';
const { toast } = useSweetalert();

onMounted(() => {
    document.body.classList.add('overflow-y-hidden');
});

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
    html2canvas(document.body).then(canvas => {
        captura.value = canvas.toDataURL()
    })
    toast('¡Se ha enviado con exito! Gracias por su reporte', 'success');
    sugerenciaVisible.value = false
    sugerencia.value = null
}
</script>
<style scoped>
.custom-image {
    width: 32px;
    height: 32px;
    object-position: 50% 30%;
    border-radius: 99999px;
    object-fit: cover;
}
</style>
