<script setup>
import ApplicationLogo from './ApplicationLogo.vue';

defineProps({
    message: {
        type: String,
        default: 'Cargando'
    }
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
    box-shadow: 0 0 10px 2px rgba(0, 0, 0, 0.3) inset;
}

.loader:after {
    box-shadow: 0 2px 0 rgb(46 48 146) inset;
    animation: rotate 2s linear infinite;
}

@keyframes rotate {
    0% {
        transform: rotate(0)
    }

    100% {
        transform: rotate(360deg)
    }
}
</style>

<template>
    <div class="w-full flex flex-col items-center text-center">
        <span class="grid items-center justify-center loader">
            <ApplicationLogo class="w-14" :letras="true"></ApplicationLogo>
        </span>
        <p class="font-bold pt-4 animate-pulse text-primary">{{ message }}</p>
    </div>
</template>
