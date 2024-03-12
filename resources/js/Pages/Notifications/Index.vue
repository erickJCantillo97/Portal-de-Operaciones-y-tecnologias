<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import NotificationCard from '@/Components/NotificationCard.vue'
</script>
<template>
  <AppLayout>

    <body>
      <section class="block justify-between md:flex">
        <div class="p-4">
          <h3 class="text-xl font-semibold">
            Notificaciones
          </h3>
        </div>
      </section>
      <main>
        <section class="grid grid-cols-12 gap-6 !mx-auto">
          <NotificationCard />
        </section>
      </main>
    </body>
  </AppLayout>
</template>
