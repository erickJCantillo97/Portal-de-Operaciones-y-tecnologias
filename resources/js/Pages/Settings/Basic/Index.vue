<script setup>
import {ref, onMounted} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import SimpleCRUD from '@/Components/SimpleCRUD.vue';
import axios from 'axios';

const gerencias = ref([])
const baseActivities = ref([]);

const baseActivitiesHeader = ref([
    {
        header: 'id',
        field: 'id',
        input: false,
        show: false
    },
    {
        header: 'Nombre',
        field: 'name',
        type: 'text',
    },

])



onMounted(() => {
    axios.get(route('get.gerencias')).then((res) => {
        gerencias.value = res.data.gerencias
    })

})


</script>
<template>
    <AppLayout>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <SimpleCRUD   :headers="[{header: 'id', field: 'id'}, {header: 'nombre', field:'name'}, { header: 'descripción', field: 'descripcion'}]" url="gerencias" title="Gerencias"></SimpleCRUD>

        <SimpleCRUD url="baseActivities" :headers="baseActivitiesHeader" :delete="true" :edit="true" :add="true"  title="Actividad Base"></SimpleCRUD>

        </div>

    </AppLayout>
</template>
