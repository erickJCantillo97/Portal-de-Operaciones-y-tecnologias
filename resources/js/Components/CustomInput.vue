<script setup>
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import FileUpload from 'primevue/fileupload';
import { number } from 'echarts';

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
    options: {
        type: Array,
        default: null
    },
    acceptFile: {
        type: String,
        default: '*'
    },
    maxFileSize: {
        type: Number,
        default: 1000000
    }
})


const input = defineModel('input', {
    required: true
})

</script>
<template>
    <div class="flex flex-col">
        <label v-if="label && !floatLabel" :for="id" class="-mb-0.5">{{ label }}</label>
        <span class="p-float-label">
            <FileUpload v-if="type == 'file'" mode="basic" name="demo[]" :multiple="false" :accept="acceptFile" :maxFileSize
                @input="input = $event.target.files[0]" class="w-full h-8" />
            <InputNumber v-else-if="type == 'number'" :id :disabled :placeholder :minFractionDigits :maxFractionDigits
                class="w-full" :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'"
                :useGrouping />

            <Textarea v-else-if="type == 'textarea'" :id :disabled :placeholder class="w-full"
                :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'" />

            <Dropdown v-else-if="type == 'dropdown'" :id :disabled :placeholder :options :class="invalid ? 'p-invalid' : ''"
                v-model="input" :aria-describedby="id + '-help'" class="w-full" :pt="{
                    root: '!h-8',
                    input: '!py-0 !flex !items-center',
                    item: '!py-1 !px-3'
                }" />

            <InputText v-else size="small" :id :disabled :placeholder :class="invalid ? 'p-invalid' : ''" v-model="input"
                :type :aria-describedby="id + '-help'" class="w-full" />
            <label v-if="floatLabel && label" :for="id" class="">{{ label }}</label>
        </span>
        <small :class="invalid ? 'p-error' : ''" v-if="help || invalid">{{ invalid ? errorMessage : help }}</small>
    </div>
</template>
