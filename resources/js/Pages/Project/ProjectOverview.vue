<script setup>
import { ref, onMounted } from 'vue'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import ColumnGroup from 'primevue/columngroup'
import Row from 'primevue/row'
import Moment from 'moment'
import Footer from '@/Components/Footer.vue'
import MiniCardInfo from '@/Components/MiniCardInfo.vue'
import Bar from '@/Pages/Dashboards/Projects/Bar.vue'
import S_Curve from '@/Pages/Dashboards/Projects/S_Curve.vue'
import GaugeGradeChart from '@/Pages/Dashboards/Projects/GaugeGradeChart.vue'

const props = defineProps({
  project: Object
})

onMounted(() => {
  showLineChart.value++
})

const showLineChart = ref(0)
const images = ref()
const imageSrc = [
  {
    id: 1,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria1s.jpg',
    alt: 'image-1'
  },
  {
    id: 2,
    src: 'https://primefaces.org/cdn/primevue/images/galleria/galleria2.jpg',
    thumb: 'https://primefaces.org/cdn/primevue/images/galleria/galleria2s.jpg',
    alt: 'image-1'
  },
]

const responsiveOptions = ref([
  {
    breakpoint: '1300px',
    numVisible: 4
  },
  {
    breakpoint: '575px',
    numVisible: 1
  }
])

