<script setup>
import { computed, ref } from 'vue';
import { EyeIcon, EyeSlashIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'

const input = ref(null);
const showPassword = ref(false);

defineProps({
    modelValue: String,
    placeholder: String,
    label: {
        type: String,
        required: true
    },
    showSup: {
        type: Boolean,
        default: false
    },
    showEmailDomain: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: null
    },
    type: {
        type: String,
        default: 'text'
    },
    name: {
        type: String,
        default: 'text'
    },
    enabled: {
        type: Boolean,
        default: true
    },
});

const Icon = computed(() => (showPassword.value ? EyeSlashIcon : EyeIcon));

const toggleShow = () => {
    showPassword.value = !showPassword.value;
}

defineEmits(['update:modelValue']);

</script>

<template>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-900 capitalize">
            {{ label }}
            <sup v-if="showSup" class="text-danger">*</sup>
        </label>
        <div class="relative rounded-md shadow-sm">
            <input :type="type != 'password' ? type : (showPassword ? 'text' : 'password')" :name="name"
                :showPassword="showPassword" autocomplete="current-password"
                :class="[error != null ? 'text-red-900 ring-1 ring-inset ring-red-300 focus:ring-2 focus:ring-inset focus:ring-red-500' : '']"
                :required="showSup" :placeholder="placeholder" :disabled="!enabled"
                class="block w-full py-2 -mt-1 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
            <div v-if="showEmailDomain" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <span class="text-gray-500 sm:text-sm" id="price-currency">@cotecmar.com</span>
            </div>
            <button @click="toggleShow" type="button" v-if="type == 'password'"
                class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none">
                <Icon class="w-5 h-5 text-gray-500" aria-hidden="true" />
            </button>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <ExclamationCircleIcon class="w-5 h-5 text-red-500" aria-hidden="true" v-if="error != null" />
            </div>
        </div>
        <p class="text-sm text-red-600" id="email-error">{{ error }}</p>
    </div>
</template>
