<script setup>
import {ref, onMounted} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import SimpleCRUD from '@/Components/SimpleCRUD.vue';
import axios from 'axios';

const gerencias = ref([])



onMounted(() => {
    axios.get(route('get.gerencias')).then((res) => {
        gerencias.value = res.data.gerencias
    })
})


</script>
<template>
    <AppLayout>
        <div class="space-y-4">
        <SimpleCRUD  :headers="[{header: 'id', field: 'id'}, {header: 'nombre', field:'name'}, { header: 'descripción', field: 'descripcion'}]" :elements="gerencias" title="Gerencias"></SimpleCRUD>
        
        </div>

    </AppLayout>
</template>
