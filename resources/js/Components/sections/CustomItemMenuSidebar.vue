<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    item: Object,
    primary: Boolean
})
const open = ref(false)
const load = ref(false)
</script>

<template>
    <label>
        <input v-model="open" class="absolute scale-0" type="checkbox" />
        <span :class="open ? 'max-h-[100vh] flex flex-col' : 'max-h-12 flex flex-col h-12'"
            class="overflow-hidden rounded-lg transition-all duration-1000 ease-in-out">
            <Link v-if="item.href" :href="route(item.href)" :aria-label="item.name" @click="load = true"
                class="flex justify-between cursor-pointer items-center hover:bg-primary-light px-4 py-3 text-primary">
            <div class="flex space-x-5 items-center">
                <i class="h-5 w-5" :class="[item.icon, 'text-primary']" />
                <span :class="['-mr-1 font-medium', { 'font-semibold': item.children || primary }]">
                    {{ item.name }}
                </span>
            </div>
            <i v-tooltip="'En desarrollo'" v-if="item.dev" class="w-4 fa-solid fa-circle-exclamation text-danger"></i>
            <span :class="load ? 'animate-spin' : 'hidden'">
                <i class="fa-solid fa-spinner"></i>
            </span>
            </Link>
            <div v-else :aria-label="item.name"
                class="flex justify-between cursor-pointer items-center hover:bg-primary-light px-4 py-3 text-primary">
                <div class="flex space-x-5 items-center">
                    <i class="h-5 w-5" :class="[item.icon, 'text-primary']" />
                    <span :class="['-mr-1 font-medium text-left', { 'font-semibold': item.children || primary }]">
                        {{ item.name }}
                    </span>
                </div>
                <span class="ease-in transition-all duration-200" :class="open ? 'rotate-90' : 'rotate-0'">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            </div>
            <div v-if="item.children" class="ml-5">
                <CustomItemMenuSidebar v-for="children in item.children" :item="children" />
            </div>
        </span>
    </label>
</template>