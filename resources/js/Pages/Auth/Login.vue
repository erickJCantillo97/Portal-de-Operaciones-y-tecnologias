<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/Button.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
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
            <video autoplay="autoplay" class="video hidden md:block" loop="loop" muted="muted" preload="">
                <source src="/video/video_margen_completa_home.mp4" type="video/mp4">
            </video>
            <AuthenticationCard>
                <template #logo>
                    <ApplicationLogo :height-logo=100 :width-logo=100 />
                </template>
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <div class="items-center justify-center text-primary md:text-white">
                    <h2 class="text-center text-2xl font-bold leading-9 tracking-tight">
                        Bienvenido al Portal Operaciones Tecnologicas</h2>
                    <p class="mt-2 text-center text-sm leading-6 ">
                        <i>Inicia sesión con tu usuario de dominio</i>
                    </p>
                </div>

                <form action="#" method="POST" @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="price" class="block text-sm font-medium leading-6 text-primary md:text-white">
                            Nombre de usuario</label>
                        <div class="relative mt-2 rounded-md shadow-sm">

                            <input type="text" v-model="form.username"
                                class="block w-full rounded-md border-0 py-1.5 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                aria-describedby="price-currency" />
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm" id="price-currency">@cotecmar.com</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm font-bold text-red-800" id="email-error">{{ $page.props.errors.username }}
                        </p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-primary md:text-white">
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
                                class="ml-3 mt-2 block text-sm leading-6 text-primary md:text-white">Recuérdame</label>
                        </div>

                        <!-- <div class="text-sm leading-6">
                                    <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">¿Olvidó su
                                        contraseña?</a>
                                </div> -->
                    </div>

                    <div>
                        <button
                            class="bg-[#2c3494] w-full flex justify-center py-3 rounded-xl text-white shadow-xl hover:shadow-inner focus:outline-none transition duration-500 ease-in-out  transform hover:-translate-x hover:scale-105">
                            <transition name="slide-fade" v-if="form.processing">
                                <svg aria-hidden="true"
                                    class="w-6 h-6 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-primary"
                                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="currentColor" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentFill" />
                                </svg>
                            </transition>
                            Iniciar Sesión
                        </button>
                    </div>
                </form>

            </AuthenticationCard>
        </div>
    </div>
</template>
