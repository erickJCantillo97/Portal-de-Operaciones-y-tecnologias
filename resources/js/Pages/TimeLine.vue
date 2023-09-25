<script setup>

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
        x: project.name,
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
}
]

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
            horizontal: true
        }
    },
    xaxis: {
        axisBorder: {
            show: true,
            color: "#008FFB"
        },
        type: 'datetime',
    },
    yaxis: {
        show: false
    },
    dataLabels: {
        enabled: true,
        formatter: function (val, opts) {
            var label = opts.w.globals.labels[opts.dataPointIndex]
            // var a = moment(val[0])
            // var b = moment(val[1])
            // var diff = b.diff(a, 'days')
            console.log(label)
            return label
        },
        style: {
            colors: ['#f3f4f5', '#fff']
        }
    },

}

</script>

<template>
    <div class="relative">
        <apexchart type="rangeBar" height="350" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>
