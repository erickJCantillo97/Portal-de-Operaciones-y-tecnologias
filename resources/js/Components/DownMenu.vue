<template>
    <Menu as="div" class="relative inline-block px-1 text-left">
        <div>
            <MenuButton class="group w-full rounded-md p-2 text-left text-sm font-medium text-gray-700focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-100">
                <span class="flex w-full items-center justify-between">
                    <slot />
                <ChevronUpDownIcon class="h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                </span>
            </MenuButton>
        </div>
        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <MenuItems class="absolute left-0 right-0 z-10 mx-3 mt-1 origin-top divide-y divide-gray-200 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div class="py-1">
                    <MenuItem v-slot="{ active }" v-for="option in options">
                    <a :href="option.href" :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">{{option.name}}</a>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup>
    import {Menu, MenuButton, MenuItem,MenuItems } from '@headlessui/vue'
    import { Bars3CenterLeftIcon, Bars4Icon, ClockIcon, HomeIcon, XMarkIcon } from '@heroicons/vue/24/outline'
    import { ChevronRightIcon, ChevronUpDownIcon, EllipsisVerticalIcon, MagnifyingGlassIcon,BellIcon,ChevronDownIcon } from '@heroicons/vue/20/solid'

    const props = defineProps({
        options: {
            type: Array,
            default: [],
        }
    })


</script>

