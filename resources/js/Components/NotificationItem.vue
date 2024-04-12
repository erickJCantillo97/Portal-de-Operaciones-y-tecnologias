<template>
    <Link :href="route('requirements.index')">
    <div class="flex items-center justify-center space-x-6 rounded-lg border-b p-1">

        <div class="col-span-2 mx-4 w-full">
            <p :href="item.href" class="font-semibold text-gray-900">
                <!-- Requerimientos Pendientes -->
                {{ item.title }}
            </p>
            <p class="text-gray-600 text-sm">{{ item.message }}</p>
            <div class="flex w-full justify-between">
                <span class="text-xs italic text-gray-500 flex">{{ item.ago }}</span>
                <span class="text-xs italic text-gray-500 flex">{{ item.user }}</span>
            </div>
        </div>
        <div
            class="col-span-1 flex size-8 cursor-pointer items-center justify-center rounded-2xl text-end hover:bg-gray-200">
            <!-- <i class="text-danger fa-solid fa-trash text-sm" aria-hidden="true" /> -->
            <Button v-tooltip.bottom="'Quitar Notificación'" icon="fa fa-xmark" severity="danger" text rounded
                @click="deleteNotification(item.id)" aria-label="Delete" />
        </div>
    </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import Button from 'primevue/button';

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    url: {

    }
})
</script>