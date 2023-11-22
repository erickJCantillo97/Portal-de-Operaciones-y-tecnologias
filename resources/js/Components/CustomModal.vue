<script setup>
import { onMounted, ref } from 'vue';
import Dialog from 'primevue/dialog';
const props =defineProps({
    visible: {
        type: Boolean,
        default: false
    },
})
</script>

<template>
    <Dialog v-model:visible="props.visible" modal :closable="false" class="" :style="{ width: '60rem' }"
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
