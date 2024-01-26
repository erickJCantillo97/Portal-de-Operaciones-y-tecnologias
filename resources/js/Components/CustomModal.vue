<script setup>
import { defineModel } from 'vue';
import Dialog from 'primevue/dialog';
const props = defineProps({
    width: {
        type: String,
        default: '60rem'
    },
    footer: {
        type: Boolean,
        default: true
    },
    maximizable: {
        type: Boolean,
        default: false
    },
    closable: {
        type: Boolean,
        default: true
    },
    closeOnEscape: {
        type: Boolean,
        default: true
    }
})
const visible = defineModel('visible')
</script>

<template>
    <Dialog v-model:visible="visible" :maximizable="maximizable" modal :closable :closeOnEscape
        :style="{ width: props.width }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :pt="{
            header: { class: '!bg-primary !h-10' },
            closeButtonIcon: { class: 'text-white' },
            content: { class: '!pb-0 !pt-2' },
            footer: { class: '!p-2 !h-min !items-end !flex !justify-end' }
        }
            ">
        <template #header>
            <div class="flex items-center space-x-2 text-white">
                <slot name="icon" />
                <slot name="titulo" />
            </div>
        </template>
        <template #default>
            <div class="h-full ">
                <slot name="body" />
            </div>
        </template>
        <template #footer v-if="footer">
            <slot name="footer" />
        </template>
        <template #maximizeicon="{ maximized }">
            <i :class="maximized ? 'fa-compress' : 'fa-expand'" class="text-white fa-solid"></i>
        </template>
    </Dialog>
</template>
