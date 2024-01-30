<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: []
    }
});

// $table->id();
// $table->integer('user_id')->index();
// $table->unsignedBigInteger('category_id')->index()->default(0);
// $table->string('name');
// $table->string('letter')->nullable();
// $table->string('level');
// $table->boolean('calibration')->default(0);
// $table->boolean('status')->default(true);
// $table->timestamps();
// $table->softDeletes();

const columnas = [
    { field: 'name', header: 'Nombre' },
    { field: 'letter', header: 'Letra' },
    { field: 'level', header: 'Nivel' },
    { field: 'calibration', header: 'Calibracion' },
    { field: 'status', header: 'Estado' },
]

const actions = [
    { event: 'edit', severity: 'warning', icon: 'fa-solid fa-list-check', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', icon: 'fa-solid fa-chart-gantt', text: true, outlined: false, rounded: false },
    // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

</script>

<template>
    <AppLayout>
        <CustomDataTable :data="categories" title="Categorias" :columnas="columnas" :actions="actions">
            <template #buttonHeader>
                <Button label="Nuevo" icon="fa-solid fa-plus"/> 
            </template>
        </CustomDataTable>
    </AppLayout>
    <CustomModal>
        <template #body>

        </template>
    </CustomModal>
</template>