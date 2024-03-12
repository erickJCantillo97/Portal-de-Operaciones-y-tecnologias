<script setup>
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'
import { Link } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

const props = defineProps({
    items: Array,
    header:{
        type: Boolean,
        default:false
    }
});

</script>

<template>
    <Menu as="div" class="inline-block text-left z-50">
        <div>
            <MenuButton
                class="flex items-center rounded-full bg-gray-100 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-100">
                <span class="sr-only">Opciones</span>
                <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
            </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems
                class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-gray-100 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div v-if="header" class="px-4 py-3">
                    <slot name="header"/>
                </div>
                <div class="py-1">
                    <MenuItem v-for="item in items" v-slot="{ active }">
                    <Link :href="route(item.url.name, item.url.parametter)"
                        :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">
                    <i :class="[active ? item.icon + ' fa-beat' : item.icon]" class="mr-2"></i>
                    {{ item.label }}
                    </Link>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
