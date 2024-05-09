<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Customers from '@/Pages/Project/Customers/Customers.vue';

const props = defineProps({
    customers: Array,
})

const url = [
    {
        ruta: 'customers.index',
        label: 'Clientes',
        active: true
    }
]
</script>
<template>
    <AppLayout :href="url">
        <Customers :customers></Customers>
    </AppLayout>
</template>