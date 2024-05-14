<script setup>
import OverlayPanel from 'primevue/overlaypanel';
import { ref } from 'vue';
import InputSwitch from 'primevue/inputswitch';
import Dropdown from 'primevue/dropdown';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    options: {
        type: Array,
        default: []
    }
})
const optionsGen = ref([
    {
        field: 'generalfontsize',
        label: 'Tamaño de letra',
        type: 'options',
        options: [
            { text: 'Pequeño', value: '11px' },
            { text: 'Normal (Recomendado)', value: '12px' },
            { text: 'Grande', value: '13px' },
            { text: 'Muy grande', value: '14px' },
            { text: 'Enorme', value: '15px' },
            { text: 'Muy Enorme', value: '17px' },
        ],
        default: '12px'
    },
    // {
    //     field: 'generalfontweight',
    //     label: 'Negrita',
    //     type: 'boolean',
    //     default: false
    // }
])

const overlayConfig = ref()

const toggle = (event) => {
    overlayConfig.value.toggle(event);
}

const optionsData = defineModel('optionsData', {
    default: {},
    type: Object
})

usePage().props.auth.user.configurations.forEach(element => {
    Object.assign(optionsData.value, element)
});

optionsGen.value.forEach((option) => {
    if (optionsData.value[option.field] == undefined) {
        optionsData.value[option.field] = { type: option.type, data: option.default }
    }
})

props.options.forEach((option) => {
    if (optionsData.value[option.field] == undefined) {
        optionsData.value[option.field] = { type: option.type, data: option.default }
    }
})
async function saveConfig(key, data, type) {
    let aux = {}
    aux[key] = JSON.stringify({ 'type': type, 'data': data })
    if (key == 'generalfontsize' || key == 'generalfontweight') {
        changeFontSize(data)
    }
    await axios.post(route('userconfiguration.store'), aux)
}

function changeFontSize(size) {
    var element = document.getElementsByClassName('sizegeneralfont');
    element[0].style.fontSize = size;
    // element[0].style.fontWeight = size ? '900' : '500';
}

changeFontSize(optionsData.value.generalfontsize?.data ?? '12px')
</script>

<template>
    <Button icon="fa-solid fa-gear animate-spin-slow"
        v-tooltip.bottom="{ value: 'Configurar pagina', pt: { root: 'text-center' } }" severity="info" text
        @click="toggle" />
    <OverlayPanel ref="overlayConfig">
        <p class="font-bold text-lg mb-5 text-center">Configuraciones visuales generales</p>
        <div class="">
            <span v-for="option, key in optionsGen"
                :class="(option.visible == undefined ? true : option.visible) ? undefined : 'hidden'">
                <div v-if="(option.type == 'options')" class="grid grid-cols-2 items-center w-full">
                    <label class="w-full mb-0.5" :for="option.field"> {{ option.label }}</label>
                    <Dropdown :id="option.field" option-value="value" option-label="text" class="w-full"
                        v-model="optionsData[option.field].data" :options="option.options"
                        @change="saveConfig(option.field, optionsData[option.field].data, option.type)" />
                </div>
                <div class="grid grid-cols-5" v-if="(option.type == 'boolean')">
                    <label class="col-span-4" :for="option.field"> {{ option.label }}</label>
                    <InputSwitch :id="option.field" v-model="optionsData[option.field].data"
                        @change="saveConfig(option.field, optionsData[option.field].data, option.type)" />
                </div>
            </span>

        </div>
        <p v-if="options.length > 0" class="font-bold text-lg my-5 text-center">Configuracion de la pagina</p>
        <div class="gap-2" :class="options.length > 4 ? 'grid grid-cols-2 w-96' : 'flex flex-col'">
            <span v-for="option, key in options"
                :class="(option.visible == undefined ? true : option.visible) ? undefined : 'hidden'">
                <div class="grid grid-cols-5" v-if="(option.type == 'boolean')">
                    <label class="col-span-4" :for="option.field"> {{ option.label }}</label>
                    <InputSwitch :id="option.field" v-model="optionsData[option.field].data"
                        @change="saveConfig(option.field, optionsData[option.field].data, option.type)" />
                </div>
                <div v-if="(option.type == 'options')">
                    <label class="col-span-4" :for="option.field"> {{ option.label }}</label>
                    <Dropdown :id="option.field" class="w-full" v-model="optionsData[option.field].data"
                        :options="option.options"
                        @change="saveConfig(option.field, optionsData[option.field].data, option.type)" />
                </div>
                <!-- <CustomInput :label="option.label" v-tooltip="option.tooltip" v-model:input="optionsData[option.field]" :type="option.type" :options="option.options"/> -->
            </span>
        </div>
    </OverlayPanel>
</template>