<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import AppLayout from "@/Layouts/AppLayout.vue";
import MultiSelect from 'primevue/multiselect';
import "../../sass/dataTableCustomized.scss";
import { ref, onMounted } from 'vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import TextInput from '@/Components/TextInput.vue';

const visible = ref(false)

const columns = ref([
    { field: 'name', header: 'Nombre' },
    { field: 'projects', header: 'Proyectos' },
    { field: 'type', header: 'Tipo' },
    { field: 'hullMaterial', header: 'Material del casco' },
    { field: 'length', header: 'Eslora' },
]);
const selectedColumns = ref(columns.value);

const onToggle = (val) => {
    selectedColumns.value = columns.value.filter(col => val.includes(col));
};

</script>

<template>
    <AppLayout>
        <DataTable :value="typeShips" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex items-center justify-between">
                    <h class="text-base font-bold">Clases de buque</h>
                    <button @click="visible = true"
                        class="p-2 border rounded-md border-success text-success hover:bg-primary hover:text-white hover:border-primary">
                        <i class="fa-solid fa-plus"></i>
                        Agregar
                    </button>
                </div>
                <!-- <div style="text-align:left">
                    <MultiSelect :modelValue="selectedColumns" :options="columns" optionLabel="header"
                        @update:modelValue="onToggle" display="chip" placeholder="Select Columns" />
                </div> -->
            </template>
            <Column v-for="(col, index) of selectedColumns" :field="col.field" :header="col.header" :key="col.field + '_' +
                index">

            </Column>
            <Column header="Acciones" class="space-x-3">
                <template #body="slotProps">
                    <div
                        class="flex pl-4 pr-3 space-x-2 text-sm font-medium text-gray-900 whitespace-normal sm:pl-6 lg:pl-8">
                        <div title="Ver programacion">
                            <Button severity="primary" @click="console.log(slotProps)" class="hover:bg-primary">
                                <i class="fa-solid fa-list-check" />
                            </Button>
                        </div>
                        <div title="Ver cronograma">
                            <Button severity="success" @click="console.log(slotProps)" class="hover:bg-danger">
                                <i class="fa-solid fa-chart-gantt" />
                            </Button>
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>
        <Dialog v-model:visible="visible" modal header="Header" :closable="false" closeOnEscape :style="{ width: '60rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="flex flex-col items-center w-full space-y-3">
                    <svg xmlns="http://www.w3.org/2000/svg" height="3em" viewBox="0 0 576 512" class="fill-success">
                        <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                        <path
                            d="M192 32c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32V64h48c26.5 0 48 21.5 48 48V240l44.4 14.8c23.1 7.7 29.5 37.5 11.5 53.9l-101 92.6c-16.2 9.4-34.7 15.1-50.9 15.1c-19.6 0-40.8-7.7-59.2-20.3c-22.1-15.5-51.6-15.5-73.7 0c-17.1 11.8-38 20.3-59.2 20.3c-16.2 0-34.7-5.7-50.9-15.1l-101-92.6c-18-16.5-11.6-46.2 11.5-53.9L96 240V112c0-26.5 21.5-48 48-48h48V32zM160 218.7l107.8-35.9c13.1-4.4 27.3-4.4 40.5 0L416 218.7V128H160v90.7zM306.5 421.9C329 437.4 356.5 448 384 448c26.9 0 55.4-10.8 77.4-26.1l0 0c11.9-8.5 28.1-7.8 39.2 1.7c14.4 11.9 32.5 21 50.6 25.2c17.2 4 27.9 21.2 23.9 38.4s-21.2 27.9-38.4 23.9c-24.5-5.7-44.9-16.5-58.2-25C449.5 501.7 417 512 384 512c-31.9 0-60.6-9.9-80.4-18.9c-5.8-2.7-11.1-5.3-15.6-7.7c-4.5 2.4-9.7 5.1-15.6 7.7c-19.8 9-48.5 18.9-80.4 18.9c-33 0-65.5-10.3-94.5-25.8c-13.4 8.4-33.7 19.3-58.2 25c-17.2 4-34.4-6.7-38.4-23.9s6.7-34.4 23.9-38.4c18.1-4.2 36.2-13.3 50.6-25.2c11.1-9.4 27.3-10.1 39.2-1.7l0 0C136.7 437.2 165.1 448 192 448c27.5 0 55-10.6 77.5-26.1c11.1-7.9 25.9-7.9 37 0z" />
                    </svg>
                    <span class="font-bold white-space-nowrap text-success">Nueva clase</span>
                </div>
            </template>
            <div class="grid grid-cols-3 gap-3">

                <TextInput label="Nombre" type="text" v-model="value" />
                <TextInput label="Nombre" type="text" v-model="value" />
                <TextInput label="Nombre" type="text" v-model="value" />
                <TextInput label="Nombre" type="text" v-model="value" />
                <TextInput label="Nombre" type="text" v-model="value" />

            </div>
            <template #footer>
                <button
                    class="p-2 border rounded-md border-success text-success hover:border-primary hover:bg-primary hover:text-white"
                    @click="visible = false" autofocus>
                    <i class="fa-regular fa-circle-check"></i>
                    Guardar
                </button>
            </template>
        </Dialog>
    </AppLayout></template>
