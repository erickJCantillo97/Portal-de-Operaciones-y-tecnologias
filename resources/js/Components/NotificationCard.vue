<script setup>
const format_ES_Date = (date) => {
  return new Date(date).toLocaleString('es-CO',
    { day: '2-digit', month: 'long', year: 'numeric', weekday: "long" })
}
</script>

<template>
  <main class="col-span-8">
    <div class="flex flex-nowrap rounded-lg border-l-4 border-purple-600 gap-4 w-full h-24 p-4 bg-slate-100">
      <div class="size-24">
        <img class="size-12 object-cover rounded-full" src="../../../public/images/generic-boat.png" alt="Profile-Picture" />
      </div>
      <div class="max-w-full w-full h-6">
        <h3 class="text-md font-semibold">Ronny Gutierrez Vitola</h3>
        <div class="w-full h-34">
          <p class="text-sm text-gray-500 truncate">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur itaque recusandae tenetur odio at omnis.
          </p>
        </div>
        <div class="w-20 h-6">
          <span class="text-xs italic text-gray-500">Hace 12 mins</span>
        </div>
      </div>
      <div class="w-36 bg-gray-300 rounded-lg align-middle">
        <h3 class="text-sm text-gray-500">17, Ene de 2024</h3>
      </div>
    </div>
  </main>
</template>
