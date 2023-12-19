<script setup>
import { ref, onMounted } from 'vue'

onMounted(() => {
  getProjects()
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
const filteredProjects = ref()
const projectsOptions = ref([])

const getProjects = async () => {
  try {
    await axios(route('projects.index'))
      .then(res => {
        projectsOptions.value = res.data.projects
        searchProjects()
      })
  } catch (error) {
    console.log('No hay ná!')
  }
}

const searchProjects = () => {
  const searchWord = keyword.value.toLowerCase().trim()

  filteredProjects.value = projectsOptions.value.filter(project =>
    project.name.toLowerCase().includes(searchWord) ||
    project.SAP_code.toLowerCase().includes(searchWord)
    // project.start_date.toLowerCase().includes(searchWord)
  )
}

const selectItemList = () => {
  console.log('Hola Mundo')
}
</script>

<template>
  <div class="flex items-center gap-6 px-8 pt-6">
    <h2 class="text-xl font-bold text-gray-900">Proyectos</h2>
    <input type="search" v-model="keyword" @input="searchProjects()"
      class="rounded-lg border-2 border-gray-200 w-full placeholder:italic" placeholder="Filtrar Proyectos" />
  </div>
  <section
    class="grid grid-cols-4 h-96 overflow-y-auto custom-scroll snap-y snap-mandatory sm:col-span-1 md:col-span-1 border gap-4 border-gray-200 rounded-lg m-8 p-4 mb-2">
    <ul v-for="project in filteredProjects" :key="project.id" class="text-sm italic [&>li>p]:font-semibold snap-start">
      <div @click="toggleSelectItem(project.id)"
        :class="selectedItems.includes(project.id) ? 'bg-blue-900 text-white' : 'hover:border-2 hover:border-blue-900'"
        class="flex flex-wrap p-2 space-x-4 shadow-md rounded-md cursor-pointer">
        <img src="https://www.cotecmar.com/sites/default/files/media/imagenes/2021-12/CotecmarLogo.png"
          onerror="this.src='images/generic-boat.png'" class="rounded-t-2xl h-32 w-full object-center object-contain" />
        <div>
          <li>
            <p class="text-blue-800 text-xl pt-2 pb-2 font-semibold">
              <i class="fa-solid fa-folder-closed"></i>
              {{ project.name }}
            </p>
          </li>
          <li>
            <p><span class="font-semibold">Código SAP:</span> {{ project.SAP_code }}</p>
          </li>
          <li>
            <p><span class="font-semibold">Fecha Inicio:</span> {{ project.start_date == null ? 'N/A' : project.start_date
            }}</p>
          </li>
          <li>
            <p><span class="font-semibold">Fecha Finalización:</span>
              {{ project.end_date == null ? 'N/A' : project.end_date }}</p>
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