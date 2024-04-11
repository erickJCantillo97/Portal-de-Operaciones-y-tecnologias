<template>
    <div v-if="debug == 'true'" class="max-h-[3vh] flex items-center px-6 bg-orange-300 gap-x-6 sm:before:flex-1">
        <p class="space-x-2 text-sm text-white">
            <strong class="font-semibold">Modo de pruebas</strong>
        </p>
        <div class="flex justify-end flex-1">
        </div>
    </div>
    <div class="max-h-screen collapsible-vertical" :class="menu ? 'toggle-sidebar' : ''">
        <main class="h-screen flex flex-col">
            <div class="fixed inset-0 bg-[black]/60 z-10" :class="{ hidden: !menu }" @click="menu = !menu"></div>
            <MenuSidebar class="lg:block " :class="{ hidden: !menu }"></MenuSidebar>
            <div
                class="max-h-[6vh] z-10 flex items-center justify-between h-auto px-4 pl-8 mb-2 bg-white border-b border-gray-200 shadow-sm shrink-0 gap-x-4 sm:gap-x-6 sm:px-6 lg:px-8 lg:ml-24">
                <div class="w-full flex items-center">
                    <button type="button" class="-ml-7 p-2.5 text-gray-700 lg:hidden" @click="menu = !menu">
                        <span class="sr-only">Open sidebar</span>
                        <Bars3CenterLeftIcon class="w-6 h-6" aria-hidden="true" />
                    </button>
                    <div class="hidden md:flex">
                        <Link :href="route('dashboard')" v-tooltip.bottom="'Dashboard'">
                        <div class="flex space-x-2 items-center">
                            <i class="fa-solid fa-home text-gray-500"></i>
                            <h1 class="text-center font-bold text-primary text-xl mt-1">
                                TOP
                            </h1>
                        </div>
                        </Link>
                        <div class="flex items-center justify-between">
                            <Breadcrumb :home="home" :model="href" :pt="{
        root: '!h-2 !flex !justify-center !items-center',
        label: '!text-blue-800'
    }">
                                <template #item="{ item, props }">

                                    <div v-if="item.active" class="flex space-x-2 items-center">
                                        <h1 class="text-center text-primary font-semibold text-md">
                                            {{ item.label }}
                                        </h1>
                                    </div>
                                    <Link v-else :href="route(item.ruta)">
                                    <div class="flex space-x-2 items-center">
                                        <h1 class="text-center text-primary text-md">
                                            {{ item.label }}
                                        </h1>
                                    </div>
                                    </Link>
                                </template>
                            </Breadcrumb>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <DolarTRM />
                    <FlyoutNotificationsMenu title="Notificaciones" icon="bellIcon" />
                    <DropdownSetting title="Utilidades" />
                    <Menu as="div" class="relative inline-block text-left">
                        <div title="Perfil">
                            <MenuButton
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white rounded-md hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                <span class="sr-only">Open user menu</span>
                                <img class="size-8 object-[50%_30%] object-cover rounded-full"
                                    :src="$page.props.auth.user.photo" alt="" />
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-xs font-semibold text-gray-900" aria-hidden="true">{{
        $page.props.auth.user.short_name }}</span>
                                </span>
                                <ChevronDownIcon class="size-5 ml-2 -mr-1 text-violet-200 hover:text-violet-100"
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
            <!--
                sm:h-[80vh]  sm:border-red-500
                md:h-[80vh]  md:border-fuchsia-600
                lg:h-[87vh]  lg:border-green-500
                xl:h-[88vh]  xl:border-blue-500
                2xl:h-[90vh]  2xl:border-yellow-500
             -->
            <div class="h-[87vh] 
                sm:h-[83vh]  
                md:h-[85vh]  
                lg:h-[87vh]  
                xl:h-[88vh] 
                2xl:h-[90vh]  
                items-center flex p-1 gap-1 border-gray-200 bg-white rounded-lg shadow-2xl g-white sm:ml-24 sm:mr-1">
                <slot />
            </div>
            <div class="fixed right-[-5px] z-50 w-10 top-1/4 animate-pulse" data-html2canvas-ignore>
                <button v-tooltip="'¿Alguna sugerencia?'" @click="sugerenciaVisible = true" v-if="!sugerenciaVisible"
                    class="flex-col p-2 bg-blue-200 opacity-80 rounded-tl-3xl rounded-bl-3xl">
                    <QuestionMarkCircleIcon class="w-6 -rotate-90" />
                    <p class="rotate-180" style="writing-mode: vertical-lr"></p>
                </button>
            </div>
            <TransitionRoot data-html2canvas-ignore as="template" :show="sugerenciaVisible">
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
                                            enter-from="opacity-0" enter-to="opacity-100"
                                            leave="ease-in-out duration-500" leave-from="opacity-100"
                                            leave-to="opacity-0">
                                            <div
                                                class="absolute left-0 right-0 z-50 flex w-12 pt-4 pr-2 -ml-8 sm:-ml-10 sm:pr-4 top-1/2">
                                                <button type="button"
                                                    class="relative flex-col p-2 bg-blue-200 hover:animate-pulse focus:outline-none opacity-80 rounded-tl-3xl rounded-bl-3xl"
                                                    @click="sugerenciaVisible = false">
                                                    <XCircleIcon class="w-6 -rotate-90" />
                                                    <p class="rotate-180" style="writing-mode: vertical-lr">Cerrar</p>
                                                </button>
                                            </div>
                                        </TransitionChild>
                                        <div class="flex flex-col h-full bg-white shadow-xl">
                                            <!-- Your content -->
                                            <div class="flex flex-col h-full">
                                                <div class="flex-1 shadow-md bg-primary shadow-primary">
                                                    <div class="px-2 py-2 sm:px-6">
                                                        <div class="flex items-center justify-between">
                                                            <DialogTitle
                                                                class="text-base font-semibold leading-6 text-white">
                                                                ¡Bienvenido a TOP!
                                                            </DialogTitle>
                                                        </div>
                                                        <div class="mt-1">
                                                            <p class="text-xs text-indigo-300">
                                                                En este espacio podrás informarnos tus opiniones,
                                                                sugerencias y errores que encuentes en el portal
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="h-full p-4 space-y-2 overflow-y-auto shadow-sm custom-scroll">
                                                    <div v-for="suggestion in suggestions"
                                                        :class="suggestion.type == 'Error' ? 'border-danger' : 'border-primary'"
                                                        class="w-full p-2 border rounded-md shadow-lg">
                                                        <div class="grid grid-cols-10">
                                                            <div class="flex items-center justify-center h-full">
                                                                <svg class="fill-yellow-400"
                                                                    v-if="suggestion.type == 'Sugerencia'" height="2em"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                    <path
                                                                        d="M297.2 248.9C311.6 228.3 320 203.2 320 176c0-70.7-57.3-128-128-128S64 105.3 64 176c0 27.2 8.4 52.3 22.8 72.9c3.7 5.3 8.1 11.3 12.8 17.7l0 0c12.9 17.7 28.3 38.9 39.8 59.8c10.4 19 15.7 38.8 18.3 57.5H109c-2.2-12-5.9-23.7-11.8-34.5c-9.9-18-22.2-34.9-34.5-51.8l0 0 0 0c-5.2-7.1-10.4-14.2-15.4-21.4C27.6 247.9 16 213.3 16 176C16 78.8 94.8 0 192 0s176 78.8 176 176c0 37.3-11.6 71.9-31.4 100.3c-5 7.2-10.2 14.3-15.4 21.4l0 0 0 0c-12.3 16.8-24.6 33.7-34.5 51.8c-5.9 10.8-9.6 22.5-11.8 34.5H226.4c2.6-18.7 7.9-38.6 18.3-57.5c11.5-20.9 26.9-42.1 39.8-59.8l0 0 0 0 0 0c4.7-6.4 9-12.4 12.7-17.7zM192 128c-26.5 0-48 21.5-48 48c0 8.8-7.2 16-16 16s-16-7.2-16-16c0-44.2 35.8-80 80-80c8.8 0 16 7.2 16 16s-7.2 16-16 16zm0 384c-44.2 0-80-35.8-80-80V416H272v16c0 44.2-35.8 80-80 80z" />
                                                                </svg>
                                                                <svg class="fill-primary"
                                                                    v-if="suggestion.type == 'Opinion'" height="2em"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                    <path
                                                                        d="M287.9 0c9.2 0 17.6 5.2 21.6 13.5l68.6 141.3 153.2 22.6c9 1.3 16.5 7.6 19.3 16.3s.5 18.1-5.9 24.5L433.6 328.4l26.2 155.6c1.5 9-2.2 18.1-9.6 23.5s-17.3 6-25.3 1.7l-137-73.2L151 509.1c-8.1 4.3-17.9 3.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1 218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9 19.3-16.3l153.2-22.6L266.3 13.5C270.4 5.2 278.7 0 287.9 0zm0 79L235.4 187.2c-3.5 7.1-10.2 12.1-18.1 13.3L99 217.9 184.9 303c5.5 5.5 8.1 13.3 6.8 21L171.4 443.7l105.2-56.2c7.1-3.8 15.6-3.8 22.6 0l105.2 56.2L384.2 324.1c-1.3-7.7 1.2-15.5 6.8-21l85.9-85.1L358.6 200.5c-7.8-1.2-14.6-6.1-18.1-13.3L287.9 79z" />
                                                                </svg>
                                                                <svg class="fill-danger"
                                                                    v-if="suggestion.type == 'Error'"
                                                                    xmlns="http://www.w3.org/2000/svg" height="2em"
                                                                    viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                    <path
                                                                        d="M256 0c53 0 96 43 96 96v3.6c0 15.7-12.7 28.4-28.4 28.4H188.4c-15.7 0-28.4-12.7-28.4-28.4V96c0-53 43-96 96-96zM41.4 105.4c12.5-12.5 32.8-12.5 45.3 0l64 64c.7 .7 1.3 1.4 1.9 2.1c14.2-7.3 30.4-11.4 47.5-11.4H312c17.1 0 33.2 4.1 47.5 11.4c.6-.7 1.2-1.4 1.9-2.1l64-64c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-64 64c-.7 .7-1.4 1.3-2.1 1.9c6.2 12 10.1 25.3 11.1 39.5H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H416c0 24.6-5.5 47.8-15.4 68.6c2.2 1.3 4.2 2.9 6 4.8l64 64c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0l-63.1-63.1c-24.5 21.8-55.8 36.2-90.3 39.6V240c0-8.8-7.2-16-16-16s-16 7.2-16 16V479.2c-34.5-3.4-65.8-17.8-90.3-39.6L86.6 502.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l64-64c1.9-1.9 3.9-3.4 6-4.8C101.5 367.8 96 344.6 96 320H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96.3c1.1-14.1 5-27.5 11.1-39.5c-.7-.6-1.4-1.2-2.1-1.9l-64-64c-12.5-12.5-12.5-32.8 0-45.3z" />
                                                                </svg>
                                                            </div>
                                                            <div class="flex flex-col justify-between col-span-9 pl-2">
                                                                <p class="text-xs font-bold">{{ suggestion.details }}
                                                                </p>
                                                                <div class="flex pt-1 mt-1 border-t border-gray-300 ">
                                                                    <span v-if="suggestion.type == 'Error'"
                                                                        class="flex justify-center px-2 text-xs font-medium rounded-md"
                                                                        :class="suggestion.status == 1 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-success'">{{
                                                                            suggestion.status == 1 ? 'Pendiente' :
                                                                                'Resuelto'
                                                                        }}</span>
                                                                    <p class="w-full text-xs text-end">{{
            formatDateTime24h(suggestion.created_at) }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex-col justify-end flex-shrink-0 px-2 py-2 space-y-1 shadow-md">
                                                    <div class="grid grid-cols-1">
                                                        <div class="px-2 divide-y divide-gray-100 sm:px-2">
                                                            <fieldset>
                                                                <legend
                                                                    class="mb-2 text-sm font-bold leading-6 text-gray-900">
                                                                    ¿Algo que comentar?</legend>
                                                                <div
                                                                    class="flex flex-wrap justify-between text-sm align-middle">
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
                                                        <div class="px-2 pt-1">
                                                            <label for="description"
                                                                class="block text-sm font-bold leading-6 text-gray-900">Descripción</label>
                                                            <div class="">
                                                                <textarea id="description" name="description"
                                                                    placeholder="Detalle aquí su sugerencia, opinión, o error de la manera mas precisa posible, incluya ubicaciones, colores, el error, la acción que realizo o que necesita realizar"
                                                                    rows="3" v-model="sugerencia"
                                                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end w-full mt-2">
                                                        <button @click="createSuggestion"
                                                            class="inline-flex items-center justify-center p-2 space-x-1 text-xs font-semibold tracking-widest uppercase transition duration-150 ease-in-out bg-white border rounded-md border-primary hover:bg-primary text-primary hover:text-white dark:focus:ring-offset-gray-800 ">
                                                            <i class="fa-regular fa-paper-plane"></i>
                                                            <p>Enviar</p>
                                                        </button>
                                                    </div>

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
        <Footer fontSize="xs" fontColor="white" marginTop="0" class="bg-gray-500 py-1" />
    </div>
    <Toast position="bottom-center" group="customToast"
        :pt="{ content: '!py-1 !items-center !flex !justify-between !px-3 !border-0' }">
        <template #message="slotProps">
            <i v-if="slotProps.message.icon" class="text-3xl" :class="slotProps.message.icon" />
            <i v-else class="text-3xl"
                :class="slotProps.message.severity == 'error' ? 'fa-solid fa-xmark' : slotProps.message.severity == 'success' ? 'fa-solid fa-check' : slotProps.message.severity == 'info' ? 'fa-solid fa-circle-info' : slotProps.message.severity == 'warn' ? 'fa-solid fa-triangle-exclamation' : null" />
            <div class="flex items-center mx-1">
                <p class="font-bold text-center text-lg">{{ slotProps.message.text }}</p>
            </div>
        </template>
    </Toast>
    <ConfirmPopup>
        <template #message="slotProps">
            <div class="flex flex-col items-center w-full gap-3 p-3">
                <i :class="slotProps.message.icon" class="text-4xl"></i>
                <p class="font-bold">{{ slotProps.message.message }}</p>
            </div>
        </template>
    </ConfirmPopup>
</template>

<script setup>
import { Bars3CenterLeftIcon } from '@heroicons/vue/24/outline'
import {
    ChevronDownIcon, ArrowLeftCircleIcon,
    QuestionMarkCircleIcon,
    XCircleIcon
} from '@heroicons/vue/20/solid'
const { eventListener, handleDataEvents } = useBroadcastNotifications()
const { formatDateTime24h } = useCommonUtilities()
const { toast } = useSweetalert()
import "@/composable/push.min.js"
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import { Menu, MenuButton, MenuItems, MenuItem, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ref, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { useBroadcastNotifications } from '@/composable/useBroadcastNotifications'
import { useSweetalert } from '@/composable/sweetAlert'
import Breadcrumb from 'primevue/breadcrumb';
import Button from '@/Components/Button.vue'
import ConfirmPopup from 'primevue/confirmpopup';
import DolarTRM from "@/Components/DolarTRM.vue"
import DropdownSetting from '@/Components/DropdownSetting.vue'
import UserStatusProgramming from '@/Components/sections/UserStatusProgramming.vue'
import FlyoutNotificationsMenu from '@/Components/FlyoutNotificationsMenu.vue'
import Footer from "@/Components/Footer.vue"
import html2canvas from 'html2canvas'
import MenuSidebar from '@/Components/MenuSidebar.vue'
import RadioButton from 'primevue/radiobutton'
import Toast from 'primevue/toast'

const debug = import.meta.env.VITE_APP_DEBUG
const menu = ref(false)
const sugerencia = ref('')
const sugerenciaVisible = ref(false)
const suggestions = ref([])
const tipoReporte = ref('Sugerencia')

const props = defineProps({
    href: {
        type: Array,
        default: []
    }
})

onMounted(() => {
    loadSuggestions()
    // testBroadcast()
})

const processId = ref(1)
const testBroadcast = () => {
    eventListener(`bulk-data-loading.${processId.value}`, '.bulk.data.loaded')
}

const logout = () => {
    router.post(route('logout'))
}

const createSuggestion = () => {
    if (sugerencia.value.length < 20) {
        toast('La sugerencia debe tener mínimo 20 caracteres', 'error')
    } else {
        html2canvas(document.body)
            .then(canvas => {
                router.post(route('suggestion.store'),
                    {
                        details: sugerencia.value,
                        type: tipoReporte.value,
                        capture: canvas.toDataURL(),
                        urlAddress: document.location.href
                    }, {
                    onSuccess: res => {
                        toast('¡Se ha enviado con éxito, muchas gracias por su reporte!', 'success')
                        sugerenciaVisible.value = false
                        sugerencia.value = null
                        loadSuggestions()
                    },
                    onError: (e) => {
                        console.log(e)
                        toast('Ha ocurrido un error', 'error')
                    }
                }
                )
            })
    }
}

const loadSuggestions = () => {
    axios.get(route('suggestion.create'))
        .then((res) => {
            suggestions.value = res.data[0]
        })
}

const home = ref({
    ruta: 'dashboard',
    // label: 'Dashboard',
    //active: true
})
</script>
