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
            <div class=" sm:ml-20 sm:mr-2 mt-3 bg-white shadow-2xl rounded-lg">
                <slot />
            </div>
        </main>

    </div>

    <div class="fixed right-0 bottom-0 -mt-24 w-10">
        <Button severity="primary" @click="toggle">
            <!-- <p>¿Sugerencias?</p> -->
            <QuestionMarkCircleIcon />
        </Button>

        <OverlayPanel class="relative" ref="op">
            <div class="flex items-start space-x-4">
                <div class="min-w-0 flex-1">
                    <form action="#" class="relative">
                        <div
                            class="overflow-hidden rounded-lg shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-indigo-600">
                            <label for="comment" class="sr-only">Añadir comentarios</label>
                            <textarea rows="3" name="comment" id="comment"
                                class="block w-full resize-none border-0 bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                                placeholder="Añade comentarios, sugerencias y reportes de errores" />

                            <!-- Spacer element to match the height of the toolbar -->
                            <div class="py-2" aria-hidden="true">
                                <!-- Matches height of button in toolbar (1px border + 36px content height) -->
                                <div class="py-px">
                                    <div class="h-9" />
                                </div>
                            </div>
                        </div>

                        <div class="absolute inset-x-0 bottom-0 flex justify-between py-2 pl-3 pr-2">
                            <div class="flex items-center space-x-5">
                                <div class="flex items-center">
                                    <button type="button"
                                        class="-m-2.5 flex h-10 w-10 items-center justify-center rounded-full text-gray-400 hover:text-gray-500">
                                        <PaperClipIcon class="h-5 w-5" aria-hidden="true" />
                                        <span class="sr-only">Attach a file</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="submit"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Enviar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </OverlayPanel>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem, Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { router } from '@inertiajs/vue3'
import {
    Bars3CenterLeftIcon, XMarkIcon, FaceFrownIcon,
} from '@heroicons/vue/24/outline'
import { BellIcon, ChevronDownIcon, ArrowLeftCircleIcon, FaceSmileIcon,
    FireIcon,
    HandThumbUpIcon,
    HeartIcon,
    PaperClipIcon,
    QuestionMarkCircleIcon
    } from '@heroicons/vue/20/solid'
import MenuSidebar from '@/Components/MenuSidebar.vue';
import DropdownSetting from '@/Components/DropdownSetting.vue';
import OverlayPanel from 'primevue/overlaypanel';
import Button from '@/Components/Button.vue';
const menu = ref(false)


const logout = () => {
    router.post(route('logout'));
};

//controla la apertura del menu
const op = ref();
const toggle = (event) => {
    op.value.toggle(event);
}

//controla los emojis
const moods = [
    { name: 'Excited', value: 'excited', icon: FireIcon, iconColor: 'text-white', bgColor: 'bg-red-500' },
    { name: 'Loved', value: 'loved', icon: HeartIcon, iconColor: 'text-white', bgColor: 'bg-pink-400' },
    { name: 'Happy', value: 'happy', icon: FaceSmileIcon, iconColor: 'text-white', bgColor: 'bg-green-400' },
    { name: 'Sad', value: 'sad', icon: FaceFrownIcon, iconColor: 'text-white', bgColor: 'bg-yellow-400' },
    { name: 'Thumbsy', value: 'thumbsy', icon: HandThumbUpIcon, iconColor: 'text-white', bgColor: 'bg-blue-500' },
    { name: 'I feel nothing', value: null, icon: XMarkIcon, iconColor: 'text-gray-400', bgColor: 'bg-transparent' },
]
const selected = ref(moods[5])

</script>
