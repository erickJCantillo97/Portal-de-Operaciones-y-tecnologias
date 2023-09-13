<script setup>
import { onMounted, ref } from 'vue';
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
defineProps({
    modelValue: String,
    placeholder: String,
    label: {
        type: String,
        required: true
    },
    error: {
        type: String,
        default: null
    },
    type: {
        type: String,
        default: 'text'
    },
    enabled: {
        type: Boolean,
        default: true
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

</script>

<template>
    <div>
        <label for="password" class="block capitalize text-sm font-mediumtext-gray-900">
            {{ label }}
            <sup class="text-danger">*</sup>
        </label>
        <div class="relative mt-2 rounded-md shadow-sm">
            <input :type="type" autocomplete="current-password"
                :class="[error != null ? 'text-red-900 ring-1 ring-inset ring-red-300 focus:ring-2 focus:ring-inset focus:ring-red-500' : '']"
                required="" :placeholder="placeholder" :disabled="!enabled"
                class="block w-full rounded-md border-0 py-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 -mt-1"
                :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" v-if="error != null" />
            </div>
        </div>
        <p class="mt-2 text-sm text-red-600" id="email-error">{{ error }}</p>
    </div>
</template>
