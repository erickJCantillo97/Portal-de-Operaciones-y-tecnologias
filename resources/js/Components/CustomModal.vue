<script setup>
import { onMounted, ref } from 'vue';
import Dialog from 'primevue/dialog';
const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    width: {
        type: String,
        default: '60rem'
    }
})
</script>

<template>
    <Dialog v-model:visible="props.visible" modal :closable="false" class="" :style="{ width: props.width }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :pt="{
            header: { class: '!bg-primary' },
            closeButtonIcon: { class: 'text-white' }
        }">
        <template #header>
            <div class="flex items-center space-x-2">
                <slot name="icon" />
                <slot name="titulo" />
            </div>
        </template>
        <div class="pt-3">
            <slot name="body" />
        </div>
        <template #footer>
            <slot name="footer" />
        </template>
    </Dialog>
</template>
