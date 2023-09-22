<script setup>
import { ref } from 'vue';
const props = defineProps({
    projects: Array,
})
const project = ref();
// import 'https://www.gstatic.com/charts/loader.js'
// Load the Visualization API and the corechart package.
google.charts.load('current', { 'packages': ['gantt'] });

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);


// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and
// draws it.
function drawChart() {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Task ID');
    data.addColumn('string', 'Task Name');
    data.addColumn('string', 'Resource');
    data.addColumn('date', 'Start Date');
    data.addColumn('date', 'End Date');
    data.addColumn('number', 'Duration');
    data.addColumn('number', 'Percent Complete');
    data.addColumn('string', 'Dependencies');
    props.projects.forEach(element => {
        data.addRows([
            [String(element.id), String(element.name), null,
            new Date(element.fechaI), new Date(element.fechaF), null, parseInt(element.avance), null],
        ])
    });

    var options = {
        height: 400,
        gantt: {
            trackHeight: 30
        }
    };

    var chart = new google.visualization.Gantt(document.getElementById('chart_div'));

    chart.draw(data, options);
}
</script>

<template>
    <div class="w-full p-4 font-extrabold text-center text-black bg-gradient-to-b from-blue-400 to-slate-50">
        <h2 class="text-xl font-extrabold ">Linea de tiempo proyectos activos</h2>
    </div>
    <div id="chart_div"></div>
</template>
