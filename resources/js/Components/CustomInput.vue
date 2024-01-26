<script setup>
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import FileUpload from 'primevue/fileupload';
import MultiSelect from 'primevue/multiselect';
import { ref } from 'vue';

const props = defineProps({
    //general
    type: {
        type: String,
        default: 'text'
    },
    label: {
        type: String,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    },
    icon: {
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
    //tipo number
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
    currency: {
        type: String,
        default: 'COP'
    },
    mode: {
        type: String,
        default: 'decimal'
    },
    suffix: {
        type: String,
        default: null
    },
    prefix: {
        type: String,
        default: null
    },
    //tipo dropdown y multiselect
    options: {
        type: Array,
        default: null
    },
    optionLabel: {
        typ: String,
        default: null
    },
    acceptFile: {
        type: String,
        default: '*'
    },
    maxSelectedLabels: {
        type: Number,
        default: 3
    },
    //tipo file
    maxFileSize: {
        type: Number,
        default: 1000000
    }
})

const countries = ref()
if (props.type == 'country') {
    axios.get('https://restcountries.com/v3.1/all?fields=flags,translations,name').then(
        (res) => {
            countries.value = res.data.map(country => {
                country.translations.spa.common = country.translations.spa.common.toUpperCase()
                return country
            })
        }
    )
}

const input = defineModel('input', {
    required: true
})

</script>
<template>
    <div class="flex flex-col">
        <label v-if="label && !floatLabel" :for="id" class="-mb-0.5">{{ label }}</label>
        <span :class="!(label && !floatLabel) ? 'p-float-label' : ''">
            <FileUpload v-if="type == 'file'" mode="basic" name="demo[]" :multiple="false" :accept="acceptFile" :maxFileSize
                @input="input = $event.target.files[0]" class="w-full h-8" customUpload />

            <InputNumber v-else-if="type == 'number'" :id :disabled :placeholder :minFractionDigits :maxFractionDigits
                class="w-full" :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'"
                :useGrouping="mode == 'currency' ? '' : useGrouping" :currency="currency" :mode="mode" :suffix :prefix />

            <Textarea v-else-if="type == 'textarea'" :id :disabled :placeholder class="w-full"
                :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'" />

            <Dropdown v-else-if="type == 'dropdown'" :id :disabled :placeholder :options :optionLabel :loading
                :filter="optionLabel ? true : false" :class="invalid ? 'p-invalid' : ''" v-model="input"
                :aria-describedby="id + '-help'" class="w-full" :pt="{
                    root: '!h-8 ',
                    input: '!py-0 !flex !items-center !text-sm !font-normal',
                    item: '!py-1 !px-3 !text-sm !font-normal',
                    filterInput: '!h-8'
                }" />
            <Dropdown v-else-if="type == 'country'" :id :disabled :placeholder filter resetFilterOnHide :options="countries"
                :loading :class="invalid ? 'p-invalid' : ''" v-model="input" optionLabel="translations.spa.common"
                :aria-describedby="id + '-help'" class="w-full" :pt="{
                    root: '!h-8 ',
                    input: '!py-0 !flex !items-center !text-sm !font-normal',
                    item: '!py-1 !px-3 !text-sm !font-normal',
                    filterInput: '!h-8'
                }">
                <template #value="slotProps">
                    <div v-if="slotProps.value" class="flex space-x-1">
                        <img :src="slotProps.value.flags.svg" width="30" :alt="slotProps.value">
                        <p class="">{{ slotProps.value.translations.spa.common }}</p>
                    </div>
                    <span v-else>
                        {{ slotProps.placeholder }}
                    </span>
                </template>
                <template #option="slotProps">
                    <div class="flex space-x-1">
                        <img :src="slotProps.option.flags.svg" width="30" :alt="slotProps.option.translations.spa.common">
                        <p>{{ slotProps.option.translations.spa.common }}</p>
                    </div>
                </template>
            </Dropdown>
            <MultiSelect v-else-if="type == 'multiselect'" :id display="chip" v-model="input" :options :optionLabel :loading
                :maxSelectedLabels :placeholder :filter="optionLabel ? true : false" :class="invalid ? 'p-invalid' : ''"
                class="w-full" :aria-describedby="id + '-help'" :pt="{
                    root: '!h-8',
                    label: '!py-0.5 !flex !h-full !items-center !text-sm !font-normal',
                    token: '!py-0 !px-1',
                    tokenLabel: '!text-sm',
                    item: '!py-1 !px-3 !text-sm !font-normal',
                    filterInput: '!h-8',
                    header: '!h-min !py-0.5'
                }" />
            <span v-else :class="(loading || icon) ? 'p-input-icon-left' : ''" class="w-full">
                <i v-if="(loading || icon)" :class="loading ? 'pi pi-spin pi-spinner' : icon" />
                <InputText size="small" :id :disabled :placeholder :class="invalid ? 'p-invalid' : ''" v-model="input" :type
                    :aria-describedby="id + '-help'" class="w-full" />
                <label v-if="floatLabel && label" :for="id" class="">{{ label }}</label>
            </span>
        </span>
        <small :class="invalid ? 'p-error' : ''" v-if="help || invalid">{{ invalid ? errorMessage : help }}</small>
    </div>
</template>
