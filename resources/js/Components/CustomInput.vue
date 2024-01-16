<script setup>
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    type: {
        type: String,
        default: 'text'
    },
    label: {
        type: String,
        default: null
    },
    floatLabel: {
        type: Boolean,
        default: false
    },
    invalid: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    id: {
        type: String,
        default: ''
    },
    help: {
        type: String,
        default: null
    },
    errorMessage: {
        type: String,
        default: null
    },
    placeholder: {
        type: String,
        default: null
    },
    useGrouping: {
        type: Boolean,
        default: false
    },
    minFractionDigits: {
        type: Number,
        default: 0
    },
    maxFractionDigits: {
        type: Number,
        default: null
    },
})


const input = defineModel('input', {
    required: true
})

</script>
<template>
    <div class="flex flex-col">
        <label v-if="label && !floatLabel" :for="id" class="-mb-0.5">{{ label }}</label>
        <span class="p-float-label">
            <InputNumber v-if="type == 'number'" :id :disabled :placeholder :minFractionDigits :maxFractionDigits
                :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'" :useGrouping />
            <InputText v-else size="small" :id :disabled :placeholder :class="invalid ? 'p-invalid' : ''" v-model="input"
                :type :aria-describedby="id + '-help'" />
            <label v-if="floatLabel && label" :for="id" class="">{{ label }}</label>
        </span>
        <small :class="invalid ? 'p-error' : ''" v-if="help || invalid">{{ invalid ? errorMessage : help }}</small>
    </div>
</template>
