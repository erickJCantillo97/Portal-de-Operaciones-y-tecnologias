<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { ref, onMounted } from 'vue'
import Combobox from '@/Components/Combobox.vue'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Moment from 'moment'
import Loading from '@/Components/Loading.vue'
import NoContentToShow from '@/Components/NoContentToShow.vue'
import { useSweetalert } from '@/composable/sweetAlert'
const { toast } = useSweetalert()

onMounted(() => {
  getProjects()
  getGerencias()
})

defineProps(
  {
    name: String,
    default: ''
  },
  {
    sap: String,
    default: ''
  },
)

const keyword = ref('')
const selectedItems = ref([])
const filteredProjects = ref([])
const projectsOptions = ref([])
const gerenciasOptions = ref([])
const selectedGerencia = ref()
const selectedDate = ref()
const loadingProjects = ref(true)
const showNoProjects = ref(true)

const getProjects = async () => {
  try {
    await axios(route('projects.index'))
      .then(res => {
        filteredProjects.value = projectsOptions.value = res.data.projects
        searchProjects()

        if (res.data.projects != null) {
          loadingProjects.value = false
        } else {
          showNoProjects.value = true
        }
      })
  } catch (error) {
    console.log('Nada')
    // toast('No hay ná!', 'error')
  }
}

const getGerencias = async () => {
  try {
    await axios(route('get.gerencias'))
      .then(res => {
        gerenciasOptions.value = res.data.gerencias
        searchProjects()
      })
  } catch (error) {
    console.log('Nada')
    // toast('No hay ná!', 'error')
  }
}

const searchProjects = () => {
  const searchWord = keyword.value.toLowerCase().trim()

  filteredProjects.value = projectsOptions.value.filter(project =>
    project.name.toLowerCase().includes(searchWord) ||
    project.SAP_code.toLowerCase().includes(searchWord))

  if (selectedGerencia.value != null) {
    filteredProjects.value = filteredProjects.value.filter(project =>
      selectedGerencia.value == project.gerencia)
  }

  if (selectedDate.value != null) {
    const selectedYear = selectedDate.value.getFullYear()

    filteredProjects.value = filteredProjects.value.filter(project => {
      const startProjectYear = new Date(project.start_date).getFullYear()
      const endProjectYear = new Date(project.end_date).getFullYear()
      return selectedYear >= startProjectYear && selectedYear <= endProjectYear
    })
  }
}

const clearFilters = () => {
  selectedDate.value = null
  selectedGerencia.value = null
  keyword.value = ''

  filteredProjects.value = projectsOptions.value
}

const truncateString = (string) => {
  let maxLength = 20;
  let truncatedString = string.length > maxLength ? string.substring(0, maxLength) + "..." : string
  return truncatedString
}

const selectItemList = () => {
  console.log('Hola Mundo')
}
</script>

