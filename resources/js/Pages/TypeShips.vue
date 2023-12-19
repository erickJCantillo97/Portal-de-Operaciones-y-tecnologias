<script setup>
import { useForm, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import AppLayout from "@/Layouts/AppLayout.vue";
import MultiSelect from 'primevue/multiselect';
import SelectButton from 'primevue/selectbutton';
import Dropdown from 'primevue/dropdown';
import "../../sass/dataTableCustomized.scss";
import { ref, onMounted } from 'vue';
import Button from '@/Components/Button.vue';
import TextInput from '@/Components/TextInput.vue';
import CustomModal from '@/Components/CustomModal.vue';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';
import { useSweetalert } from '@/composable/sweetAlert';
const { toast } = useSweetalert();
const { confirmDelete } = useSweetalert();

const props = defineProps({
    typeShips: Object
})

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
    typeShip.transform((data) => ({
        ...data,
        hull_material: data.hull_material ? data.hull_material.name : null
    })).post(route('typeShips.store'), {
        onSuccess: (response) => {
            modalVisible.value = false
            toast('Clase creada correctamente', 'success');
        },
        onError: (response) => {
            console.table(response)
            toast('Ocurrio un error', 'warning');
        }
    })
}

const showEdit = (dato) => {
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
    typeShip.transform((data) => ({
        ...data,
        hull_material: data.hull_material ? data.hull_material.name : null
    }))
    router.post(route('typeShips.update', typeShip.id), typeShip, {
        onSuccess: (response) => {
            modalVisible.value = false
            toast('Cambios guardados', 'success');
            console.table(response)
        },
        onError: (response) => {
            toast('Ocurrio un error', 'warning');
            console.table(response)
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
                    <div>
                        <Button severity="primary" type="button" @click="showNew()">
                            <i class="fa-solid fa-plus" />
                        </Button>
                    </div>
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
                        <div title="Editar">
                            <Button severity="success" @click="showEdit(slotProps.data)" class="hover:bg-primary">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </Button>
                        </div>
                        <div>
                            <div title="Eliminar">
                                <Button severity="danger" @click="confirmDelete(slotProps.data.id, 'Clase', 'typeShips')"
                                    class="hover:bg-primary">
                                    <i class="fa-solid fa-trash"></i>
                                </Button>
                            </div>
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>
    </AppLayout>

    <CustomModal :visible="modalVisible">
        <template #icon>
            <i class="text-white fa-solid fa-ship"></i>
        </template>
        <template #titulo>
            <span class="text-xl font-bold text-white white-space-nowrap">
                {{ modalType == 'new' ? 'Nueva clase' : 'Editar clase' }}</span>
        </template>
        <template #body>
            <div class="grid grid-cols-4 gap-2 px-1 pt-4">
                <TextInput label="Nombre" type="text" :showSup="true" v-model="typeShip.name" />
                <TextInput label="Tipo de buque" type="text" v-model="typeShip.type" />
                <TextInput label="Empresa diseñadora" type="text" v-model="typeShip.disinger" />
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-900 capitalize" for="hull_material">
                        Material Casco</label>
                    <Dropdown id="hull_material" v-model="typeShip.hull_material" :options="hull_materials"
                        optionLabel="name" placeholder="Selecciona un material" class="w-full rounded-md md:w-14rem" :pt="{
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
                <FileUpload mode="basic" :multiple="false" accept="image/*" class="w-full" :maxFileSize="1000000"
                    @select="typeShip.image = $event.files[0]" v-model="typeShip.image" :showCancelButton="false"
                    :showUploadButton="false" chooseLabel="Agregar imagen" :pt="{
                        root: { class: '' }
                    }">
                </FileUpload>
                <Image v-if="typeShip.image != null" id="verFoto" alt="imagen" :src="verfoto(typeShip.image)" preview
                    height="170" width="190" />

            </div>
        </template>
        <template #footer>
            <Button severity="primary" @click="save()" v-if="modalType == 'new'" class="hover:bg-danger">
                <i class="fa-solid fa-floppy-disk"></i>
                <p>Guardar</p>
            </Button>
            <Button severity="primary" @click="edit()" v-else class="hover:bg-danger">
                <i class="fa-solid fa-floppy-disk"></i>
                <p>Guardar</p>
            </Button>
            <Button severity="danger" @click="modalVisible = false" class="hover:bg-danger">
                <i class="fa-regular fa-circle-xmark"></i>
                <p>Cancelar</p>
            </Button>
        </template>
    </CustomModal>
</template>