const formatCurrency = (valor, moneda) => {
  if (valor == undefined || valor == null) {
    return 'Sin definir'
  } else {
    return parseInt(valor).toLocaleString('es-CO',
      { style: 'currency', currency: moneda == null ? 'COP' : moneda, maximumFractionDigits: 0 })
  }
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
  <main class="flex flex-col max-w-full justify-center bg-gray-800 min-h-screen overflow-hidden">
    <section
      class="grid grid-cols-1 overflow-y-auto space-y-6 bg-white px-6 pt-2 shadow-lg shadow-gray-600 ring-1 ring-gray-900/5  md:space-x-6  md:px-10 h-screen">
      <!--Project Details-->
      <article class="">
        <TabView :scrollable="true" :pt="{
          nav: '!flex !justify-between'
        }">
          <TabPanel header="Información del Proyecto" :pt="{
            root: 'w-full',
          }">
            <div class="grid grid-cols-2 gap-2">
              <div class="col-span-1 rounded-lg p-2 mb-2 w-full bg-gray-800 border border-solid">
                <div class="flex text-sm font-medium justify-center items-center">
                  <i class="fa-solid fa-screwdriver-wrench text-white"></i>
                  <h1 class="text-lg font-semibold text-white px-4 uppercase">
                    {{ project.name }} - {{ project.SAP_code }}
                  </h1>
                </div>
                <div class="flex justify-center items-center">
                  <h2 class="text-sm font-semibold text-blue-500 italic">
                    INFORMACIÓN DEL PROYECTO
                  </h2>
                </div>
                <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                  <div class="flex py-3 text-xs font-medium">
                    <dt class="text-white">No. del Casco:</dt>
                    <dd class="text-gray-400 uppercase">&nbsp;{{ project.manager }}</dd>
                  </div>
                  <div class="flex py-3 text-xs font-medium">
                    <dt class="text-white">Alcance:</dt>
                    <dd class="text-gray-400 uppercase">&nbsp;{{ project.scope }}</dd>
                  </div>
                  <div class="flex py-3 text-xs font-medium">
                    <dt class="text-white">Tipo de Proyecto:</dt>
                    <dd class="text-gray-400 uppercase">&nbsp;{{ project.type }}</dd>
                  </div>
                </dl>
                <div>
                  <h2 class="text-sm text-white italic">
                    {{ project.description }}
                  </h2>
                </div>
                <div class="w-full h-full">
                  <img :src="'https://primefaces.org/cdn/primevue/images/galleria/galleria2.jpg'"
                    onerror="this.src='/public/images/generic-boat.png'" :alt="`${project.file}`"
                    class="object-cover object-center w-full h-[20rem] mr-1" />
                </div>
              </div>
              <TabView :scrollable="true" :pt="{
                nav: '!flex !justify-between'
              }">
                <TabPanel header="Información del Contrato" :pt="{
                  root: 'w-full',
                }">
                  <div class="w-full p-2 first-letter:uppercase text-justify">
                    <p>
                      {{ project.contract.subject }}
                    </p>
                  </div>
                  <div class="custom border border-solid rounded-lg p-2 mb-2">
                    <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">No. del Contrato:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.contract_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Estado del Contrato:</dt>
                        <dd class="p-2 bg-emerald-400 rounded-lg text-white text-xs animate-pulse">{{
                          project.contract.state }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Cliente:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.customer_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Precio de Venta:</dt>
                        <dd class="text-gray-500 uppercase">{{ formatCurrency(project.contract.price, 'COP') }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Fecha de Inicio:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.start_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Fecha de Fin:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.end_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Tipo de Venta:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.type_of_sale }}</dd>
                      </div>
                    </dl>
                  </div>
                </TabPanel>
                <TabPanel header="Buques" :pt="{
                  root: 'w-full',
                  content: '!h-[28rem] !p-2 !overflow-y-auto'
                }">
                  <div class="custom border border-solid rounded-lg p-2 mb-2">
                    <dl class="divide-y divide-gray-200 border-b border-t border-gray-200">
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Tipo de Buque:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.contract_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Empresa Diseñadora:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.state }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Material del Casco:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.customer_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Tipo de Polución:</dt>
                        <dd class="text-gray-500 uppercase">{{ formatCurrency(project.contract.price, 'COP') }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Alcance:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.start_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Autonomía:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.end_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">GT:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.type_of_sale }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">CGT:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.contract_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Eslora:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.state }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Manga:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.customer_id }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Tripulación Máxima:</dt>
                        <dd class="text-gray-500 uppercase">{{ formatCurrency(project.contract.price, 'COP') }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Light Ship:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.start_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Full Load:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.end_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Diseño Calado:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.type_of_sale }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Bollard Pull:</dt>
                        <dd class="text-gray-500 uppercase">{{ formatCurrency(project.contract.price, 'COP') }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Puntal:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.start_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Velocidad Máxima:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.end_date }}</dd>
                      </div>
                      <div class="flex justify-between py-3 text-sm font-medium">
                        <dt class="text-gray-900">Potencia:</dt>
                        <dd class="text-gray-500 uppercase">{{ project.contract.type_of_sale }}</dd>
                      </div>
                    </dl>
                  </div>
                </TabPanel>
              </TabView>
              <!-- <div class="col-span-1 rounded-lg p-2 mb-2 w-full h-full bg-gray-500 border border-solid">
                <p>asasa</p>
              </div> -->
            </div>
          </TabPanel>
          <TabPanel header="Dashboard" :pt="{
            root: 'w-full'
          }">
            <!-- TABLAS -->
            <div class="block md:flex justify-between pb-1">
              <!--TABLA 1-->
              <div class="w-full md:w-2/3 grid grid-cols-4 text-xs rounded-xl">
                <!-- primera fila -->
                <div class="col-span-2 border text-center border-gray-800 bg-gray-100">Gerente: Ronny Gutierrez</div>
                <div class="border text-center border-gray-800 bg-sky-100 font-bold">N° CONTRATO</div>
                <div class="border text-center border-gray-800">{{ project.contract.contract_id }}</div>

                <!-- Segunda fila -->
                <div class="border text-center border-gray-800 bg-gray-100">FECHA REPORTE: </div>
                <div class="border text-center border-gray-800"> {{ Moment().format('DD/MM/YYYY') }}</div>
                <div class="border text-center border-gray-800 bg-sky-100 font-bold">FECHA DE INICIO </div>
                <div class="border text-center border-gray-800">{{
                  Moment(project.contract.start_date).format('DD/MM/YYYY') }}</div>

                <!-- Tercera fila -->
                <div class="border text-center border-gray-800 bg-gray-100">SEMANA: </div>
                <div class="border text-center border-gray-800"> WK - 2410</div>
                <div class="border text-center border-gray-800 bg-sky-100 font-bold">FECHA DE FIN </div>
                <div class="border text-center border-gray-800">{{ Moment(project.contract.end_date).format('DD/MM/YYYY')
                }}</div>
              </div>

              <!--TABLA 2-->
              <div class="w-full md:w-1/3 grid grid-cols-4 text-xs rounded-xl md:mx-2 mt-4 md:mt-0 ">
                <!--  Primera fila -->
                <div class="border text-center font-bold bg-sky-100 border-gray-800 col-span-2 ">DIAS EJECUTADOS </div>
                <div class="border text-center border-gray-800"> 444 </div>
                <div class="border text-center border-gray-800">
                  <div
                    class="bg-sky-300 text-xs align-self-center font-extrabold opacity-60 h-full text-black text-center p-0.5">
                    14%
                  </div>
                </div>
                <!-- Segunda fila -->
                <div class="border text-center font-bold bg-sky-100 border-gray-800 col-span-2 ">DIAS RESTANTES </div>
                <div class="border text-center border-gray-800">2164</div>
                <div class="border text-center border-gray-800 ">
                  <div
                    class="bg-teal-900 text-xs align-self-center font-extrabold opacity-60 h-full text-white text-center p-0.5">
                    83%
                  </div>
                </div>
                <!-- Tercera fila -->
                <div class="border text-center font-bold bg-sky-100 border-gray-800 col-span-2 ">TOTAL DIAS PROYECTO</div>
                <div class="border text-center border-gray-800 col-span-2">2608</div>
              </div>
            </div>
            <!-- OTROS DATOS -->
            <div class="grid grid-cols-2 gap-2">
              <div class="mb-2">
                <div class="flex justify-center items-center p-1 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">GESTIÓN DEL CRONOGRAMA</h2>
                </div>
                <div class="flex justify-start h-full bg-red-500">
                  <div class="size-64">
                    <Bar />
                  </div>
                </div>
                <!-- <div class="flex justify-start">
                  <GaugeGradeChart />
                </div> -->
                <!-- <div class="flex justify-end">
                  <S_Curve :title="Horarios" :key="showLineChart" />
                </div> -->
              </div>
              <article>
                <div class="flex justify-center items-center p-1 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">GESTIÓN DE LOS COSTOS</h2>
                </div>
                <!-- MINICARDS INFO -->
                <div>
                  <MiniCardInfo />
                </div>
                <!-- <div class="flex justify-end">
                  <GaugeGradeChart />
                </div> -->

                <!-- TABLA: GESTIÓN DE LOS COSTOS-->
                <div>
                  <table>
                    <thead>
                      <tr>
                        <th></th>
                        <th class="bg-sky-100">MATERIALES</th>
                        <th class="bg-sky-100">MANO DE OBRA</th>
                        <th class="bg-sky-100">SERVICIOS</th>
                        <th class="bg-sky-100">TOTAL</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-left font-semibold bg-sky-100">PRESUPUESTO</td>
                        <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                        <td>$110.000.000</td>
                        <td>$110.000.000</td>
                        <td>$110.000.000</td>
                      </tr>
                      <tr>
                        <td class="text-left font-semibold bg-sky-100">CONSUMO ACTUAL</td>
                        <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                        <td>$110.000.000</td>
                        <td>$110.000.000</td>
                        <td>$110.000.000</td>
                      </tr>
                      <tr>
                        <td class="text-left font-semibold bg-sky-100">DISPONIBLE</td>
                        <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                        <td>$110.000.000</td>
                        <td>$110.000.000</td>
                        <td>$110.000.000</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </article>
              <article>
                <div class="flex justify-center items-center p-1 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">ACTIVIDADES/NOVEDADES DE LA SEMANA</h2>
                </div>
              </article>
              <article class="overflow-y-auto">
                <table>
                  <thead>
                    <tr>
                      <th colspan="3" style="background-color: antiquewhite;">Hitos Contractuales</th>
                    </tr>
                  </thead>
                  <thead>
                    <tr>
                      <th class="uppercase" style="width:27rem">Hitos</th>
                      <th class="uppercase">Fecha</th>
                      <th class="uppercase">Monto</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left font-semibold">PAGO ANTICIPADO</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>!110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>!110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>!110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>!110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>!110.000.000</td>
                    </tr>
                    <!-- <tr>
                      <td>Columna 1, Fila 6</td>
                      <td>Columna 2, Fila 6</td>
                      <td>Columna 3, Fila 6</td>
                    </tr> -->
                    <tr>
                      <td>TOTAL</td>
                      <td></td>
                      <td>$110.000.000</td>
                    </tr>
                  </tbody>
                </table>
                <!-- <DataTable :options="project" tableStyle="min-width: 50rem">
                  <Column header="Hitos">
                    <template #body="slotProps">
                      {{ slotProps.data.contract_id }}
                    </template>
                  </Column>
                  <Column header="Fecha">

                  </Column>
                  <Column header="Monto">
                    <template #body="slotProps">
                      <p class="font-semibold">$110.000.000</p>
                    </template>
                  </Column>
                </DataTable> -->
              </article>
            </div>
          </TabPanel>
        </TabView>
      </article>
    </section>
    <!-- <Footer fontSize="sm" fontColor="white" marginTop="0" /> -->
  </main>
</template>
