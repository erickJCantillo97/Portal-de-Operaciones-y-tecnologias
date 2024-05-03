<script setup>
import OverlayPanel from 'primevue/overlaypanel';
import { ref } from 'vue';
import CustomInput from './CustomInput.vue';
import InputSwitch from 'primevue/inputswitch';
import Dropdown from 'primevue/dropdown';

defineProps({
    options: Array
})

const overlayConfig = ref()

const toggle = (event) => {
    overlayConfig.value.toggle(event);
}

const optionsData = defineModel('optionsData', {
    required: true,
    type: Object
})

</script>

<template>
    <Button icon="fa-solid fa-gear animate-spin-slow" v-tooltip="{ value: 'Configurar programacion', pt: { root: 'text-center' } }"
        severity="info" raised @click="toggle" />
    <OverlayPanel ref="overlayConfig">
        <div class="gap-2" :class="options.length>4?'grid grid-cols-2 w-96':'flex flex-col'">
            <span v-for="option, key in options"
                :class="(option.visible == undefined ? true : option.visible) ? undefined : 'hidden'">
                <div class="grid grid-cols-5" v-if="(option.type == 'boolean')">
                    <label class="col-span-4" :for="option.field"> {{ option.label }}</label>
                    <InputSwitch :id="option.field" v-model="optionsData[option.field]" />
                </div>
                <div v-if="(option.type == 'options')">
                    <label class="col-span-4" :for="option.field"> {{ option.label }}</label>
                    <Dropdown :id="option.field" class="w-full" v-model="optionsData[option.field]" :options="option.options"/>
                </div>
                <!-- <CustomInput :label="option.label" v-tooltip="option.tooltip" v-model:input="optionsData[option.field]" :type="option.type" :options="option.options"/> -->
            </span>
        </div>
    </OverlayPanel>
</template>