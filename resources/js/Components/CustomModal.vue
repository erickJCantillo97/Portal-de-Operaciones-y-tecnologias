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
    },
    maximizable: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <Dialog v-model:visible="props.visible" :maximizable="maximizable" modal :closable="false" class=""
        :style="{ width: props.width }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :pt="{
            header: { class: '!bg-primary' },
            closeButtonIcon: { class: 'text-white' },

        }
            ">
        <template #header>
            <div class="flex items-center space-x-2">
                <slot name="icon" />
                <slot name="titulo" />
            </div>
        </template>
        <template #default>
            <div class="pt-2 h-92">
                <slot name="body" />
            </div>
        </template>
        <template #footer>
            <slot name="footer" />
        </template>
        <template #maximizeicon="{ maximized }">
            <i :class="maximized ? 'fa-compress' : 'fa-expand'" class="text-white fa-solid"></i>
        </template>
    </Dialog>
</template>
