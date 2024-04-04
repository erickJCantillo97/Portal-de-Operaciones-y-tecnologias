<script setup>
import { ref } from 'vue';
import Calendar from 'primevue/calendar';
import Checkbox from 'primevue/checkbox';
import Dropdown from 'primevue/dropdown';
import FileUpload from 'primevue/fileupload';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import RadioButton from 'primevue/radiobutton';
import Textarea from 'primevue/textarea';
import ToggleButton from 'primevue/togglebutton';

const props = defineProps({
    //general
    autoResize: {
        type: Boolean,
        default: false
    },
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
    filterPlaceholder: {
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
        default: null
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
    max: {
        type: Number,
        default: null
    },
    min: {
        type: Number,
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
    optionValue: {
        typ: String,
        default: null
    },
    showClear: {
        typ: Boolean,
        default: true
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
        default: 100000000
    },
    multiple: {
        type: Boolean,
        default: false
    },
    selectionMode: {
        type: String,
        default: 'single'
    },
    onLabel: {
        type: String,
        default: 'Si'
    },
    offLabel: {
        type: String,
        default: 'No'
    },
    required: {
        type: Boolean,
        default: false
    },
    minDate: {
        type: Date,
        default: null
    },
    maxDate: {
        type: Date,
        default: null
    },
    rowsTextarea: {
        type: Number,
        default: 4
    },
    //calendar
    stepMinute: {
        type: Number,
        default: 30
    },
    disabledDays: {
        type: Array,
        default: null
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

defineEmits(['valueChange'])

</script>
<template>
    <div class="flex flex-col">
        <div v-if="type == 'radiobutton'" class="w-full flex h-8"
            :class="options.length > 2 ? 'justify-between' : 'space-x-4'">
            <div v-for="option in options" :key="option.key" class="flex items-center">
                <RadioButton v-model="input" :inputId="option.key" name="dynamic" :value="option.name" />
                <label :for="option.key" class="ml-2 -mb-0.5">{{ option.name }}</label>
            </div>
        </div>
        <span v-else>
            <label v-if="label && !floatLabel" :for="id" class="mb-0.5 font-bold">{{ label }}</label>
            <span :class="!(label && !floatLabel) ? 'p-float-label' : undefined">
                <FileUpload v-if="type == 'file'" mode="basic" :multiple :accept="acceptFile" :maxFileSize
                    @input="input = $event.target.files[0]" class="w-full h-8" customUpload />
                <InputNumber v-else-if="type == 'number'" :max :min :id :disabled :placeholder :minFractionDigits
                    :maxFractionDigits class="w-full" :class="invalid ? 'p-invalid' : ''" v-model="input"
                    :aria-describedby="id + '-help'" :required :useGrouping="mode == 'currency' ? '' : useGrouping"
                    :currency="currency" :mode="mode" :suffix :prefix :pt="{ input: '!w-full' }" />
                <Textarea v-else-if="type == 'textarea'" :id :disabled :rows="rowsTextarea" class="w-full" :required
                    :placeholder :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'" />
                <Dropdown v-else-if="type == 'dropdown'" :optionValue :id :disabled :placeholder :options :optionLabel
                    :loading @change="$emit('change', $event)" showClear :filter="optionLabel ? true : false"
                    :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'" class="w-full"
                    :pt="{
            root: '!h-8',
            input: '!py-0 !flex !items-center !text-sm !font-normal',
            item: '!py-1 !px-3 !text-sm !font-normal',
            filterInput: '!h-8'
        }" />
                <Dropdown v-else-if="type == 'country'" :optionValue :id :disabled :placeholder :filterPlaceholder
                    filter resetFilterOnHide :options="countries" :loading :class="invalid ? 'p-invalid' : ''"
                    v-model="input" optionLabel="translations.spa.common" :aria-describedby="id + '-help'"
                    class="w-full" :pt="{
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
                            <img :src="slotProps.option.flags.svg" width="30"
                                :alt="slotProps.option.translations.spa.common">
                            <p>{{ slotProps.option.translations.spa.common }}</p>
                        </div>
                    </template>
                </Dropdown>
                <MultiSelect v-else-if="type == 'multiselect'" :optionValue :id display="chip" v-model="input" :options
                    :optionLabel :loading :maxSelectedLabels :placeholder :filter="optionLabel ? true : false"
                    :class="invalid ? 'p-invalid' : ''" class="w-full" :aria-describedby="id + '-help'" :pt="{
            root: '!h-8',
            label: '!py-0.5 !flex !h-full !items-center !text-sm !font-normal',
            token: '!py-0 !px-1',
            tokenLabel: '!text-sm',
            item: '!py-1 !px-3 !text-sm !font-normal',
            filterInput: '!h-8',
            header: '!h-min !py-0.5'
        }" />
                <span v-else-if="type == 'groupcheckbox'">
                    <div class="card flex flex-wrap justify-content-center gap-3">
                        <div class="flex h-8 space-x-1 items-center" v-for="option in options" :key="option.key">
                            <Checkbox v-model="input" :inputId="option.key" name="category" :value="option.name" />
                            <label :for="option.key">{{ option.name }}</label>
                        </div>
                    </div>
                </span>
                <ToggleButton v-else-if="type == 'tooglebutton'" v-model="input" :onLabel :offLabel
                    :pt="{ root: '!h-8' }" />

                <span v-else-if="type == 'datetime'">
                    <Calendar :id v-model="input" :minDate :maxDate showTime :required hourFormat="24" showIcon
                        :stepMinute dateFormat="dd/mm/yy" @date-select="$emit('valueChange', $event)" :disabledDays :pt="{
            root: '!w-full',
            input: '!h-8'
        }" />
                </span>
                <span v-else-if="type == 'date'">
                    <Calendar :id v-model="input" :minDate :maxDate :required showIcon :disabledDays :selectionMode
                        @date-select="$emit('valueChange', $event)" dateFormat="dd/mm/yy" :pt="{
            root: '!w-full',
            input: '!h-8 text-center'
        }" />
                </span>
                <span v-else-if="type == 'time'">
                    <Calendar :id v-model="input" timeOnly hourFormat="24" :required showIcon dateFormat="dd/mm/yy"
                        :stepMinute @date-select="$emit('valueChange', $event)" :pt="{
            root: '!w-full',
            input: '!h-8'
        }" />
                </span>
                <IconField v-else-if="loading || icon" iconPosition="left" class="w-full">
                    <InputIcon :class="loading ? 'fa-solid fa-spinner animate-spin' : icon" />
                    <InputText size="small" :id :disabled :placeholder :class="invalid ? 'p-invalid' : ''"
                        v-model="input" :type :required :aria-describedby="id + '-help'" class="w-full" />
                </IconField>
                <span v-else class="w-full">
                    <InputText size="small" :id :disabled :placeholder :class="invalid ? 'p-invalid' : ''"
                        @change="$emit('valueChange', $event)" v-model="input" :type :required
                        :aria-describedby="id + '-help'" class="w-full" />
                </span>
                <label v-if="floatLabel && label" :for="id" class="">{{ label }}</label>
            </span>
        </span>
        <small :class="invalid ? 'p-error' : ''" v-if="help || invalid">{{ invalid ? errorMessage : help }}</small>
    </div>
</template>
