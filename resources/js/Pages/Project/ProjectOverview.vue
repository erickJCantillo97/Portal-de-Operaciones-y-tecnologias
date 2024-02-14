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
import html2canvas from 'html2canvas'

const props = defineProps({
  project: Object
})

onMounted(() => {
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

const severitys = [
  { text: 'DISEÑO Y CONSTRUCCIÓN', severity: 'primary', class: '' },
  { text: 'CONSTRUCCIÓN', severity: 'success', class: '' },
  { text: 'DISEÑO', severity: 'info', class: '' },
  { text: 'GARANTIA', severity: 'warning', class: '' },
  { text: 'SERVICIO POSTVENTA', severity: 'success', class: '' },
  { text: 'SIN ESTADO', severity: 'danger', class: 'animate-pulse' }
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
    <div
      class="overflow-y-auto space-y-6 bg-white px-6 pt-0.5 divide-x-[500px] md:space-x-6  md:px-10 h-screen">
      <!--Project Details-->
      <TabView :scrollable="true" :pt="{
        nav: '!flex !justify-between'
      }">
        <TabPanel header="Información del Proyecto" :pt="{
          root: 'w-full',
        }">
          <div class="grid grid-cols-2 gap-2">
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
              <div class="w-full h-full">
                <img :src="'https://primefaces.org/cdn/primevue/images/galleria/galleria2.jpg'"
                  onerror="this.src='/public/images/generic-boat.png'" :alt="`${project.file}`"
                  class="object-cover object-center w-full h-[20rem] mr-1" />
              </div>
            </div>
            <TabView :scrollable="true" :pt="{
              nav: '!flex !justify-between'
            }
              ">
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
          root: '!w-full !bottom-0'
        }">
          <!-- <span id="contentToCapture" class="w-full"> -->
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
              <div class="bottom-0">
                <div class="flex justify-center items-center p-1 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">GESTIÓN DEL CRONOGRAMA</h2>
                </div>
                  <Bar :key="showLineChart" />
                  <div class="flex justify-center w-full">
                    <GaugeGradeChart :key="showLineChart" />
                    <S_Curve :title="Horarios" :key="showLineChart" />
                  </div>
              </div>

              <!-- TABLA: GESTIÓN DE LOS COSTOS -->
              <article>
                <div class="flex justify-center items-center p-1 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">GESTIÓN DE LOS COSTOS</h2>
                </div>
                <!-- MINICARDS INFO -->
                <div class="grid grid-cols-2 w-full">
                  <div class="grid grid-cols-2">
                    <MiniCardInfo title="Presupuesto" :value="191520456373" :valueProgressBar="50" />
                    <MiniCardInfo title="Presupuesto" :value="191520456373" :valueProgressBar="35" />
                    <MiniCardInfo title="Presupuesto" :value="191520456373" :valueProgressBar="10" />
                    <MiniCardInfo title="Presupuesto" :value="191520456373" :valueProgressBar="90" />
                  </div>
                  <GaugeGradeChart :key="showLineChart" />
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

              <!-- TABLA: ACTIVIDADES/NOVEDADES DE LA SEMANA -->
              <article>
                <div class="flex justify-center items-center p-1 mb-1 bg-blue-800 text-white">
                  <h2 class="font-semibold">ACTIVIDADES/NOVEDADES DE LA SEMANA</h2>
                </div>
                <div class="flex gap-2">
                  <div class="w-full ">
                    <ul class="[&>li]:text-xs [&>li]:font-semibold space-y-2">
                      <li>1. Bloque de Láminas bloque 5650</li>
                      <li>2. Corte de perfiles bloque 5550</li>
                      <li>3. Armado de previas bloque 2320</li>
                      <li>4. Ensamble bloque 5740-5640-5540-1130-2320</li>
                    </ul>
                  </div>
                  <div class="flex justify-end items-center w-full">
                    <img
                      src="https://assets.asana.biz/transform/5d0217e1-a08d-4e8c-a8cb-616e658f434e/inline-project-management-critical-path-method-2-es-2x"
                      alt="picture" class="h-48 object-fill">
                  </div>
                </div>
              </article>

              <!-- TABLA: HITOS CONTRACTUALES -->
              <article class="overflow-y-auto">
                <div class="flex justify-center items-center p-1 mb-2 bg-blue-800 text-white">
                  <h2 class="font-semibold">HITOS CONTRACTUALES</h2>
                </div>
                <table>
                  <thead>
                    <tr>
                      <th class="uppercase bg-sky-100" style="width:27rem">Hitos</th>
                      <th class="uppercase bg-sky-100">Fecha</th>
                      <th class="uppercase bg-sky-100">Monto</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left font-semibold ">PAGO ANTICIPADO</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>$110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold ">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>$110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold ">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>$110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold ">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>$110.000.000</td>
                    </tr>
                    <tr>
                      <td class="text-left font-semibold ">Nombre del hito</td>
                      <td>{{ Moment().format('DD/MM/YYYY') }}</td>
                      <td>$110.000.000</td>
                    </tr>
                    <!-- <tr>
                      <td>Columna 1, Fila 6</td>
                      <td>Columna 2, Fila 6</td>
                      <td>Columna 3, Fila 6</td>
                    </tr> -->
                    <tr>
                      <td class="font-semibold bg-sky-100">TOTAL</td>
                      <td></td>
                      <td>$110.000.000</td>
                    </tr>
                  </tbody>
                </table>
              </article>
            </div>
          <!-- </span> -->
        </TabPanel>
      </TabView>
      <button @click="showLineChart++">Descargar Imagen</button>
    </div>
    <!-- <Footer fontSize="sm" fontColor="white" marginTop="0" /> -->
  </main>
</template>
