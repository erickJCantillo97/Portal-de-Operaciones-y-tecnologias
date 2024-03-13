<script setup>
import { ref } from 'vue';
import MultiSelect from 'primevue/multiselect';
import ProjectCard from '@/Components/ProjectCard.vue';

const props = defineProps({
    projects: Array,
})

var datos = [];

const getRandomColor = () => {
    const getRandomNumber = (maxNum) => {
        return Math.floor(Math.random() * maxNum);
    };

    const h = getRandomNumber(360);
    const s = getRandomNumber(100);
    const l = getRandomNumber(100);

    return `hsl(${h}deg, ${s}%, ${l}%)`;
};

props.projects.forEach(project => {
    const randomColor = getRandomColor();
    let a = {
        x: project.name.split(' '),
        y: [
            new Date(project.fechaI).getTime(),
            new Date(project.fechaF).getTime()
        ],
        fillColor: randomColor
    }
    datos.push(a)
});
const series = [{
    data: datos
}]

const chartOptions = {
    chart: {
        height: 350,
        type: 'rangeBar',
        defaultLocale: 'es',
        locales: [{
            name: 'es',
            options: {
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octuber', 'Noviembre', 'Dicembre'],
                shortMonths: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                days: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                shortDays: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                toolbar: {
                    download: 'Descargar SVG',
                    selection: 'Seleccion',
                    selectionZoom: 'Selecionar Zoom',
                    zoomIn: 'Acercarse',
                    zoomOut: 'Alejarse',
                    pan: 'Arrastrar',
                    reset: 'Reiniciar Zoom',
                }
            }
        }]
    },
    plotOptions: {
        bar: {
            horizontal: true,
        }
    },
    xaxis: {
        type: 'datetime',
        style: {
            colors: [],
            fontSize: '12px',
            fontWeight: 400,
            fontFamily: undefined,
            cssClass: ''
        },
        labels: {
            formatter: function (value) {
                const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octuber', 'Noviembre', 'Dicembre'];
                return months[new Date(value).getMonth()];
            }
        }
    },
    yaxis: {
        show: true,
    },


}
// const id = ref(null)
const tasks = ref();
const loading = ref(true)
const tasksSelect = ref()

const getTasks = (project) => {
    console.log(project)
    axios.get(route('tasks.index', { id: project.project_id })).then((res) => {
        loading.value = false;
        tasks.value = res.data.tasks
        console.log(tasks.value)
        // projects.value = [...new Set(res.data.map(obj => obj.project.id))]
    })
}
</script>
<template>
    <div class="max-w-full p-3 m-3 border-2 border-blue-100 rounded-xl">
        <h3 class="text-xl font-medium text-primary">Seleciona un proyecto</h3>
        <dl class="grid grid-cols-1 gap-5 mt-5 sm:grid-cols-2 lg:grid-cols-4">
            <ProjectCard v-for="project in projects" :project=project class="cursor-pointer" @click="getTasks(project)"
                :activo="false" />
        </dl>
    </div>
    <div class="w-2/3 p-3 m-3 border-2 border-blue-100 rounded-xl">
        <h3 class="text-xl font-medium text-primary">Seleciona una actividad</h3>
        <dl class="grid grid-cols-1 gap-5 mt-5 sm:grid-cols-2 lg:grid-cols-4">
            <MultiSelect v-model="tasksSelect" :options="tasks" optionLabel="name" placeholder="Select Countries"
                display="chip" class="w-full md:w-20rem">
                <template #option="slotProps">
                    <div class="flex align-items-center">
                        <!-- <img :alt="slotProps.option.name"
                            src="https://primefaces.org/cdn/primevue/images/flag/flag_placeholder.png"
                            :class="`flag flag-${slotProps.option.code.toLowerCase()} mr-2`" style="width: 18px" /> -->
                        <div>{{ slotProps.option.name }}</div>
                    </div>
                </template>
                <template #footer>
                    <div class="py-2 px-3">
                        <b>{{ tasksSelect ? tasksSelect.length : 0 }}</b> item{{ (tasksSelect ?
                tasksSelect.length : 0) > 1 ? 's' : '' }} selected.
                    </div>
                </template>
            </MultiSelect>
        </dl>
        <!-- <ProjectCard :project="slotProps.item" :activo="false" :menu="false" /> -->
    </div>
    <div class="max-w-full p-3 m-3 border-2 border-blue-100 rounded-xl">
        <div class="relative">
            <apexchart type="rangeBar" height="350" :options="chartOptions" :series="series"></apexchart>
        </div>
    </div>
</template>
