<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import Dropdown from 'primevue/dropdown';
import { ref } from 'vue';
import Button from 'primevue/button';
import TextInput from '@/Components/TextInput.vue';
import CustomModal from '@/Components/CustomModal.vue';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';
import { useSweetalert } from '@/composable/sweetAlert';
import CustomDataTable from '@/Components/CustomDataTable.vue';
import CustomInput from '@/Components/CustomInput.vue';
const { toast } = useSweetalert();
const { confirmDelete } = useSweetalert();

const props = defineProps({
    typeShips: Object
})
const hull_materials = ['ACERO', 'ALUMINIO', 'MATERIALES COMPUESTOS']
const modalVisible = ref(false)
const modalType = ref('new')

const verfoto = (foto) => {
    try {
        return URL.createObjectURL(foto)
    } catch (error) {
        return null;
    }
}

const typeShip = useForm(
    {
        name: null,
        type: null,
        disinger: null,
        hull_material: null, //material del casco
        length: null, //eslra
        breadth: null, //Manga
        draught: null, //calado de diseño
        depth: null, //punta
        full_load: null,
        light_ship: null,
        power_total: null,
        propulsion_type: null,
        velocity: null,
        autonomias: null,
        autonomy: null,
        crew: null,
        GT: null,
        CGT: null,
        bollard_pull: null,
        clasification: null,
        render: null,
        image: null
    }
)
const showNew = () => {
    typeShip.reset()
    modalType.value = 'new'
    modalVisible.value = true
}

const save = () => {
    typeShip.post(route('typeShips.store'), {
        onSuccess: (response) => {
            modalVisible.value = false
            typeShip.reset()
            toast('Clase creada correctamente', 'success');
        },
        onError: (response) => {
            toast('Ocurrio un error', 'warning');
        }
    })
}

const showEdit = (event, dato) => {
    typeShip.id = dato.id
    typeShip.name = dato.name
    typeShip.type = dato.type
    typeShip.disinger = dato.disinger
    typeShip.hull_material = dato.hull_material //material del casco
    typeShip.length = dato.length //eslra
    typeShip.breadth = dato.breadth //Manga
    typeShip.draught = dato.draught //calado de diseño
    typeShip.depth = dato.depth //punta
    typeShip.full_load = dato.full_load
    typeShip.light_ship = dato.light_ship
    typeShip.power_total = dato.power_total
    typeShip.propulsion_type = dato.propulsion_type
    typeShip.velocity = dato.velocity
    typeShip.autonomias = dato.autonomias
    typeShip.autonomy = dato.autonomy
    typeShip.crew = dato.crew
    typeShip.GT = dato.GT
    typeShip.CGT = dato.CGT
    typeShip.bollard_pull = dato.bollard_pull
    typeShip.clasification = dato.clasification
    typeShip.render = dato.render
    modalType.value = 'edit'
    modalVisible.value = true
}
const edit = () => {
    typeShip.post(route('typeShips.update', typeShip.id), {
        onSuccess: (response) => {
            modalVisible.value = false
            typeShip.reset()
            toast('Cambios guardados', 'success');
        },
        onError: (response) => {
            toast('Ocurrio un error', 'warning');
        }
    })
}

const deleteClic = (event, dato) => {
    confirmDelete(dato.id, 'Clase', 'typeShips')
}

const columns = [
    { field: 'name', header: 'Nombre' },
    { field: 'count_ships', header: 'Cascos', filter: true, type: 'number' },
    { field: 'type', header: 'Tipo' },
    { field: 'hull_material', header: 'Material del casco', filter: true },
    { field: 'length', header: 'Eslora' },
    { field: 'breadth', header: 'Manga' },
];
const buttons = [
    { event: 'showEdit', severity: 'success', class: '', icon: 'fa-solid fa-pen-to-square', text: true, outlined: false, rounded: false },
    { event: 'deleteClic', severity: 'danger', icon: 'fa-solid fa-trash', class: '!h-8', text: true, outlined: false, rounded: false },
]

</script>