<template>
  <section class="flex items-center gap-6 px-8 pt-6">
    <h2 class="text-xl font-bold text-gray-900">Proyectos</h2>
  </section>
  <section class="flex flex-nowrap items-center gap-6 px-8 pt-2">
    <h2 class="text-lg italic font-semibold">Filtros:</h2>
    <input type="search" v-model="keyword" @input="searchProjects()"
      class="rounded-lg border-2 border-gray-200 w-3/4 placeholder:italic" placeholder="Filtrar Proyectos" />
    <!--CAMPO GERENCIA (gerencia)-->
    <!-- <Combobox @click="searchProjects()" class="font-semibold" label="" placeholder="Seleccione Gerencia"
      :options="gerenciasOptions" v-model="selectedGerencia" /> -->

    <Dropdown @change="searchProjects()" v-model="selectedGerencia" :options="gerenciasOptions" showClear
      virtualScrollerOptions optionLabel="name" optionValue="name" placeholder="Seleccione Gerencia"
      class="w-56 rounded-md md:w-14rem" :pt="{
        root: {
          class: '!w-56 h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
        },
        input: {
          class: '!text-md pt-3 pl-2 italic'
        },
        item: {
          class: '!text-md italic'
        }
      }" />

    <Calendar @date-select="searchProjects()" v-model="selectedDate" showIcon manualInput view="year" dateFormat="yy"
      placeholder="Fecha" class="rounded-md md:w-14rem" :pt="{
        root: {
          class: '!w-48 h-10 !ring-gray-300 !ring-inset ring-1 !border-0 !shadow-sm '
        },
        input: {
          class: '!text-md pt-3 pl-2 italic'
        },
        item: {
          class: '!text-md italic'
        }
      }" />
  </section>
  <section
    class="grid grid-cols-6 h-96 overflow-y-auto custom-scroll snap-y snap-mandatory sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg m-8 p-4 mb-2">
    <section v-if="loadingProjects" class="h-[50vh] w-full flex flex-col justify-center items-center col-span-6">
      <Loading message="Cargando Proyectos" />
    </section>

    <NoContentToShow subject="Proyectos" v-if="filteredProjects.length == 0" />

    <ul v-for="project in filteredProjects" :key="project.id"
      class="col-span-2 text-sm italic [&>li>p]:font-semibold snap-start">
      <div @click="toggleSelectItem(project.id)"
        :class="selectedItems.includes(project.id) ? 'bg-blue-900 text-white' : 'hover:border-2 hover:border-blue-900'"
        class="flex flex-nowrap w-full p-2 shadow-md rounded-md cursor-pointer">
        <!-- <img src="https://www.cotecmar.com/sites/default/files/media/imagenes/2021-12/CotecmarLogo.png"
          onerror="this.src='images/generic-boat.png'" class="rounded-t-2xl h-32 w-full object-center object-contain" /> -->
        <div>
          <ApplicationLogo :width-logo="130" :height-logo="130" />
        </div>
        <div class="w-full mx-8">
          <li>
            <p class="w-full text-blue-800 text-md pt-2 pb-2 font-semibold truncate">
              <i class="fa-solid fa-folder-closed"></i>
              {{ truncateString(project.name) }}
            </p>
          </li>
          <li>
            <p><span class="font-semibold">SAP:</span> {{ project.SAP_code }}</p>
          </li>
          <li>
            <p><span class="font-semibold">Inicio:</span>
              {{ project.start_date == null ? 'N/A' : project.start_date }}
            </p>
          </li>
          <li>
            <p><span class="font-semibold">Fin:</span>
              {{ project.end_date == null ? 'N/A' : project.end_date }}
            </p>
          </li>
          <li>
            <p><span class="font-semibold">Gerencia:</span> {{ project.gerencia }}</p>
          </li>
          <li>
            <p><span class="font-semibold">Estado:</span> {{ project.status }}</p>
          </li>
        </div>
      </div>
    </ul>
  </section>

  <!--PROYECTOS-->
  <!-- <h2 class="text-xl font-bold text-gray-900 px-6 pt-6 ">Proyectos</h2>
  <main class="grid grid-cols-4 w-full max-h-screen px-6 pt-4">
    <section
      class="col-span-6 w-full h-96 overflow-y-auto custom-scroll border-2 border-gray-300 rounded-lg p-2 focus hover:border-blue-500">
      <ul v-for="item in items" :key="item.id">
        <div @click="selectItemList(item.id)"
          :class="itemSelect == item.id ? 'bg-blue-900 text-white' : 'hover:bg-gray-200'"
          class="flex justify-between items-center text-xs space-x-6 p-2 w-full cursor-pointer rounded-lg">
          <div>
            <p class=" text-xs font-bold">{{ item.name }}:</p>
          </div>
          <div class="flex italic">
            <ClockIcon class="w-4 h-4" />
            <p>{{ item.name }}</p>
            <p>{{ item.last }}</p>
          </div>
        </div>
      </ul>
    </section>
  </main> -->
</template>