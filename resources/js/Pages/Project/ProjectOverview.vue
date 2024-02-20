<script setup>
import { ref, onMounted } from 'vue'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import Moment from 'moment'
import MiniCardInfo from '@/Components/MiniCardInfo.vue'
import DescriptionItem from '@/Components/DescriptionItem.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Loading from '@/Components/Loading.vue'
import Bar from '@/Pages/Dashboards/Projects/Bar.vue'
import S_Curve from '@/Pages/Dashboards/Projects/S_Curve.vue'
import GaugeGradeChart from '@/Pages/Dashboards/Projects/GaugeGradeChart.vue'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import Image from 'primevue/image'
import html2canvas from 'html2canvas'
import Tag from 'primevue/tag'

const props = defineProps({
  project: Object,
  ships: Array,
  semana: Object
})

const budge = ref({
})

onMounted(() => {
  getBudges()
})

const loadingDashboard = ref(true)
const showDashboard = ref(false)

const showLineChart = ref(0)

const contractLabel = [
  'No. de Contrato',
  'Estado',
  'Cliente',
  'Precio de Venta',
  'Fecha de Inicio',
  'Fecha Finalización',
  'Tipo de Venta',
]

const contractField = [
  'contract_id',
  'state',
  'customer_id',
  'price',
  'start_date',
  'end_date',
  'type_of_sale',
]

const shipLabel = [
  'Tipo de Buque',
  'Empresa Diseñadora',
  'Material del Casco',
  'Tipo de Propulsión',
  'Alcance',
  'Autonomía',
  'GT',
  'CGT',
  'Eslora',
  'Manga',
  'Tripulación Máxima',
  'Light Ship',
  'Full Load',
  'Diseño de Calado',
  'Bollard Pull',
  'Puntal',
  'Velocidad Máxima',
  'Potencia'
]

const shipField = [
  'type',
  'disinger',
  'hull_material',
  'propulsion_type',
  'autonomy',
  'autonomias',
  'GT',
  'CGT',
  'length',
  'breadth',
  'crew',
  'light_ship',
  'full_load',
  'draught',
  'bollard_pull',
  'depth',
  'velocity',
  'power_total'
]


const selectedImage = ref(props.ships[0]?.file ?? props.ships[0].type_ship.render)

const severitys = [
  { text: 'DISEÑO Y CONSTRUCCIÓN', severity: 'primary', class: '' },
  { text: 'CONSTRUCCIÓN', severity: 'success', class: '' },
  { text: 'DISEÑO', severity: 'info', class: '' },
  { text: 'GARANTIA', severity: 'warning', class: '' },
  { text: 'SERVICIO POSTVENTA', severity: 'success', class: '' },
  { text: 'SIN ESTADO', severity: 'danger', class: 'animate-pulse' }
]

const formatCurrency = (valor, moneda) => {
  if (valor == undefined || valor == null) {
    return 'Sin definir'
  } else {
    return parseInt(valor).toLocaleString('es-CO',
      { style: 'currency', currency: moneda == null ? 'COP' : moneda, maximumFractionDigits: 0 })
  }
}

const getBudges = async () => {
  loadingDashboard.value = true
  await axios.get(route('budget.project', props.project.id))
    .then((res) => {
      budge.value = res.data
      showLineChart.value++
      loadingDashboard.value = false
    })
}

const captureAndDownloadImage = async () => {
  showLineChart.value++
  const contentToCapture = document.getElementById('contentToCapture')
  await html2canvas(contentToCapture, {
    windowWidth: 1280,
    windowHeight: 720,
  }).then(canvas => {
    const imageUrl = canvas.toDataURL('image/png')
    const link = document.createElement('a')
    link.href = imageUrl
    link.download = 'captura_de_pantalla.png'
    link.click()
  })
}

