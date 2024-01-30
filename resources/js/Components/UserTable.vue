<template>
    <div class="flex space-x-2">
        <img v-if="photo" draggable="false" class="size-10 object-cover rounded-full border border-1 border-indigo-400"
            :src="user.photo" alt="profile-photo">
        <div class="block">
            <p class="font-semibold text-gray-800">{{ user.Nombres_Apellidos }}</p>
            <p class="text-xs italic text-gray-500">{{ user.Cargo }} - {{ user.Oficina }}</p>
        </div>
    </div>
</template>

<script setup>

defineProps({
    user: Object,
    photo: {
        type: Boolean,
        default: false
    }
})
</script>