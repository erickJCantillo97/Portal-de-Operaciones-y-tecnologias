<script setup>
import { onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

onMounted(() => {
    parallaxBackgroundFX()
})

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
        username: data.username.includes('@') ? data.username.slice(0, data.username.indexOf("@")) : data.username,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const parallaxBackgroundFX = () => {
    const parallax = document.getElementById('parallax')

    window.addEventListener('mousemove', (e) => {
        const x = e.clientX / window.innerWidth
        const y = e.clientY / window.innerHeight

        parallax.style.transform = `translate(-${x * 5}px, -${y * 5}px)`
    })
}
</script>
<template>
    <div class="flex min-h-screen min-w-full items-center justify-center bg-white md:bg-primary text-white">
        <div class="max-w-sm md:min-w-max px-2">
            <div class="flex h-screen w-full items-center justify-center overflow-hidden bg-white md:bg-primary">
                <img id="parallax" src="images/fondo.webp"
                    class="absolute bottom-0 hidden md:block right-0 translate-x-[2.5px] translate-y-[2.5px] bg-cover bg-center bg-repeat-x blur-[0.6px]"
                    alt="login-background">
                <AuthenticationCard>
                    <template #logo>
                        <ApplicationLogo :height-logo=100 :width-logo=100 />
                    </template>
                    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                        {{ status }}
                    </div>

                    <div class="items-center justify-center text-primary md:text-white">
                        <h2 class="text-center text-2xl font-bold leading-9 tracking-tight">
                            Bienvenido al Portal Operaciones Tecnologicas
                        </h2>
                    </div>

                    <form action="#" method="POST" @submit.prevent="submit" class="space-y-6">
                        <div>
                            <div class="flex flex-col gap-1">
                                <label for="username">Usuario o Correo de Dominio</label>
                                <InputText id="username" size="small" class="!h-8" v-model="form.username"
                                    aria-describedby="username-help" />
                                <small id="username-help">Inicia sesión con tu usuario de dominio.</small>
                            </div>
                            <p class="text-xs font-bold text-red-800" id="email-error">{{ $page.props.errors.username }}
                            </p>
                        </div>
                        <div>
                            <div class="flex flex-col gap-1">
                                <label for="password">Contraseña</label>
                                <Password id="password" v-model="form.password" toggleMask :feedback="false"
                                    class="!h-8" :pt="{
                            root: '!grid'
                        }" />
                            </div>
                            <p class="text-xs font-bold text-red-800" id="email-error">{{ $page.props.errors.password }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember-me" v-model="form.remember" name="remember-me" type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-[#2c3494] focus:ring-indigo-600" />
                                <label for="remember-me"
                                    class="ml-3 mt-2 block text-sm leading-6 text-primary md:text-white">Recuérdame</label>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <Button type="submit" class="!h-8 !w-full"
                                :label="!form.processing ? 'Iniciar sesion' : 'Iniciando sesion...'"
                                :loading="form.processing" />
                        </div>
                    </form>
                </AuthenticationCard>
            </div>
        </div>
    </div>
</template>
