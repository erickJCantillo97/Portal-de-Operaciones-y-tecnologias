@extends('errors::minimal')

<section class="absolute bottom-0 flex w-full items-center justify-center py-2 text-center">
    <footer class="text-sm italic text-gray-700">
      © TOP - COTECMAR {{ \Carbon\Carbon::now()->format('d-m-Y') }} - Todos los derechos reservados.
      <div class="inline-block w-36 bg-blue-800">
        <span class="text-white ">#Seguimos </span>
        <span class="text-yellow-300">Avante</span>
      </div>
    </footer>
  </section>