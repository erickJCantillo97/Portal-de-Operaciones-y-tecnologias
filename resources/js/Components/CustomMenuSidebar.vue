<script setup>
import { ref } from 'vue';
import ApplicationLogo from './ApplicationLogo.vue';
import CustomItemMenuSidebar from './sections/CustomItemMenuSidebar.vue';

defineProps({
    items: {
        type: Array,
        required: true,
        default: []
    }
})
const menu = ref(false)
</script>
<template>
    <Button icon="fa-solid fa-bars" class="text-2xl top-7 fixed lg:hidden" text @click="menu = !menu" />
    <div class="fixed inset-0 bg-[black]/60 z-10" :class="{ hidden: !menu }" @click="menu = !menu"></div>
    <div class="min-h-screen bg-gray-100 fixed z-20 sm:block" :class="{ hidden: !menu }">
        <div :class="menu?'w-80':'hover:w-80'"
            class="sidebar min-h-screen w-[3.35rem] overflow-hidden border-r bg-white hover:shadow-lg">
            <div class="flex h-screen flex-col justify-between pb-16">
                <div class="flex h-12 items-center justify-center w-full shadow-md">
                    <ApplicationLogo class="w-10 mb-2" :letras="true" />
                </div>
                <ul class="mt-2 space-y-2 tracking-wide h-full overflow-hidden hover:overflow-y-scroll">
                    <li class="min-w-max px-1">
                        <span v-for="item in items">
                            <CustomItemMenuSidebar :item :primary="true"></CustomItemMenuSidebar>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<style></style>