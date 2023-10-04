<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { FilterMatchMode } from 'primevue/api';
import Button from '@/Components/Button.vue';
import Calendar from 'primevue/calendar';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InputText from 'primevue/inputtext';
import '../../../sass/dataTableCustomized.scss';
import Avatars from '@/Components/Avatars.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import MinimalMenu from '@/Components/MinimalMenu.vue';
import { ChevronRightIcon } from '@heroicons/vue/20/solid'
import { Container, Draggable } from "vue-dndrop";
import { applyDrag, generateItems } from "@/composable/helpers.js";

//#region Draggable
const data = ref({
    items1: generateItems(15, (i) => ({
        id: "1" + i,
        data: `Source Draggable - ${i}`
    })),
    items2: generateItems(15, (i) => ({
        id: "1" + i,
        data: `Source Draggable - ${i}`
    })),
    items3: generateItems(15, (i) => ({
        id: "1" + i,
        data: `Source Draggable - ${i}`
    })),
})

const onDrop = (collection, dropResult) => {
    console.log(data.value)
    data.value[collection] = applyDrag(data.value[collection], dropResult);
}

const getChildPayload1 = (index) => {
    console.log(data.value)
    return data.value.items1[index];
}

const getChildPayload2 = (index) => {
    return data.value.items2[index];
}





//#endregion

const open = ref(true)

const unidad = {
    day: 'Dias',
    hour: 'Horas'
};

const projects = ref()

onMounted(() => {
    getTask('today')
})

const dates = ref([]);
const tasks = ref([]);
const loading = ref(false);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'project.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
    'project.id': { value: null, matchMode: FilterMatchMode.EQUALS },
    'manager': { value: null, matchMode: FilterMatchMode.CONTAINS },
    'tasks': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const optionValue = ref('today')

function obtenerLunesYViernesSemanaActual() {
    const hoy = new Date();
    const diaSemana = hoy.getDay(); // 0 para domingo, 1 para lunes, ..., 6 para sábado

    // Calcula la fecha del lunes de la semana actual
    const lunes = new Date(hoy);
    lunes.setDate(hoy.getDate() - diaSemana + (diaSemana === 0 ? -6 : 1));

    // Calcula la fecha del viernes de la semana actual
    const viernes = new Date(lunes);
    viernes.setDate(lunes.getDate() + 4);


    return {
        lunes: lunes,
        viernes: viernes
    };
}

const getTask = (option) => {

    const today = new Date();
    optionValue.value = option
    switch (option) {
        case 'today':
            dates.value[0] = today;
            dates.value[1] = today;
            break;
        case 'tomorrow':
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dates.value[0] = tomorrow;
            dates.value[1] = tomorrow;
            break;

        case 'week':
            const week = new Date();
            const fechasSemanaActual = obtenerLunesYViernesSemanaActual();
            week.setDate(week.getDate() + 7);
            dates.value[0] = fechasSemanaActual.lunes;
            dates.value[1] = fechasSemanaActual.viernes;
            break;
        default:
            break;
    }

    if (dates.value[1] != null) {
        tasks.value = [];
        loading.value = true;

        axios.get(route('actividadesDeultimonivel', { dates: dates.value })).then((res) => {
            loading.value = false;
            tasks.value = res.data
            projects.value = [...new Set(res.data.map(obj => obj.project.id))]
        })
    }

}

const redondear = (value) => {
    return new Intl.NumberFormat().format(Number(value).toFixed(2))
}

const filterProject = (id) => {
    filters.value['project.id'].value = id;
}

const clearFilter = () => {
    filters();
};

//#region
const items = ref([
    {
        label: 'Añadir recursos',
        icon: 'fa-solid fa-chart-gantt',
        url: {
            name: 'createSchedule.create',
            parametter: ''
        }
    },
    {
        label: 'Asignar colaborador',
        icon: 'fa-solid fa-arrows-down-to-people',
        url: {
            name: 'createSchedule.create',
            parametter: ''
        }
    },

    {
        label: 'Ver programacion',
        icon: 'fa-solid fa-list-check',
        url: {
            name: 'programming',
            parametter: ''
        }
    },
]);

