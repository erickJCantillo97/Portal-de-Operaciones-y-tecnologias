<template>
    <div class="bg-whitw md:backdrop-blur-xl px-2 md:px-6 py-4  shadow-2xl shadow-black rounded-3xl flex flex-col md:w-full bg-whitw md:bg-transparent sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mt-6">
            <slot name="logo" />
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
