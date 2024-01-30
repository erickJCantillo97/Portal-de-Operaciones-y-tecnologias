<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomModal from '@/Components/CustomModal.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CustomInput from '@/Components/CustomInput.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: []
    }
});

const modalVisible = ref(false)

const form = useForm({})

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
    { field: 'padre.name', header: 'Sub Grupo' },
    { field: 'padre.letter', header: 'Letra' },
    { field: 'padre.padre.name', header: 'Grupo' },
    { field: 'padre.padre.letter', header: 'Letra' },
    // { field: 'status', header: 'Estado' },
]

const actions = [
    { event: 'edit', severity: 'warning', icon: 'fa-solid fa-list-check', text: true, outlined: false, rounded: false },
    { event: 'delete', severity: 'danger', icon: 'fa-solid fa-chart-gantt', text: true, outlined: false, rounded: false },
    // { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

</script>

<template>
    <AppLayout>
        <div class="h-[89vh] overflow-y-auto">
            <CustomDataTable :rowsDefault="20" :data="categories" title="Categorias" :columnas="columnas" :actions="actions">
                <template #buttonHeader>
                    <Button label="Nuevo" severity="success" icon="fa-solid fa-plus" @click="modalVisible = true" />
                </template>
            </CustomDataTable>
    </div>
    </AppLayout>
    <CustomModal v-model:visible="modalVisible">
        <template #icon>
            <i class="fa-solid text-white fa-table-list"></i>
        </template>
        <template #titulo>
            <span class="text-lg font-bold text-white white-space-nowrap">
                Agregar
            </span>
        </template>
        <template #body>
            <span class="grid grid-cols-2 gap-2">
                <CustomInput label="Nombre" v-model:input="form.name" placeholder="Nombre para mostrar"></CustomInput>
                <CustomInput label="Letra" v-model:input="form.letter" placeholder="Letra que lo identificara"></CustomInput>
                <CustomInput label="Tipo" v-model:input="form.level" type="dropdown" placeholder="Selecciona una categoria" :options="['Grupo','SubGrupo','Categoria']" ></CustomInput>
                <CustomInput label="Requiere calibracion?" v-model:input="form.calibration"></CustomInput>
                <CustomInput label="Estado" v-model:input="form.status"></CustomInput>
            </span>
        </template>
        <template #footer>
            <Button severity="primary" outlined label="Guardar" icon="fa-solid fa-floppy-disk"
                @click="modalType == 'new' ? save() : edit()" />
            <Button severity="danger" outlined label="Cancelar" icon="fa-regular fa-circle-xmark"
                @click="modalVisible = false; typeShip.reset()" />
        </template>
    </CustomModal>
</template>