//#endregion

</script>
<style scoped>
.copy {
    display: flex;
    gap: 1rem;
}

.item {
    flex: 1;
}
</style>

<template>
    <AppLayout>
        <div class="w-full md:p-4 px-auto">
            <div class="flex items-center mx-2 mb-2">
                <div class="flex-auto">
                    <h1 class="text-xl font-semibold leading-6 capitalize text-primary">
                        Programación de Actividades
                    </h1>
                </div>
            </div>
            <div>
                <div class="flex items-center my-4 space-x-2">
                    <div class="hidden w-1/12 md:block">
                        <Button icon="pi pi-filter-slash shadow-xl" @click="filterProject()" type="button" text=""
                            severity="primary" class="hover:bg-primary ">
                            Ver Todos
                        </Button>
                    </div>
                    <div class="flex w-11/12 p-1 space-x-2 overflow-x-auto shadow-sm shadow-primary rounded-xl snap-x">
                        <ProjectCard v-for="project in projects" :projectId=project :menu=false clases="cursor-pointer"
                            @click="filterProject(project)" :activo="filters['project.id'].value == project" />
                    </div>
                </div>

            </div>
            <div class="grid grid-cols-2 space-x-4">
                
                <div class="copy">
                     <div class="item">
                            <Container group-name="1" :get-child-payload="getChildPayload2" @drop="onDrop('items2', $event)">
                                <Draggable v-for="item in data.items2" :key="item.id">
                                    <div class="draggable-item">
                                        {{ item.data }}
                                    </div>
                                </Draggable>
                            </Container>
                        </div>
                    <div class="item">
                        <Container class="item" behaviour="copy" group-name="1" :get-child-payload="getChildPayload1">
                            <Draggable v-for="item in data.items1" :key="item.id">
                                <div class="draggable-item">
                                    {{ item.data }}
                                </div>
                            </Draggable>
                        </Container>
                    </div>
                   
                </div>
            </div>

            <!-- <div>
                <ul role="list"
                    class="overflow-hidden bg-white divide-y divide-gray-100 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                    <li v-for="person in people" :key="person.email"
                        class="relative flex justify-between px-4 py-5 gap-x-6 hover:bg-gray-50 sm:px-6">
                        <div class="flex min-w-0 gap-x-4">
                            <img class="flex-none w-12 h-12 rounded-full bg-gray-50" :src="person.imageUrl" alt="" />
                            <div class="flex-auto min-w-0">
                                <p class="text-sm font-semibold leading-6 text-gray-900">
                                    <a :href="person.href">
                                        <span class="absolute inset-x-0 bottom-0 -top-px" />
                                        {{ person.name }}
                                    </a>
                                </p>
                                <p class="flex mt-1 text-xs leading-5 text-gray-500">
                                    <a :href="`mailto:${person.email}`" class="relative truncate hover:underline">{{
                                        person.email }}</a>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center shrink-0 gap-x-4">
                            <div class="hidden sm:flex sm:flex-col sm:items-end">
                                <p class="text-sm leading-6 text-gray-900">{{ person.role }}</p>
                                <p v-if="person.lastSeen" class="mt-1 text-xs leading-5 text-gray-500">
                                    Last seen <time :datetime="person.lastSeenDateTime">{{ person.lastSeen }}</time>
                                </p>
                                <div v-else class="mt-1 flex items-center gap-x-1.5">
                                    <div class="flex-none p-1 rounded-full bg-emerald-500/20">
                                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500">Online</p>
                                </div>
                            </div>
                            <ChevronRightIcon class="flex-none w-5 h-5 text-gray-400" aria-hidden="true" />
                        </div>
                    </li>
                </ul>
            </div> -->
        </div>
    </AppLayout>
</template>
