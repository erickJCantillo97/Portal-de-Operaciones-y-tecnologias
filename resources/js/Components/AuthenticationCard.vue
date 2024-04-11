<template>
    <div
        class="bg-white md:backdrop-blur-xl md:px-6 py-4 shadow-lg  rounded-3xl flex flex-col md:w-full bg-whitw md:bg-withe sm:justify-center items-center pt-6 sm:pt-0 opacity-100 bg-opacity-50 mx-1 ">
        <div class="mt-6">
            <slot name="logo" />
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