const getDays = (startDate, endData) => {
  let date_1 = new Date(startDate)
  let date_2 = new Date(endData)

  // Calcular la diferencia en milisegundos
  let diffMiliseconds = date_2 - date_1

  // Calcular la diferencia en días
  let milisecondsPerDay = 24 * 60 * 60 * 1000; // Número de milisegundos en un día
  return Math.round(diffMiliseconds / milisecondsPerDay) < 0 ? 0 : Math.round(diffMiliseconds / milisecondsPerDay)
}

const calculatePercentage = (data, total) => {
  let percentage = (data / total) * 100;

  if (percentage > 100) {
    percentage = 100;
  }

  if (percentage < 0) {
    percentage = 0;
  }

  return percentage.toFixed(0) > 100 ? 0 : percentage.toFixed(0);
}

const facturado = props.project.milestone.filter(hito => hito.advance == 100)
  .reduce((sum, hito) => sum + parseInt(hito.value), 0);
const porFacturar = props.project.milestone.filter(hito => hito.advance != 100)
  .reduce((sum, hito) => sum + parseInt(hito.value), 0);

const panelClass = (props, parent, index) => {
  return [
    {
      '!bg-blue-200': parent.state.d_activeIndex === index
    }
  ]
}

const toggleTabClicked = (event) => {
  setTimeout(() => {
    showDashboard.value = true
  }, 1)
}

const handleTabClick = (event) => {
  // console.log(props.ships[event.index].file)
  selectedImage.value = props.ships[event.index]?.file ?? props.ships[event.index].type_ship.render
}
</script>
<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  overflow-y: auto;
}

