<template>
    <div class="min-h-full collapsible-vertical" :class="menu ? 'toggle-sidebar' : ''">
        <div class="bg-GECON hiden"></div>
        <div class="fixed inset-0 bg-[black]/60 z-50" :class="{ hidden: !menu }" @click="menu = !menu"></div>

        <MenuSidebar class="lg:block " :class="{ hidden: !menu }"></MenuSidebar>
        <!-- Main column -->
        <div class="flex flex-col lg:ml-16">
            <!-- Search header -->
            <div class="sticky z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white pl-8 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 justify-between">
                <div>
                    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="menu = !menu">
                        <span class="sr-only">Open sidebar</span>
                        <Bars3CenterLeftIcon class="h-6 w-6" aria-hidden="true" />
                    </button>
                </div>
                <div class="flex ">
                    <div class="flex items-center ">
                        <button type="button" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">View notifications</span>
                            <BellIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                        <DropdownSetting></DropdownSetting>
                        <Menu as="div" class="relative inline-block text-left">
                            <div>
                                <MenuButton class="inline-flex w-full items-center justify-center rounded-md  px-4 py-2 text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full bg-gray-50" :src="$page.props.auth.user.photo" alt="" />
                                    <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">{{$page.props.auth.user.short_name}}</span>
                                    </span>
                                    <ChevronDownIcon class="ml-2 -mr-1 h-5 w-5 text-violet-200 hover:text-violet-100" aria-hidden="true" />
                                </MenuButton>
                            </div>

                            <transition enter-active-class="transition duration-100 ease-out" enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100" leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0">
                                <MenuItems class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="px-1 py-1">
                                        <MenuItem v-slot="{ active }">
                                        <button @click="logout" :class="[
                                            active ? 'bg-primary text-white' : 'text-gray-900',
                                            'group flex w-full items-center rounded-md px-2 py-2 text-sm',]">
                                          <ArrowLeftCircleIcon
                                            :active="active"
                                            class="mr-2 h-5 w-5 text-violet-400"
                                            aria-hidden="true"
                                          />
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
            <main class="flex-1 bg-white border shadow-lg rounded-md m-4 py-2">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { router } from '@inertiajs/vue3'
import { Bars3CenterLeftIcon, Bars4Icon, ClockIcon, HomeIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { BellIcon, ChevronDownIcon, ArrowLeftCircleIcon } from '@heroicons/vue/20/solid'
import MenuSidebar from '@/Components/MenuSidebar.vue';
import DropdownSetting from '@/Components/DropdownSetting.vue';

const menu = ref(false)


const logout = () => {
    router.post(route('logout'));
};

</script>
