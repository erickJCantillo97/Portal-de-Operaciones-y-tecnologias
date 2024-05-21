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
import { useCommonUtilities } from '@/composable/useCommonUtilities';
import InputSwitch from 'primevue/inputswitch';
const { byteSizeFormatter } = useCommonUtilities()

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
        type: String,
        default: null
    },
    emptyMessage: {
        type: String,
        default: null
    },
    optionValue: {
        type: String,
        default: null
    },
    showClear: {
        type: Boolean,
        default: true
    },
    acceptFile: {
        type: String,
        default: null
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
    previewImage: {
        type: Boolean,
        default: false
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
    //calendar
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

const onRemoveTemplatingFile = (removeFileCallback, index) => {
    if (props.multiple) {
        removeFileCallback(index);
    }
    else {
        input.value = null
        removeFileCallback(index);
    }
};

defineEmits(['valueChange'])

</script>
<template>
    <div class="flex flex-col">
        <div v-if="type == 'radiobutton'" class="w-full">
            <label v-if="label" :for="id" class="mb-0.5 font-bold">{{ label }}</label>
            <div class="flex" :class="options.length > 2 ? 'justify-between' : 'space-x-4'">
                <div v-for="option in options" :key="option.key" class="flex items-center">
                    <RadioButton v-model="input" :inputId="option.key" name="dynamic" :value="option.name" />
                    <label :for="option.key" class="ml-2 -mb-0.5">{{ option.name }}</label>
                </div>
            </div>
        </div>
        <span v-else>
            <label v-if="label && !floatLabel" :for="id" class="mb-0.5 font-bold">{{ label }}</label>
            <span :class="!(label && !floatLabel) ? 'p-float-label' : undefined">
                <span v-if="type == 'file'">
                    <span v-if="mode == 'advanced'">
                        <FileUpload mode="advanced" :multiple :accept="acceptFile" :maxFileSize
                            @remove="input = multiple ? $event.files : $event.files[0]"
                            @select="input = multiple ? $event.files : $event.files[0]" class="" customUpload>
                            <template #empty>
                                <div class="text-primary flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
                                    <p class="font-bold text-center">Arrastra aqui</p>
                                </div>
                            </template>
                            <template #header="{ chooseCallback, clearCallback, files }">
                                <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                                    <div class="flex gap-2">
                                        <Button
                                            @click="{ !multiple ? input = null : undefined; !multiple ? clearCallback() : undefined; chooseCallback() }"
                                            icon="fa-solid fa-file-import" text label="Seleccionar"></Button>
                                        <Button @click="clearCallback(); input = null" icon="fa-solid fa-circle-xmark"
                                            text severity="danger" label="Quitar todos"
                                            :disabled="!files || files.length === 0"></Button>
                                    </div>
                                </div>
                            </template>
                            <template #content="{ files, removeFileCallback }">
                                <div v-if="files.length > 0">
                                    <div class="grid w-full p-0 sm:p-1 gap-2">
                                        <div v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                            class="flex w-full border rounded-md p-2 justify-between items-center hover:bg-gray-100">
                                            <div class="flex space-x-2 cursor-default" v-tooltip.left="file.name">
                                                <span class="w-24 h-14 flex items-center justify-center">
                                                    <img v-if="file.type.includes('image')" class="w-full p-1"
                                                        :alt="file.name" :src="file.objectURL" />
                                                    <i v-else-if="file.type.includes('pdf')"
                                                        class="fa-solid fa-file-pdf text-6xl text-red-600" />
                                                    <i v-else-if="file.type == 'text/plain'"
                                                        class="fa-regular fa-file-lines text-6xl  text-gray-600" />
                                                    <i v-else-if="file.type.includes('spreadsheet') || file.type.includes('excel')"
                                                        class="fa-solid fa-file-excel text-6xl  text-green-600" />
                                                    <i v-else-if="file.type.includes('word')"
                                                        class="fa-solid fa-file-word text-6xl  text-primary" />
                                                    <i v-else class="fa-solid fa-file text-6xl  text-gray-600" />
                                                </span>
                                                <div class="w-full">
                                                    <p class="font-semibold">{{ file.name }}
                                                    </p>
                                                    <p class="text-xs">{{ byteSizeFormatter(file.size) }}</p>
                                                </div>
                                            </div>
                                            <Button icon="fa-solid fa-trash-can" v-tooltip.rigth="'Quitar'"
                                                @click="onRemoveTemplatingFile(removeFileCallback, index)" text
                                                severity="danger" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </FileUpload>
                    </span>
                    <span v-else>
                        <FileUpload mode="basic" :multiple :accept="acceptFile" :maxFileSize
                            @input="input = $event.target.files[0]" class="w-full h-8" customUpload />
                    </span>
                </span>
                <InputNumber v-else-if="type == 'number'" :max :min :id :disabled :placeholder :minFractionDigits
                    :maxFractionDigits class="w-full" :class="invalid ? 'p-invalid' : ''" v-model="input"
                    :aria-describedby="id + '-help'" :required :useGrouping="mode == 'currency' ? '' : useGrouping"
                    :currency="currency" :mode="mode" :suffix :prefix :pt="{ input: '!w-full !text-sm' }" />
                <Textarea v-else-if="type == 'textarea'" :id :disabled :rows="rowsTextarea" class="w-full" :required
                    :placeholder :class="invalid ? 'p-invalid' : ''" v-model="input" :aria-describedby="id + '-help'" />
                <Dropdown v-else-if="type == 'dropdown'" :optionValue :id :disabled :placeholder :options :optionLabel
                    :emptyMessage :loading @change="$emit('valueChange', $event)" showClear
                    :filter="optionLabel ? true : false" :class="invalid ? 'p-invalid' : ''" v-model="input"
                    :aria-describedby="id + '-help'" class="w-full" :pt="{
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
                    :optionLabel :loading :maxSelectedLabels :placeholder :disabled :filter="optionLabel ? true : false"
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
                <div v-else-if="type == 'tooglebutton'" class="">
                    <ToggleButton v-model="input" :onLabel :offLabel :pt="{ root: '!h-8' }" />
                </div>
                <div v-else-if="type == 'boolean'" class="">
                    <InputSwitch v-model="input" />
                </div>
                <span v-else-if="type == 'datetime'">
                    <Calendar :id v-model="input" :minDate :maxDate :placeholder showTime :required hourFormat="24"
                        showIcon :stepMinute dateFormat="dd/mm/yy" @date-select="$emit('valueChange', $event)"
                        :disabledDays :pt="{
            root: '!w-full',
            input: '!h-8'
        }" />
                </span>
                <span v-else-if="type == 'date'">
                    <Calendar :id v-model="input" :minDate :maxDate :placeholder :required showIcon :disabledDays
                        :selectionMode @date-select="$emit('valueChange', $event)" dateFormat="dd/mm/yy" :pt="{
            root: '!w-full',
            input: '!h-8 !text-center '
        }" />
                </span>
                <span v-else-if="type == 'time'">
                    <Calendar :id v-model="input" :min-date :max-date timeOnly hourFormat="24" :placeholder :required
                        showIcon dateFormat="dd/mm/yy" :stepMinute @date-select="$emit('valueChange', $event)" :pt="{
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
                        :aria-describedby="id + '-help'" class="w-full" :pt="{
            input: '!text-sm'
        }" />
                </span>
                <label v-if="floatLabel && label" :for="id" class="">{{ label }}</label>
            </span>
        </span>
        <small :class="invalid ? 'p-error' : ''" v-if="help || invalid">{{ invalid ? errorMessage : help }}</small>
    </div>
</template>
