<script setup>
import { ref, onMounted } from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import SimpleCRUD from '@/Components/SimpleCRUD.vue';
import axios from 'axios';
import DataTableCustom from '@/Components/DataTableCustom.vue'

const gerencias = ref([])
const gruposConstructivos = ref([]) //
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

const gruposConstructivosHeader = ref([
    {
        header: 'id',
        field: 'id',
        input: false,
        show: false
    },
    {
        header: 'Grupo',
        field: 'name',
        type: 'text',
    },

    {
        header: 'Nombre',
        field: 'descripcion',
        type: 'text',
    },

])

onMounted(() => {
    axios.get(route('get.gerencias')).then((res) => {
        gerencias.value = res.data.gerencias
    })
    axios.get(route('gruposConstructivos.index')).then((res) => {
        gruposConstructivos.value = res.data
    })
})

const systemsHeaders = ref([
    {
        header: 'id',
        field: 'id',
        input: false,
        show: false
    },
    {
        header: 'Grupo Constructivo',
        field: 'constructive_group_id',
        type: 'multiple',
        show: false,
        options: gruposConstructivos
    },
    {
        field: 'code',
        header: 'Codigo',
        type: 'text',
    },
    {
        header: 'Nombre',
        field: 'name',
        type: 'text',
    }

])


</script>
<template>
    <AppLayout>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 gap-y-9">
            <SimpleCRUD   :headers="[{header: 'id', field: 'id'}, {header: 'nombre', field:'name'}, { header: 'descripción', field: 'descripcion'}]" url="gerencias" title="Gerencias"></SimpleCRUD>

            <SimpleCRUD url="gruposConstructivos" :headers="gruposConstructivosHeader"   title="Grupo Constructivos"></SimpleCRUD>

            <DataTableCustom url="system" :headers="systemsHeaders" :delete="true"  :add="true"  title="Sistema"></DataTableCustom>

            <DataTableCustom url="subSystem" :headers="systemsHeaders"   :add="true" :delete="true"  title="Sub Sistema"></DataTableCustom>

            <SimpleCRUD url="baseActivities" :headers="baseActivitiesHeader" :delete="true" :edit="true" :add="true"  title="Actividad Base"></SimpleCRUD>

            <SimpleCRUD url="specificActivities" :headers="baseActivitiesHeader" :delete="true"  :add="true"  title="Actividad Especifica"></SimpleCRUD>



        </div>
    </AppLayout>
</template>