th,
td {
  border: 1px solid black;
  padding: 1px;
  text-align: center;
  font-size: .85rem;
}
</style>
<template>
  <main class="flex flex-col max-w-full justify-center min-h-screen overflow-hidden">
    <section class="space-y-6  pt-0.5 md:space-x-6 h-screen">
      <!--Project Details-->
      <TabView @tab-click="toggleTabClicked($event)" :scrollable="true" :pt="{
        nav: '!flex !justify-between'
      }">
        <TabPanel header="Información del Proyecto" :activeIndex="0" :pt="{
          root: 'w-full',
        }">
          <div class="grid grid-cols-2 gap-2 h-[90vh]">
            <div class="col-span-1 rounded-lg p-2 mb-2 w-full border border-solid">
              <div class="flex text-sm font-medium justify-center items-center">
                <i class="fa-solid fa-ship "></i>
                <h1 class="text-lg font-semibold  px-4 uppercase">
                  {{ project.name }} - {{ project.SAP_code }}
                </h1>
              </div>
              <div class="flex justify-center items-center">
              </div>
              <dl class="divide-y flex space-x-4 divide-gray-200 border-b border-t border-gray-200 items-center">
                <Tag :severity="severitys.find((severity) => severity.text == project.status).severity"
                  :value="'EN ' + project.status"></Tag>
                <div class="flex py-3 text-xs font-medium">
                  <dt class="">Alcance:</dt>
                  <dd class="text-gray-400 uppercase">&nbsp{{ project.scope }}</dd>
                </div>
                <div class="flex py-3 text-xs font-medium">
                  <dt class="">Tipo de Proyecto:</dt>
                  <dd class="text-gray-400 uppercase">&nbsp{{ project.type }}</dd>
                </div>
              </dl>
              <div>
                <h2 class="text-sm  italic">
                  {{ project.description }}
                </h2>
              </div>
              <div class="flex7 size-full justify-center">
                <Image :src="selectedImage" v-if="selectedImage != '/'" alt="image" preview />
                <div v-else class="flex justify-center mt-10">
                  <ApplicationLogo height="350" width="350" />
                </div>
              </div>
            </div>
            <TabView :scrollable="true" :pt="{
              nav: '!flex !justify-between'
            }
              ">
              <!-- INFORMACIÓN DEL PROYECTO -->
              <TabPanel header="Información del Contrato" :pt="{
                root: 'w-full',
              }">
                <div class="w-full p-2 first-letter:uppercase text-justify ">
                  <p>{{ project.contract.subject ? project.contract.subject : 'SIN DEFINIR' }}
                  </p>
                </div>
                <div class="border border-solid rounded-lg p-2 mb-2">
                  <DescriptionItem :data="project.contract" :label="contractLabel" :field="contractField" />
                </div>
              </TabPanel>

              <!-- BUQUES -->
              <TabPanel header="Buques" :pt="{
                root: '!w-full ',
                content: '!h-[80vh] !p-2 !overflow-y-auto'
              }">
                <Accordion @tab-click="handleTabClick($event)">
                  <AccordionTab :activeIndex="0" v-for="(ship, index) in ships " :key="ship.id" :pt="{
                    headerAction: ({ props, parent }) => ({
                      class: ['!sticky !top-0', panelClass(props, parent, index)]
                    })
                  }">
                    <template #header>
                      <div class=" align-items-center gap-2 w-full block space-y-2">
                        <span class="font-bold white-space-nowrap">{{ index + 1 }}. {{ ship.name }}</span>
                        <div class="flex justify-start space-x-2">
                          <Tag v-tooltip.bottom="'Numero de Casco'" :value="ship.idHull" severity="info"></Tag>
                          <Tag v-tooltip.bottom="'Clase'" :value="ship.type_ship.name" severity="success"></Tag>
                          <Tag v-tooltip.bottom="'Tipo de Buque'" :value="ship.type_ship.type" severity="primary"></Tag>
                        </div>
                      </div>
                    </template>
                    <div class="border border-solid rounded-lg p-2 mb-2">
                      <DescriptionItem :data="ship.type_ship" :label="shipLabel" :field="shipField" />
                    </div>
                  </AccordionTab>
                </Accordion>
              </TabPanel>
            </TabView>
          </div>
        </TabPanel>
        <TabPanel v-if="semana" header="Dashboard" :activeIndex="1" :pt="{
          root: '!w-full !bottom-0'
        }
          ">
          <!-- <span id="contentToCapture" class="w-full"> -->
          <article v-if="loadingDashboard" class="h-screen w-full flex flex-col justify-center items-center col-span-6">
            <Loading message="Generando Dashboard" />
          </article>
          <article v-if="showDashboard">
            <!-- TABLAS -->
            <div class="block md:flex justify-between pb-1">
              <!--TABLA 1-->
              <div class="w-full md:w-2/3 grid grid-cols-4 text-xs rounded-xl">
                <!-- primera fila -->
                <div class="col-span-2 border text-center border-gray-800 bg-gray-100">Gerente: {{ project.supervisor }}
                </div>
                <div class="border text-center border-gray-800 bg-sky-100 font-bold">N° CONTRATO</div>
                <div class="border text-center border-gray-800"
                  :class="project.contract.contract_id == null ? 'bg-red-400' : ''">{{
                    project.contract?.contract_id ?? 'Sin Contrato Asignado' }}
                </div>

                <!-- Segunda fila -->
                <div class="border text-center border-gray-800 bg-gray-100">FECHA REPORTE: </div>
                <div class="border text-center border-gray-800"> {{
                  Moment(project.contract.start_date).format('DD/MM/YYYY') }}</div>
                <div class="border text-center border-gray-800 bg-sky-100 font-bold">FECHA DE INICIO </div>
                <div class="border text-center border-gray-800">{{
                  Moment(project.contract.start_date).format('DD/MM/YYYY') }}</div>

                <!-- Tercera fila -->
                <div class="border text-center border-gray-800 bg-gray-100">SEMANA: </div>
                <div class="border text-center border-gray-800"> {{ semana.week }}</div>
                <div class="border text-center border-gray-800 bg-sky-100 font-bold">FECHA DE FIN </div>
                <div class="border text-center border-gray-800">{{
                  Moment(project.contract.end_date).format('DD/MM/YYYY')
                }}</div>
              </div>

              <!--TABLA 2-->
              <div class="w-full md:w-1/3 grid grid-cols-4 text-xs rounded-xl md:mx-2 mt-4 md:mt-0 "
                v-if="project.contract.start_date">
                <!--  Primera fila -->
                <div class="border text-center font-bold bg-sky-100 border-gray-800 col-span-2 ">DIAS EJECUTADOS </div>
                <div class="border text-center border-gray-800">
                  {{ getDays(project.contract.start_date, new Date()) }}
                </div>
                <div class="border text-center border-gray-800">
                  <div
                    class="bg-sky-300 text-xs align-self-center font-extrabold opacity-60 h-full text-black text-center p-0.5"
                    :style="'width: ' + calculatePercentage(getDays(project.contract.start_date, new Date()),
                      getDays(project.contract.start_date, project.contract.end_date)) + '%'
                      ">
                    {{ calculatePercentage(getDays(project.contract.start_date, new Date()),
                      getDays(project.contract.start_date, project.contract.end_date)) }}%
                  </div>
                </div>
                <!-- Segunda fila -->
                <div class="border text-center font-bold bg-sky-100 border-gray-800 col-span-2">DIAS RESTANTES </div>
                <div class="border text-center border-gray-800">
                  {{ getDays(new Date(), project.contract.end_date) }}
                </div>
                <div class="border text-center border-gray-800 ">
                  <div
                    class="bg-teal-900 text-xs align-self-center font-extrabold opacity-60 h-full text-white text-center p-0.5"
                    :style="'width: ' + calculatePercentage(getDays(new Date(), project.contract.end_date),
                      getDays(project.contract.start_date, project.contract.end_date)) + '%;color:black;'
                      ">
                    {{ calculatePercentage(getDays(new Date(), project.contract.end_date),
                      getDays(project.contract.start_date, project.contract.end_date)) }}%
                  </div>
                </div>
                <!-- Tercera fila -->
                <div class="border text-center font-bold bg-sky-100 border-gray-800 col-span-2 ">TOTAL DIAS PROYECTO
                </div>
                <div class="border text-center border-gray-800 col-span-2">
                  {{ getDays(project.contract.start_date, project.contract.end_date) }}
                </div>
              </div>
            </div>
            <!-- OTROS DATOS -->
            <div class="grid grid-cols-2 gap-2">
              <div class="border border-b-gray-300 rounded-lg shadow-xs">
                <div class="flex justify-center items-center p-0.5 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">GESTIÓN DEL CRONOGRAMA</h2>
                </div>
                <Bar :key="showLineChart" :planeado='semana.planned_progress' :real="semana.real_progress" />
                <div class="flex justify-center w-full">
                  <GaugeGradeChart :key="showLineChart" title="SPI" :value="semana.SPI" />
                  <S_Curve :project="project.id" :key="showLineChart" />
                </div>
              </div>

              <!-- TABLA: GESTIÓN DE LOS COSTOS -->
              <article>
                <div class="flex justify-center items-center p-0.5 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">GESTIÓN DE LOS COSTOS</h2>
                </div>
                <!-- MINICARDS INFO -->
                <div class="grid grid-cols-2 w-full">
                  <div class="grid grid-cols-2 gap-2 mb-2">
                    <MiniCardInfo title="Presupuesto" :value="budge.total" :valueProgressBar="50" />
                    <MiniCardInfo title="Ejecutado" :value="budge.materials_ejecutados + budge.labor_ejecutados +
                      budge.services_ejecutados" :valueProgressBar="35" />
                    <MiniCardInfo title="Disponible" :value="budge.total - (budge.materials_ejecutados + budge.labor_ejecutados +
                      budge.services_ejecutados)" :valueProgressBar="10" />
                  </div>
                  <GaugeGradeChart :key="showLineChart" title="CPI" :value="semana.CPI" />
                </div>
                <div>
                  <table>
                    <thead>
                      <tr>
                        <th class="bg-slate-300"></th>
                        <th class="bg-sky-100">MATERIALES</th>
                        <th class="bg-sky-100">MANO DE OBRA</th>
                        <th class="bg-sky-100">SERVICIOS</th>
                        <th class="bg-sky-100">TOTAL</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-left font-semibold bg-sky-100">PRESUPUESTO</td>
                        <td>{{ formatCurrency(budge.materials) }}</td>
                        <td>{{ formatCurrency(budge.labor) }}</td>
                        <td>{{ formatCurrency(budge.services) }}</td>
                        <td>{{ formatCurrency(budge.total) }}</td>
                      </tr>
                      <tr>
                        <td class="text-left font-semibold bg-sky-100">CONSUMO ACTUAL</td>
                        <td>{{ formatCurrency(budge.materials_ejecutados) }}</td>
                        <td>{{ formatCurrency(budge.labor_ejecutados) }}</td>
                        <td>{{ formatCurrency(budge.services_ejecutados) }}</td>
                        <td>{{ formatCurrency(budge.materials_ejecutados + budge.labor_ejecutados +
                          budge.services_ejecutados) }}</td>
                      </tr>
                      <tr>
                        <td class="text-left font-semibold bg-sky-100">DISPONIBLE</td>
                        <td class="font-bold"
                          :class="budge.materials - budge.materials_ejecutados < 0 ? 'text-danger' : 'text-success'">{{
                            formatCurrency(budge.materials - budge.materials_ejecutados)
                          }}</td>
                        <td class="font-bold"
                          :class="budge.labor - budge.labor_ejecutados < 0 ? 'text-danger' : 'text-success'">{{
                            formatCurrency(budge.labor - budge.labor_ejecutados) }}</td>
                        <td class="font-bold"
                          :class="budge.services - budge.services_ejecutados < 0 ? 'text-danger' : 'text-success'">{{
                            formatCurrency(budge.services - budge.services_ejecutados)
                          }}</td>
                        <td class="font-bold text-success">{{ formatCurrency(budge.total -
                          (budge.materials_ejecutados +
                            budge.labor_ejecutados +
                            budge.services_ejecutados)) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </article>

              <!-- TABLA: ACTIVIDADES/NOVEDADES DE LA SEMANA -->
              <article>
                <div class="flex justify-center items-center p-0.5 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">ACTIVIDADES/NOVEDADES DE LA SEMANA</h2>
                </div>
              </article>

              <!-- TABLA: HITOS CONTRACTUALES -->
              <article class="overflow-y-auto">
                <div class="flex justify-center items-center p-0.5  bg-blue-800 text-white">
                  <h2 class="font-semibold">HITOS CONTRACTUALES</h2>
                </div>
                <p class="w-full text-sm text-center text-primary bg-yellow-100 italic my-1 font-bold">
                  {{ formatCurrency(facturado) }} Facturados
                </p>
                <table>
                  <thead>
                    <tr>
                      <th class="uppercase bg-sky-100" style="width:27rem">Hitos</th>
                      <th class="uppercase bg-sky-100">Fecha</th>
                      <th class="uppercase bg-sky-100">Monto</th>
                    </tr>
                  </thead>
                  <tbody v-for="hito in project.milestone">
                    <tr v-if="hito.advance != 100">
                      <td class="text-left font-semibold ">
                        {{ hito.title }}
                      </td>
                      <td>{{ Moment().format(hito.end_date) }}</td>
                      <td>{{ formatCurrency(hito.value, 'COP') }}</td>
                    </tr>
                  </tbody>
                </table>
                <p class="w-full text-sm text-center text-primary bg-yellow-100 italic my-1 font-bold">
                  {{ formatCurrency(porFacturar) }} Por Facturar
                </p>
              </article>
            </div>
            <!-- </span> -->
          </article>
        </TabPanel>
      </TabView>
    </section>
  </main>
  <!-- <Footer fontSize="sm" fontColor="white" marginTop="0" /> -->
</template>
