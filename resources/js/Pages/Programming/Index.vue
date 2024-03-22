<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Dropdown from 'primevue/dropdown';
import NoContentToShow from '@/Components/NoContentToShow.vue'
import WeekTable from '@/Pages/Programming/WeekTable.vue'
import CustomInput from '@/Components/CustomInput.vue';
/**
 * The above code is a JavaScript function that takes a time string in 24-hour format (e.g., "13:30")
// and converts it to a 12-hour format with AM/PM indicator. It creates a new Date object with the time
// string appended to a date string ("1970-01-01T") and then uses the toLocaleString method with
// options to format the time in 12-hour format with the 'es-CO' locale and 'h23' hour cycle.
//
 * @param {*} hora
 */
function format24h(hora) {
    return new Date("1970-01-01T" + hora).toLocaleString('es-CO',
        { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' })
}

const props = defineProps({
    projects: Array,
})

const loading = ref(false)
const project = ref()

const divisionsOptions = ref(
    ['GEMAM',
    'GEBOC',
    'DEEST',
    'DEGPM',
    'DEPRO',
    'DVPCP',
    'DVARD',
    'DVSOL',
    'DVMEC',
    'DVPIN',
    'DVELC',
    'DVHAB',
    'DVAIR',
    'DVEAT',
    'DVMOT',
    'DVADQ',
    'DEINE',
    'DEMTO',
    'CLIENTE'
])

//#endregion
</script>
<template>
    <AppLayout>
        <div class="h-full w-full">
            <div class="flex justify-between items-center px-4 h-min">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    <p> Programación de Actividades </p>
                    <!-- <CustomInput type="week" v-model:input="date" @change="getTask('date'); getPersonalData()" /> -->
                </span>
                <div class="flex items-center space-x-2">
                    <span class="flex flex-col sm:flex-row items-center sm:space-x-2">
                        <Dropdown :options="projects" placeholder="Seleccione un proyecto" optionLabel="name"
                            optionValue="id" showClear :filter="true" filterPlaceholder="Buscar proyecto"
                            v-model="project" :pt="{
                            root: '!h-8',
                            input: '!py-0 !flex !items-center !text-sm !font-normal',
                            item: '!py-1 !px-3 !text-sm !font-normal',
                            filterInput: '!h-8'
                        }" />
                    </span>
                    <span class="flex flex-col sm:flex-row items-center sm:space-x-2">
                        <Dropdown :options="divisionsOptions" placeholder="Seleccione una división"
                            showClear :filter="true" filterPlaceholder="Buscar proyecto"
                            v-model="project" :pt="{
                            root: '!h-8',
                            input: '!py-0 !flex !items-center !text-sm !font-normal',
                            item: '!py-1 !px-3 !text-sm !font-normal',
                            filterInput: '!h-8'
                        }" />
                    </span>
                    <CustomInput type="week" placeholder="Seleccione una semana"></CustomInput>
                    <Link :href="route('programming.create')">
                    <Button label="Programar Personal" severity="success" icon="fa-solid fa-plus" :project="project" />
                    </Link>
                </div>
            </div>
            <div class="mt-2 px-4 h-[79vh] overflow-y-auto">
                <div v-if="!project && !loading" class="flex items-center">
                    <NoContentToShow class="mt-5" :subject="'Por favor seleccione un Proyecto'" />
                </div>
                <WeekTable v-else />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-image {
    width: 200px;
    height: 50px;
    /* object-position: 50% 30%; */
    border-radius: 5px 10px;
    object-fit: cover;
    /* Opciones: 'cover', 'contain', 'fill', etc. */
}

.info-resto {
    font-size: 15px;
    text-wrap: balance;
    opacity: .5;
}
</style>
