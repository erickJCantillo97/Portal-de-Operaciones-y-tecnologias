@extends('errors::minimal')

<main class="h-screen min-h-screen w-full max-w-full bg-white">
    <section class="flex h-screen p-12">
        <article class="w-full items-center justify-center align-middle">
            <div class="w-32">
                <a href="https://www.topcotecmar.com/" target="_blank" rel="noopener noreferrer nofollow">
                    <img src="/images/cotemar-logo.png" alt="cotecmar-background" class="mx-6 size-32 object-cover" />
                </a>
            </div>
            <article class="mt-20 p-6 w-full">
                <p class="font-semibold leading-8 text-indigo-600 underline">ERROR 404</p>
                <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    No Hemos encontrado esa dirección
                </h1>
                <p class="mt-6 text-gray-600">
                    Por favor contacte con el administrador de la plataforma.
                </p>
                <div class="mt-6">
                    <button type="button" class="rounded-lg border border-blue-700 p-1 hover:bg-blue-100">
                        <a href="https://www.cotecmar.com/" target="_blank" rel="noopener noreferrer nofollow"
                            class="text-sm font-semibold text-blue-800">
                            <span class="text-blue-900" sr-only aria-hidden="true">&larr;</span>
                            Ir al Inicio
                        </a>
                    </button>
                </div>
            </article>
        </article>
        <div class="flex jutify-center items-center">
            @include('errors.partials.404_SVG')
        </div>
    </section>
    @include('errors.partials.footer')
</main>
