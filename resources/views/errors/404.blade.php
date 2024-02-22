@extends('errors::minimal')

<div class="grid min-h-screen grid-cols-1 grid-rows-[1fr,auto,1fr] bg-white lg:grid-cols-[max(50%,36rem),1fr]">
    <header class="mx-auto w-full max-w-7xl px-6 pt-6 sm:pt-10 lg:col-span-2 lg:col-start-1 lg:row-start-1 lg:px-8">
        <a href="https://www.cotecmar.com/">
            <span class="sr-only">Cotecmar</span>
            logo
        </a>
    </header>
    <main class="mx-auto w-full max-w-7xl px-6 py-24 sm:py-32 lg:col-span-2 lg:col-start-1 lg:row-start-2 lg:px-8">
        <div class="max-w-lg">
            <p class="text-base font-semibold leading-8 text-indigo-600">404</p>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                No cuenta con permisos para acceder a la plataforma
            </h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Por favor contacte con OFTIC</p>
            <div class="mt-10">
                <Button severity="info" size="small" raised rounded @click="logout()">
                    <a class="text-white text-sm font-semibold leading-7">
                        <span aria-hidden="true">&larr;</span>
                        Cerrar Sesión
                    </a>
                </Button>
            </div>
            <Footer class="mt-20" />
        </div>
    </main>
    <div class="hidden lg:relative lg:col-start-2 lg:row-start-1 lg:row-end-4 lg:block">
        <img src="https://www.cotecmar.com/sites/default/files/media/imagenes/2021-12/miniatura1.jpg"
            alt="cotecmar-background" class="h-full w-full object-cover" />
    </div>
</div>
