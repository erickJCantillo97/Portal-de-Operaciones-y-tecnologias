<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/Button.vue';


defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>


<template>
    <!--
      This example requires updating your template:

      ```
      <html class="h-full bg-white">
      <body class="h-full">
      ```
    -->
    <div class="min-w-full min-h-screen flex sm:justify-center justify-center items-center">
        <div class="sm:max-w-sm min-w-max">
            <video autoplay="autoplay" class="video" loop="loop" muted="muted" preload="">
                <source src="/video/video_margen_completa_home.mp4" type="video/mp4">
            </video>
            <div class="rounded-3xl backdrop-blur-xl px-6 py-4  shadow-2xl shadow-black">
                <div class="items-center justify-center">
                    <ApplicationLogo class="mx-[45%]"></ApplicationLogo>
                    <h2 class="mt-8 text-center text-2xl font-bold leading-9 tracking-tight text-white">
                        Bienvenido al Portal Operaciones Tecnologicas</h2>
                    <p class="mt-2 text-center text-sm leading-6 text-gray-200">
                        <i>Inicia sesión con tu usuario de dominio</i>
                    </p>
                </div>

                <div class="mt-10">
                    <div>
                        <form action="#" method="POST" @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="price" class="block text-sm font-medium leading-6 text-white">
                                    Nombre de usuario</label>
                                <div class="relative mt-2 rounded-md shadow-sm">

                                    <input type="text" v-model="form.username"
                                        class="block w-full rounded-md border-0 py-1.5 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        aria-describedby="price-currency" />
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 sm:text-sm" id="price-currency">@cotecmar.com</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-red-600" id="email-error">{{ $page.props.errors.username }}</p>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium leading-6 text-white">
                                    Password</label>
                                <div class="mt-2">
                                    <input id="password" v-model="form.password" name="password" type="password"
                                        autocomplete="current-password" required=""
                                        class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember-me" v-model="form.remember" name="remember-me" type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-[#2c3494] focus:ring-indigo-600" />
                                    <label for="remember-me"
                                        class="ml-3 mt-2 block text-sm leading-6 text-white">Recuérdame</label>
                                </div>

                                <!-- <div class="text-sm leading-6">
                                    <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">¿Olvidó su
                                        contraseña?</a>
                                </div> -->
                            </div>

                            <div>
                                <button
                                    class="bg-[#2c3494] w-full py-3 rounded-xl text-white shadow-xl hover:shadow-inner focus:outline-none transition duration-500 ease-in-out  transform hover:-translate-x hover:scale-105"
                                     :loading="form.processing">Iniciar Sesión</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
