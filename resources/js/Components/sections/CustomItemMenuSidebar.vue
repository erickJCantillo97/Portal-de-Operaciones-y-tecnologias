<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    item: Object,
    primary: Boolean
})
const open=ref()
</script>

<template>
    <label>
        <input v-model="open" class="peer/showLabel absolute scale-0" type="checkbox" />
        <span
            class="block max-h-12 overflow-hidden rounded-lg transition-all duration-300 peer-checked/showLabel:max-h-full">
            <Link v-if="item.href" :href="route(item.href)" :aria-label="item.name"
                class="relative flex cursor-pointer items-center space-x-5 hover:bg-primary-light px-4 py-3 text-primary">
            <i class="h-5 w-5" :class="[item.icon, 'text-primary']" />
            <span :class="['-mr-1 font-medium', { 'font-semibold': item.children || primary }]">
                {{ item.name }}
            </span>
            </Link>
            <div v-else :aria-label="item.name"
                class="  relative flex justify-between cursor-pointer items-center hover:bg-primary-light px-4 py-3 text-primary">
                <div class="flex space-x-5">
                    <i class="h-5 w-5" :class="[item.icon, 'text-primary']" />
                    <span :class="['-mr-1 font-medium text-left', { 'font-semibold': item.children || primary }]">
                        {{ item.name }}
                    </span>
                </div>
                <span :class="open?'rotate-90':'rotate-0'">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            </div>
            <div v-if="item.children" class="ml-5">
                <CustomItemMenuSidebar v-for="children in item.children" :item="children" />
            </div>
        </span>
    </label>
</template>