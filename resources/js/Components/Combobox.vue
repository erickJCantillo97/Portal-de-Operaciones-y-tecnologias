<script setup>
import { computed, ref } from 'vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue'


const emit = defineEmits(['update:select']);

const query = ref('')
const props = defineProps({
    modelValue: Object,
    options: Array,
    label: String,
    placeholder: String,
    enabled: {
        type: Boolean,
        default: true
    }
})
const filtered = computed(() =>
    query.value === ''
        ? props.options
        : props.options.filter((person) => {
            return person.name.toLowerCase().includes(query.value.toLowerCase())
        })
)
</script>

<template>
    <Combobox as="div" :modelValue="modelValue" @update:modelValue="value => emit('update:modelValue', value)">
        <ComboboxLabel class="block text-sm font-medium leading-6 text-gray-900">
            {{ label }}
            <sup class="text-danger">*</sup>
        </ComboboxLabel>
        <div class="relative">
            <ComboboxInput
                class="w-full rounded-md border-0 bg-white h-10 placeholder:italic py-2 -mt-1.5 pl-3 pr-12 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                @change="query = $event.target.value" :display-value="(customer) => customer?.name"
                :placeholder="placeholder" :disabled="!enabled" />
            <ComboboxButton :disabled="!enabled"
                class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </ComboboxButton>

            <ComboboxOptions v-if="filtered.length > 0"
                class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <ComboboxOption v-for="item in filtered" :key="item.name" :value="item" as="template"
                    v-slot="{ active, selected }">
                    <li
                        :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                        <div class="flex">
                            <span :class="['truncate', selected && 'font-semibold']">
                                {{ item.name }}
                            </span>
                        </div>

                        <span v-if="selected"
                            :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </div>
    </Combobox>
</template>

