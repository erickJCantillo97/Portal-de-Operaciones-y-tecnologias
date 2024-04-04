<script setup>
import CustomInput from '@/Components/CustomInput.vue';
import CustomModal from '@/Components/CustomModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
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
            material: material,
            nivel: 1,
            ver: 1,
            codigo_material: material.material.code,
            cantidad: parseFloat(material.count),
            estado: material.status,
            unidad: material.unit,
            observacion: material.observation,
        });
    }
});

const addEstado = (ma) => {
    var posicion = form.materiales.indexOf(ma);
    const cantidad = form.materiales
        .filter((material) =>
            material.material.material.description == ma.material.material.description &&
            material.material.material.code == ma.material.material.code &&
            material.material.requirement.consecutive == ma.material.requirement.consecutive
        )
        .reduce((total, material) => {
            return parseFloat(total) + parseFloat(material.cantidad);
        }, 0);

    if (ma.material.count - cantidad > 0) {
        console.log(ma.material)
        form.materiales.splice(posicion + 1, 0, {
            material: ma.material,
            nivel: 2,
            ver: 1,
            codigo_material: ma.material.material.code,
            cantidad: ma.material.count - cantidad,
            estado: "0",
            unidad: ma.material.unit,
            observacion: "",
        });
    } else {
        toast.add({ severity: 'error', group: 'customToast', text: 'No se puede realizar esta acción, ¡Material Completo!', life: 2000 });
    }
};


const deleteEstado = (index) => {
    form.materiales[index - 1].cantidad += form.materiales[index].cantidad;
    form.materiales.splice(index, 1);
};




const submit = () => {
    router.post(route('store.requirement.oficial'), form, {
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
                <span class="text-2xl font-extrabold text-primary h-full items-center flex">
                    <p> Gestion de Requerimientos </p>
                </span>

            </div>
            <div class="block space-y-2 p-2 overflow-y-auto">

                <div class="block items-center space-x-2 rounded-md px-4" v-for="(material, index) in form.materiales"
                    :class="material.nivel > 1 ? 'bg-white border border-gray-800 px-8' : 'bg-secondary'">
                    <div class="flex justify-between my-2">
                        <span class="text-primary font-bold">
                            <span class="text-primary cursor-default" v-tooltip="'Cantidad Material Requerido'">
                                {{ material.material.count }}
                            </span> {{ material.material.material.description }}
                            <div>
                                {{ material.material.requirement.consecutive }}
                            </div>
                        </span>
                    </div>
                    <div class="flex space-x-4">
                        <div class="w-1/6">
                            <span for="code text-sm ">Codigo de material</span>
                            <CustomInput v-model:input="material.codigo_material"></CustomInput>
                        </div>
                        <div class="w-1/6">
                            <span for="code text-sm ">Cantidad</span>
                            <CustomInput v-model:input="material.cantidad" :min="1" :maxFractionDigits="4"
                                type="number">
                            </CustomInput>
                        </div>
                        <div class="w-1/6">
                            <span for="code text-sm ">Unidad de medida</span>
                            <CustomInput v-model:input="material.unidad" option-label="text" option-value="value"
                                :options="unidades" type="dropdown"></CustomInput>
                        </div>
                        <div class="w-1/6">
                            <span for="code text-sm ">Estado del material</span>
                            <CustomInput v-model:input="material.estado" option-label="text" option-value="value"
                                :options="estados" type="dropdown"></CustomInput>
                        </div>
                        <div class="w-2/6">
                            <span for="code text-sm ">Observaciones</span>
                            <CustomInput :rowsTextarea="2" type="textarea" v-model:input="material.observacion">
                            </CustomInput>
                        </div>
                        <div class=" space-x-4 text-center">
                            <div for="code text-sm">Acciones</div>
                            <Button class="size-10" icon="fa-solid fa-plus" v-if="material.nivel == 1"
                                @click="addEstado(material)" severity="success"></Button>
                            <Button class="size-10" icon="fa-solid fa-minus" v-else @click="deleteEstado(index)"
                                severity="danger"></Button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="justify-between w-full flex mt-2 px-4">
                <Link :href="route('requirements.index')">
                <Button icon="fa-solid fa-arrow-left" raised label="Cancelar" severity="danger"></Button>
                </Link>
                <div class="flex space-x-2">
                    <Button label="Guardar" icon="fa-solid fa-floppy-disk" raised severity="primary"></Button>
                    <Button label="Enviar a Oficiales" @click="submit" icon="fa-solid fa-paper-plane" raised
                        severity="success"></Button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>