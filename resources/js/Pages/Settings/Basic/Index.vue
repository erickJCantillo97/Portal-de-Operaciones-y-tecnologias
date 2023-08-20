<script setup>
import {ref, onMounted} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import SimpleCRUD from '@/Components/SimpleCRUD.vue';
import axios from 'axios';

const gerencias = ref([
])
const activities = ref([
    { id: '1', tarea: 'Nueva tarea', prioridad: 'GECON'},
    // More people...
])

onMounted(() => {
    axios.get(route('get.gerencias')).then((res) => {
        gerencias.value = res.data.gerencias
    })
})


</script>
<template>
    <AppLayout>
        <div class="space-y-4">
        <SimpleCRUD  :headers="['id', 'nombre', 'descripcion']" :elements="gerencias" title="Gerencias"></SimpleCRUD>


        <SimpleCRUD :headers="['id', 'tarea', 'prioridad']" :elements="activities" title="Actividades"></SimpleCRUD>
        </div>

    </AppLayout>
</template>
