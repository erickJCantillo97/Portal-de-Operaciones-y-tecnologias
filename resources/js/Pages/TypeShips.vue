<script setup>
import { useForm } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import AppLayout from "@/Layouts/AppLayout.vue";
import MultiSelect from 'primevue/multiselect';
import SelectButton from 'primevue/selectbutton';
import Dropdown from 'primevue/dropdown';
import "../../sass/dataTableCustomized.scss";
import { ref, onMounted } from 'vue';
import Button from '@/Components/Button.vue';
import Dialog from 'primevue/dialog';
import TextInput from '@/Components/TextInput.vue';
import CustomModal from '@/Components/CustomModal.vue';
import Combobox from '@/Components/Combobox.vue';
import FileUpload from 'primevue/fileupload';
import Badge from 'primevue/badge';
import Image from 'primevue/image';

const props = defineProps({
    typeShips: Object
})
const verfoto = (foto) => {
    console.log(foto)
    return URL.createObjectURL(foto)
}

const typeShip = useForm(
    {
        name: '',
        type: '',
        disinger: '',
        hull_material: '', //material del casco
        length: '', //eslra
        breadth: '', //Manga
        draught: '', //calado de diseño
        depth: '', //punta
        full_load: '',
        light_ship: '',
        power_total: '',
        propulsion_type: '',
        velocity: '',
        autonomias: '',
        autonomy: '',
        crew: '',
        GT: '',
        CGT: '',
        bollard_pull: '',
        clasification: '',
        render: null,
    }
)

const save = () => {
    typeShip.transform((data) => ({
        ...data,
        hull_material: data.hull_material.name
    })).post(route('typeShips.store'), {
        onSuccess: (response) => {
            console.log(response)
        }
    })
}

const columns = ref([
    { field: 'name', header: 'Nombre' },
    { field: 'projects', header: 'Proyectos' },
    { field: 'type', header: 'Tipo' },
    { field: 'hull_material', header: 'Material del casco' },
    { field: 'length', header: 'Eslora' },
    { field: 'breadth', header: 'Manga' },
]);
const hull_materials = ref([{ name: 'ACERO' }, { name: 'ALUMINIO' }, { name: 'MATERIALES COMPUESTOS' }])
const selectedColumns = ref(columns.value);

</script>

<template>
    <AppLayout>
        <DataTable :value="props.typeShips" tableStyle="min-width: 50rem">
            <template #header>
                <div class="flex items-center justify-between">
                    <h class="text-base font-bold">Clases de buque</h>
                    <CustomModal>
                        <template #button>
                            <Button severity="primary" type="button">
                                <i class="fa-solid fa-plus"></i>
                            </Button>
                        </template>
                        <template #icon>
                            <i class="text-white fa-solid fa-ship"></i>
                        </template>
                        <template #titulo>
                            <span class="text-xl font-bold text-white white-space-nowrap">Nueva clase</span>
                        </template>
                        <template #body>
                            <div class="grid grid-cols-4 gap-2 px-1 pt-4">
                                <TextInput label="Nombre" type="text" :showSup="true" v-model="typeShip.name" />
                                <TextInput label="Tipo de buque" type="text" v-model="typeShip.type" />
                                <TextInput label="Empresa diseñadora" type="text" v-model="typeShip.disinger" />
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-900 capitalize"
                                        for="hull_material">
                                        Material Casco</label>
                                    <Dropdown id="hull_material" v-model="typeShip.hull_material" :options="hull_materials"
                                        optionLabel="name" placeholder="Selecciona un material"
                                        class="w-full rounded-md md:w-14rem" :pt="{
                                            root: {
                                                class: 'h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
                                            },
                                            input: {
                                                class: '!text-sm pt-3 pl-2'
                                            },
                                            item: {
                                                class: '!text-sm'
                                            }
                                        }" />
                                </div>
                                <TextInput label="Eslora" type="number" v-model="typeShip.length" />
                                <TextInput label="Manga" type="number" v-model="typeShip.breadth" />
                                <TextInput label="Calado de diseño" type="number" v-model="typeShip.draught" />
                                <TextInput label="Puntal" type="number" v-model="typeShip.depth" />
                                <TextInput label="Full Load" type="number" v-model="typeShip.full_load" />
                                <TextInput label="Ligth Ship" type="number" v-model="typeShip.light_ship" />
                                <TextInput label="Potencia" type="number" v-model="typeShip.power_total" />
                                <TextInput label="Tipo de propulsion" type="text" v-model="typeShip.propulsion_type" />
                                <TextInput label="Velocidad maxima" type="text" v-model="typeShip.velocity" />
                                <TextInput label="Autonomia" type="number" v-model="typeShip.autonomias" />
                                <TextInput label="Alcance" type="number" v-model="typeShip.autonomy" />
                                <TextInput label="Tripulacion maxima" type="number" v-model="typeShip.crew" />
                                <TextInput label="GT" type="number" v-model="typeShip.GT" />
                                <TextInput label="CGT" type="number" v-model="typeShip.CGT" />
                                <TextInput label="Bollard pull" type="number" v-model="typeShip.bollard_pull" />
                                <TextInput label="Clasificacion" type="text" v-model="typeShip.clasification" />
                                <FileUpload mode="basic" :multiple="false" accept="image/*" class="w-full"
                                    :maxFileSize="1000000" @select="typeShip.render = $event.files[0]"
                                    @removeUploadedFile="typeShip.render = null" v-model="typeShip.render"
                                    :showCancelButton="false" :showUploadButton="false" chooseLabel="Agregar imagen" :pt="{
                                        root: { class: '' }
                                    }">
                                </FileUpload>
                                <Image v-if="typeShip.render != null" id="verFoto" alt="imagen"
                                    :src="verfoto(typeShip.render)" preview height="170" width="190" />

                            </div>
                        </template>
                        <template #footer>
                            <Button severity="primary" @click="save()" class="hover:bg-danger">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <p>Guardar</p>
                            </Button>
                        </template>
                    </CustomModal>
                    <!-- <button @click="visible = true"
                        class="p-2 border rounded-md border-success text-success hover:bg-primary hover:text-white hover:border-primary">
                        <i class="fa-solid fa-plus"></i>
                        Agregar
                    </button> -->
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
                            <Button severity="success" @click="console.log(slotProps)" class="hover:bg-primary">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </Button>
                        </div>
                        <div>
                            <div title="Ver programacion">
                                <Button severity="danger" @click="console.log(slotProps)" class="hover:bg-primary">
                                    <i class="fa-solid fa-trash"></i>
                                </Button>
                            </div>
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>
    </AppLayout>
</template>
