<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import GraphicStatus from "./GraphicStatus.vue"
import Inventory from "./Inventory.vue"
import UserHeader from "@/Components/sections/UserHeader.vue";
import { onMounted, ref } from "vue";
import PieChart from '@/Pages/Dashboards/PieChart.vue'
const pieChartSerie = ref([])
const statues = ref([])

const title = ref({
    text: 'Disponiblidad',
    left: 'center'
})

const getDataStatusAssigment = async () => {
    await axios.get(route('get.tool.assigment.status'))
        .then((res) => {
            statues.value = res.data.tools
            pieChartSerie.value.push({
                type: 'pie',
                radius: '55%',
                center: ['50%', '55%'],
                color: ['#2E3092', '#81BE50'],
                is3D: true,
                data: [{
                    value: statues.value[0].value,
                    name: statues.value[0].status,
                    label: {
                        formatter: '{b|{b}}{abg|}\n{hr|}\n  {c}  {per|{d}%}',
                        backgroundColor: '#fff5f5',
                        borderColor: '#8C8D8E',
                        borderWidth: 1,
                        borderRadius: 4,
                        rich: {
                            a: {
                                color: '#6E7079',
                                lineHeight: 22,
                                align: 'center'
                            },
                            hr: {
                                borderColor: '#8C8D8E',
                                width: '100%',
                                borderWidth: 1,
                                height: 0
                            },
                            b: {
                                align: 'center',
                                color: '#6E7079',
                                fontSize: 8,
                                fontWeight: 'bold',
                                padding: [2, 2],
                            },
                            c: {
                                fontWeight: 'bold',
                            },
                            per: {
                                color: '#fff',
                                backgroundColor: '#4C5058',
                                padding: [2, 2],
                                fontSize: 10,
                            }
                        }
                    },

                },
                {
                    value: statues.value[1].value,
                    name: statues.value[1].status,
                    label: {
                        formatter: '{b|{b}}{abg|}\n{hr|}\n  {c}  {per|{d}%}',
                        backgroundColor: '#ddf5f0',
                        borderColor: '#8C8D8E',
                        borderWidth: 1,
                        borderRadius: 4,
                        rich: {
                            a: {
                                color: '#6E7079',
                                lineHeight: 22,
                                align: 'center'
                            },
                            hr: {
                                borderColor: '#8C8D8E',
                                width: '100%',
                                borderWidth: 1,
                                height: 0
                            },
                            b: {
                                align: 'center',
                                color: '#6E7079',
                                fontSize: 8,
                                fontWeight: 'bold',
                                padding: [2, 2],
                            },
                            c: {
                                fontWeight: 'bold',
                            },
                            per: {
                                color: '#fff',
                                backgroundColor: '#4C5058',
                                padding: [2, 2],
                                fontSize: 10,
                            }
                        }
                    },

                }]
            }

            )

            // legends.value.push(chartItemsRender.name)

        })
}
onMounted(() => {
    getDataStatusAssigment()
    // getImagesFiles()
})
</script>
<template>
    <AppLayout :urls="urls">
        <div class="w-full h-full overflow-y-auto space-y-4">
            <UserHeader />
            <div class="grid grid-cols-3 gap-4 w-full">
                <PieChart :series="pieChartSerie" :title class="shadow-md rounded-md" />
                <GraphicStatus class="shadow-md rounded-md" />
                <Inventory class="shadow-md rounded-md col-span-1" />
            </div>
        </div>
    </AppLayout>

</template>