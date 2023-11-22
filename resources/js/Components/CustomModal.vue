<script setup>
import { ref } from 'vue';
import Dialog from 'primevue/dialog';
const visible=ref(false)
</script>

<template>
    <button @click="visible = true">
        <slot name="button"></slot>
    </button>
    <Dialog v-model:visible="visible" modal closeOnEscape class="" :style="{ width: '60rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :pt="{
                header: { class: '!bg-primary' },
                closeButtonIcon: { class: 'text-white' }

            }">
            <template #header>
                <div class="flex items-center space-x-2">
                    <slot name="icon"/>
                    <slot name="titulo"/>
                </div>
            </template>
            <slot name="body"/>
            <template #footer>
                <slot name="footer"/>
            </template>

    </Dialog>
</template>
