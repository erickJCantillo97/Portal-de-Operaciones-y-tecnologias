@extends('errors::minimal')

@section('content')
    <div class="grid min-h-screen w-screen bg-white">
        <header class="mx-auto w-full max-w-7xl px-6 pt-6 sm:pt-10 lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:px-8">
            <a href="https://www.cotecmar.com/" class="flex items-center">
                <span class="sr-only">Cotecmar</span>
                <img src="/images/cotecmar-logo-bg-white.png" alt="cotecmar-background" class="size-20 object-cover" />
                <span class="text-2xl mx-4 text-primary font-extrabold">
                    Portal Operaciones Tecnológicas
                </span>
            </a>

        </header>
        <main class="mx-48 w-full lg:col-span-2 lg:col-start-1 lg:row-start-2 lg:px-8">
            <div class="max-w-lg">
                <p class="text-base font-semibold leading-8 text-indigo-600">Error 403</p>
                <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    No cuenta con permisos para acceder a la plataforma
                </h1>
                <p class="mt-6 text-base leading-7 text-gray-600">Por favor contacte con tu Administrador</p>
                <div class="mt-10">
                    <button severity="info" size="small" raised rounded @click="logout()">
                        <a class="text-white text-sm font-semibold leading-7">
                            <span aria-hidden="true">&larr;</span>
                            Cerrar Sesión
                        </a>
                    </button>
                </div>
                <Footer class="mt-20" />
            </div>
        </main>
        <div class="hidden lg:relative lg:col-start-2 lg:row-start-1 lg:row-end-4 lg:block">

        </div>
    </div>
@endsection
