<script setup>
import ApplicationLogo from './ApplicationLogo.vue';

defineProps({
    message: String
})
</script>

<style scoped>
.loader {
    position: relative;
    width: 100px;
    height: 100px;
}

.loader:before,
.loader:after {
    content: '';
    border-radius: 50%;
    position: absolute;
    inset: 0;
    box-shadow: 0 0 10px 2px rgb(46, 48, 146, 0.3) inset;
}
</style>

<template>
    <div class="w-full flex flex-col items-center text-center">
        <span class="grid items-center justify-center loader">
            <ApplicationLogo class="justify-center" :letras="true"></ApplicationLogo>
        </span>
        <p class="font-bold pt-4 text-primary">{{ message }}</p>
    </div>
</template>
