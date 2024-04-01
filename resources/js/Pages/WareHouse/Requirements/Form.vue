<script setup>
import CustomInput from '@/Components/CustomInput.vue';
import CustomModal from '@/Components/CustomModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { onMounted, ref } from 'vue';
import { useToast } from "primevue/usetoast";
import CustomDataTable from '@/Components/CustomDataTable.vue';
const toast = useToast()
const props = defineProps({
    materials: Array

})

const open = ref(false)
const formData = ref({
    requirement: {}
})


const form = useForm({
    materiales: [],
});

const estados = [
    { value: "0", text: "Pendiente" },
    { value: "1", text: "Disponible GECON" },
    { value: "2", text: "Disponible SAP" },
    { value: "6", text: "Disponible INHOUSE" },
    { value: "3", text: "Retal" },
];

const unidades = [
    { value: "0", text: "UNIDAD" },
    { value: "1", text: "METROS" },
    { value: "2", text: "PIES" },
];

onMounted(() => {
    for (let material of props.materials) {
        form.materiales.push({
            material: material.material,
            nivel: 1,
            ver: 1,
            estados: {
                codigo_material: material.material.code,
                cantidad: parseFloat(material.count).toFixed(2),
                estado: material.status,
                unidad: material.unit,
                observacion: material.observation,
            },
        });
    }
    console.log(form.materiales);
});


const addItem = () => {
    // toast.add({ severity: 'success', group: 'customToast', text: 'Atividad Eliminada', life: 2000 });
    formData.value.requirement = {}
    open.value = true
}



const gestion = (event, data) => {
    router.get(route('manage.requirements', { requirements: data.map((x) => x.id) }))
}

const submit = () => {
    router.post(route('requirements.store'), formData.value.requirement, {
        preserveScroll: true,
        onSuccess: (res) => {
            open.value = false
            toast.add({ severity: 'success', group: 'customToast', text: 'Atividad Eliminada', life: 2000 });

        },
        onError: (errors) => {
            toast.add({ severity: 'error', group: 'customToast', text: 'Ocurrio un Error', life: 2000 });
            // toast('Hubo un error al crear el contrato', 'error')
        },
        onFinish: visit => {
            loading.value = false
        }
    })
}
</script>

<template>
    <AppLayout>
        <div class="h-full w-full overflow-y-auto">
            <div class="flex justify-between items-center px-4 h-min">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    <p> Requerimientos de Materiales </p>
                </span>

            </div>
            <div class="block space-y-4 p-2">

                <div class="block items-center space-x-2 bg-gray-200 rounded-md py-2 px-4"
                    v-for="material in form.materiales">
                    <span class="text-primary font-extrabold">
                        {{ material.material.description }}
                    </span>
                    <div class="flex space-x-4">
                        <div class="w-1/6">
                            <span for="code text-sm ">Codigo de material</span>
                            <CustomInput v-model:input="material.estados.codigo_material"></CustomInput>
                        </div>
                        <div class="w-1/6">
                            <span for="code text-sm ">Cantidad</span>
                            <CustomInput v-model:input="material.estados.cantidad" maxFractionDigits="4" type="number">
                            </CustomInput>
                        </div>
                        <div class="w-1/6">
                            <span for="code text-sm ">Unidad de medida</span>
                            <CustomInput v-model:input="material.estados.unidad" option-label="text"
                                option-value="value" :options="unidades" type="dropdown"></CustomInput>
                        </div>
                        <div class="w-1/6">
                            <span for="code text-sm ">Estado del material</span>
                            <CustomInput v-model:input="material.estados.estado" option-label="text"
                                option-value="value" :options="estados" type="dropdown"></CustomInput>
                        </div>
                        <div class="w-1/6">
                            <span for="code text-sm ">Observaciones</span>
                            <CustomInput v-model:input="material.estados.observacion"></CustomInput>
                        </div>
                        <div class="w-1/6 space-x-4 text-center">
                            <div for="code text-sm">Acciones</div>
                            <Button class="size-10" icon="fa-solid fa-plus" severity="success"></Button>
                            <Button class="size-10" icon="fa-solid fa-minus" severity="danger"></Button>
                        </div>
                    </div>
                    <!-- {{ material }} -->

                </div>
            </div>

        </div>

    </AppLayout>
</template>