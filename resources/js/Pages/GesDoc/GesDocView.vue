<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Button from 'primevue/button'
import Combobox from '@/Components/Combobox.vue'
import DataView from 'primevue/dataview'
import Divider from 'primevue/divider'
import Image from 'primevue/image'
import Listbox from 'primevue/listbox'
import Loading from '@/Components/Loading.vue'
import Moment from 'moment'
import NoContentToShow from '@/Components/NoContentToShow.vue'
import ProjectsCard from '@/Components/ProjectsCard.vue'

onMounted(() => {
  getProjects()
})

const selectedProjects = ref([])
const projectsOptions = ref([])
const loadingProjects = ref(false)
const showNoProjects = ref(true)

const project = ref()
const tipologia = ref() // v-model
const tipologias = ref(null) //options
const listTipologia = ref(0) // Truco Ninja

const getTipologias = async () => {
  showNoProjects.value = false
  loadingProjects.value = true
  tipologias.value = []
  tipologia.value = null
  try {
    if (selectedProjects.value != null) {
      await axios.get(route('get.tipologias', selectedProjects.value.id))
        .then((res) => {
          tipologias.value = Object.values(res.data.tipologias)
          loadingProjects.value = false
          // listTipologia.value++
        })
    } else {
      showNoProjects.value = true
      loadingProjects.value = false
    }
  } catch (error) {
    console.log(error)
  }
}

const files = ref([])
const fileup = ref(Math.random() * (10))

const tipologiaFiles = ref([])

const selectedTipologia = async () => {
  fileup.value = Math.random() * (10)
  if (tipologia.value) {
    await axios.get(route('get.files.project.tipologia',
      {
        porjectID: selectedProjects.value.id,
        tipologiaID: tipologia.value.id
      }))
      .then((response) => {
        tipologiaFiles.value = response.data.files
      })
  } else {
    tipologiaFiles.value = []
  }
}

const getProjects = async () => {
  try {
    await axios(route('projects.index'))
      .then(res => {
        projectsOptions.value = res.data.projects
        // searchProjects()

        // if (res.data.projects != null) {
        //   loadingProjects.value = false
        // } else {
        //   showNoProjects.value = true
        // }
      })
  } catch (error) {
    console.log('Nada')
    // toast('No hay ná!', 'error')
  }
}

const getProjectDetails = (option) => {
  const name = option.name ? option.name : 'N/A';
  const startDate = option.start_date ? option.start_date : 'N/A';
  const endDate = option.end_date ? option.end_date : 'N/A';
  const gerencia = option.Gerencia ? option.Gerencia : 'N/A';
  const status = option.status ? option.status : 'N/A';

  return `Nombre: ${name}\nFecha Inicio: ${startDate}\nFecha Fin: ${endDate}\nGerencia: ${gerencia}\nEstado: ${status}`;
}

function formatDateTime24h(dateTime) {
  return new Date(dateTime).toLocaleString('es-CO',
    { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false })
}

const formatSize = (bytes) => {
  const k = 1024;
  const dm = 1;
  const sizeType = ['B', 'KB', 'MB', 'GB']

  if (bytes === 0) {
    return `0 byte`;
  }

  const i = Math.floor(Math.log(bytes) / Math.log(k));
  const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

  return `${formattedSize} ${sizeType[i]}`;
}

const truncateString = (string, maxLength) => {
  let truncatedString = string.length > maxLength ? string.substring(0, maxLength) + "..." : string
  return truncatedString
}
</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(-20px);
  opacity: 0;
}
</style>

