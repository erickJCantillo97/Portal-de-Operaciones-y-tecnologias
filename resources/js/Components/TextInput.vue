<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    label: {
        type: String,
        required:true
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

</script>

<template>
    <div>
        <label for="password" class="block capitalize text-sm font-mediumtext-gray-900">{{label}}</label>
        <div>
            <input  type="text" autocomplete="current-password" required="" class="block w-full rounded-md border-0 py-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 -mt-1"  :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"/>
        </div>
    </div>


</template>