<template>
    <AppLayout>
        <div class="w-full h-[89vh] overflow-y-auto">
            <CustomDataTable :rows-default="100" :data="typeShips" :columnas="columns" :actions="buttons"
                @showEdit="showEdit" title="Clases de buque" @deleteClic="deleteClic">
                <template #buttonHeader>
                    <Button title="Nuevo" severity="success" label="Agregar" outlined class="!h-8" icon="fa-solid fa-plus"
                        @click="showNew()" />
                </template>
            </CustomDataTable>
        </div>
    </AppLayout>

    <CustomModal v-model:visible="modalVisible">
        <template #icon>
            <i class="text-white fa-solid fa-ship"></i>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                {{ modalType == 'new' ? 'Nueva clase' : 'Editar clase' }}</span>
        </template>
        <template #body>
            <div class="grid grid-cols-4 gap-2 px-1 pt-4">
                <CustomInput label="Nombre" id='name' :showSup="true" v-model:input="typeShip.name" />
                <CustomInput label="Tipo de buque" id="type" type="text" v-model:input="typeShip.type" />
                <CustomInput label="Empresa diseñadora" id="designer" type="text" v-model:input="typeShip.disinger" />
                <CustomInput label="Material Casco" id="material" :options="hull_materials" type="dropdown"
                    v-model:input="typeShip.hull_material" placeholder="Selecciona un material" />
                <CustomInput label="Eslora" type="number" id="eslora" v-model:input="typeShip.length" />
                <CustomInput label="Manga" type="number" id="breadth" v-model:input="typeShip.breadth" />
                <CustomInput label="Calado de diseño" id="draught" type="number" v-model:input="typeShip.draught" />
                <CustomInput label="Puntal" type="number" id="depth" v-model:input="typeShip.depth" />
                <CustomInput label="Full Load" type="number" id="full_load" v-model:input="typeShip.full_load" />
                <CustomInput label="Ligth Ship" type="number" id="light_ship" v-model:input="typeShip.light_ship" />
                <CustomInput label="Potencia" type="number" id="power_total" v-model:input="typeShip.power_total" />
                <CustomInput label="Tipo de propulsion" type="text" id="propulsion_type"
                    v-model:input="typeShip.propulsion_type" />
                <CustomInput label="Velocidad maxima" type="text" id="velocity" v-model:input="typeShip.velocity" />
                <CustomInput label="Autonomia" type="number" id="autonomias" v-model:input="typeShip.autonomias" />
                <CustomInput label="Alcance" type="number" id="autonomy" v-model:input="typeShip.autonomy" />
                <CustomInput label="Tripulacion maxima" id="crew" type="number" v-model:input="typeShip.crew" />
                <CustomInput label="GT" type="number" id=gt v-model:input="typeShip.GT" />
                <CustomInput label="CGT" type="number" id="cgt" v-model:input="typeShip.CGT" />
                <CustomInput label="Bollard pull" type="number" id="bollard_pull" v-model:input="typeShip.bollard_pull" />
                <CustomInput label="Clasificacion" type="text" id="clasification" v-model:input="typeShip.clasification" />
                <CustomInput label="Imagen" type="file" id="image" v-model:input="typeShip.image" acceptFile="image/*" />
                <span class="flex items-center justify-center">
                    <Image v-if="typeShip.image != null || typeShip.render != null" id="verFoto" alt="imagen" preview
                        height="50" width="50" :pt="{
                        }">
                        <template #image>
                            <div class="flex items-center justify-center w-full">
                                <img :src="typeShip.image ? verfoto(typeShip.image) : typeShip.render" alt="image"
                                    class="h-12" />
                            </div>
                        </template>
                        <template #preview="slotProps">
                            <img :src="typeShip.image ? verfoto(typeShip.image) : typeShip.render"
                                class="!max-w-[80vw] !max-h-[80vh]" alt="preview" :style="slotProps.style"
                                @click="slotProps.previewCallback" />
                        </template>
                    </Image>
                </span>

            </div>
        </template>
        <template #footer>
            <Button severity="primary" outlined label="Guardar" icon="fa-solid fa-floppy-disk"
                @click="modalType == 'new' ? save() : edit()" />
            <Button severity="danger" outlined label="Cancelar" icon="fa-regular fa-circle-xmark"
                @click="modalVisible = false; typeShip.reset()" />
        </template>
    </CustomModal>
</template>