<template>
  <AppLayout>
    <!-- <ProjectsCard /> -->
    <main class="h-[89vh] overflow-y-auto hover:scrollbar-width-lg">
      <section class="grid grid-cols-3 size-full max-w-full gap-4 p-2">
        <!--Lista de Proyectos-->
        <article class="col-span-1">
          <Listbox v-model="selectedProjects" @change="getTipologias()" :options="projectsOptions" filter
            :filterFields="['name', 'SAP_code', 'contract.name', 'gerencia', 'start_date', 'end_date']"
            optionLabel="name" filterPlaceholder="Seleccione proyecto" :virtualScrollerOptions="{ itemSize: 38 }"
            listStyle="height:70vh" class="w-full md:w-14rem" :pt="{
            filterInput: '!h-8',
            item: '!h-20',
          }">
            <template #header>
              <div class="bg-blue-800 rounded-t-sm">
                <h2 class="flex justify-center items-center font-semibold first-letter:uppercase text-white italic">
                  Proyectos
                </h2>
              </div>
            </template>

            <template #option="slotProps">
              <ul class="text-sm italic [&>li>p]:font-semibold snap-start">
                <div v-tooltip.top="{
            value: getProjectDetails(slotProps.option),
            class: '!w-full',
            autoHide: false
          }" class="flex flex-nowrap w-full space-x-4 size-12">
                  <img class="object-cover rounded-lg" :src="'/images/generic-boat.png'" :alt="slotProps.option.name" />
                  <li class="w-full space-y-2">
                    <p class="w-full text-blue-800 text-md font-semibold truncate">
                      <i class="fa-solid fa-folder-closed"></i>
                      {{ truncateString(slotProps.option.name, 50) }}
                    </p>
                    <p class="font-semibold">
                      Código SAP: {{ slotProps.option.SAP_code }}
                    </p>
                  </li>
                </div>
              </ul>
            </template>
          </Listbox>
        </article>

        <!--Tipologías-->
        <Transition name="slide-fade">
          <article v-if="tipologias && selectedProjects != null" class="col-span-1">
            <Listbox :key="listTipologia" v-model="tipologia" :options="tipologias" filter optionLabel="name"
              @click="selectedTipologia()" listStyle="max-height:70vh" class="w-full md:w-14rem" :pt="{
            item: '!p-2',
            filterInput: { class: 'rounded-md border !h-8 border-gray-200' },
          }">

              <template #header>
                <div class="bg-blue-800 rounded-t-sm">
                  <h2 class="flex justify-center items-center font-semibold first-letter:uppercase text-white italic">
                    Tipologías
                  </h2>
                </div>
              </template>

              <template #option="slotProps">
                <div v-tooltip.top="slotProps.option.name" class="flex items-center justify-between h-min">
                  <div class="flex space-x-2 items-center px-1">
                    <i class="fa-solid fa-minus fa-xs"></i>
                    <p class="col-span-6 flex items-center font-semibold italic">
                      {{ truncateString(slotProps.option.name, 50) }}
                    </p>
                  </div>
                  <div class="flex space-x-1 rounded-md p-1 justify-end text-right items-center"
                    v-if="slotProps.option.count != 0">
                    <p class="text-sm">{{ slotProps.option.count }}</p>
                    <i class="fa-regular fa-file text-danger border p-1 rounded-md border-danger">
                    </i>
                  </div>
                </div>
              </template>

              <template #empty>
                <div class="flex h-[67vh] items-center">
                  <Loading :message="`Cargando tipologias del proyecto \n${selectedProjects.name}`" />
                </div>
              </template>
            </Listbox>
          </article>
        </Transition>

        <!--Archivos-->
        <Transition name="slide-fade">
          <article :class="showNoProjects == false ? 'col-span-1' : 'col-span-2'">
            <div v-if="tipologia != null && selectedProjects != null" class="w-full h-[78vh] border rounded-lg">
              <div>
                <div class="flex w-full space-x-2 p-2">
                  <p class="font-bold">Tipologia:</p>
                  <p>{{ tipologia.name }}</p>
                </div>
                <Divider />
                <div v-if="tipologiaFiles.length > 0" class="overflow-y-auto h-[40vh]">
                  <DataView :value="tipologiaFiles" class="w-full overflow-y-auto">

                    <template #list="slotProps">
                      <div class="p-1 flex justify-between items-center w-full">
                        <div class="flex">
                          <i v-if="slotProps.data.filePath.slice(slotProps.data.filePath.lastIndexOf('.') + 1) == 'pdf'"
                            class=" text-danger fa-regular border p-1 rounded-md border-danger text-xl w-9 flex items-center justify-center fa-file-pdf"></i>
                          <Image v-else :src="slotProps.data.filePath" preview class="w-6">
                            <template #image>
                              <div class="flex items-center h-full">
                                <img :src="slotProps.data.filePath" alt="image" />
                              </div>
                            </template>

                            <template #preview="slotProps1">
                              <img :src="slotProps.data.filePath" class="!max-w-[80vw] !max-h-[80vh]" alt="preview"
                                :style="slotProps1.style" @click="slotProps1.previewCallback" />
                            </template>
                          </Image>
                          <div class="px-3">
                            <p class="text-sm">
                              {{ (slotProps.index + 1) + '. ' + slotProps.data.tipologia_name }}
                            </p>
                            <p class="text-xs font-semibold">{{ slotProps.data.name }}</p>
                            <span class="flex space-x-2">
                              <p class="text-xs">{{ slotProps.data.name_user }} </p>
                              <p class="text-xs">{{ formatDateTime24h(slotProps.data.created_at) }}
                              </p>
                              <p class="text-xs">{{ formatSize(slotProps.data.file_size) }} </p>
                              <p class="text-xs"
                                v-if="slotProps.data.filePath.slice(slotProps.data.filePath.lastIndexOf('.') + 1) == 'pdf'">
                                {{ slotProps.data.num_folios }} folio(s) </p>
                            </span>
                          </div>
                        </div>
                        <span class="space-x-1">
                          <Button class="!h-6 !w-6" icon="fa-regular fa-eye" outlined rounded
                            @click="showPdf($event, slotProps.data)"
                            v-if="slotProps.data.filePath.slice(slotProps.data.filePath.lastIndexOf('.') + 1) == 'pdf'"
                            severity="success">
                          </Button>
                          <Button class="!h-6 !w-6" icon="fa-regular fa-trash-can" outlined disabled @click="" rounded
                            severity="danger">
                          </Button>
                        </span>
                      </div>
                    </template>
                  </DataView>
                </div>
                <div class="flex items-center justify-center h-[30vh]" v-if="tipologiaFiles.length == 0">
                  <span>
                    <i class="w-full text-center text-2xl text-danger fa-solid fa-file-circle-exclamation"></i>
                    <p class="w-full text-center font-bold text-danger">
                      Aun no hay archivos
                    </p>
                  </span>
                </div>
                <div class="h-full flex items-center" v-if="tipologia.count > 0 && tipologiaFiles.length == 0">
                  <Loading message="Cargando archivos" />
                </div>
              </div>
            </div>
            <div v-else class="flex size-full justify-center items-center">
              <NoContentToShow class="mt-5" subject="Seleccione un proyecto para ver sus tipologías" />
            </div>
          </article>
        </Transition>
      </section>
    </main>
  </AppLayout>
</template>
