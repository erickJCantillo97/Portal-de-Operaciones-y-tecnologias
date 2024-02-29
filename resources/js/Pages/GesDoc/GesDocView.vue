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
  // getTipologias()
  // getGerencias()
})

const selectedProjects = ref([])
const projectsOptions = ref([])
const loadingProjects = ref(true)
const showNoProjects = ref(true)

const project = ref()
const tipologia = ref() // v-model
const tipologias = ref(null) //options
const listTipologia = ref(0) // Truco Ninja

const getTipologias = async () => {
  try {
    if (selectedProjects.value != null) {
      await axios.get(route('get.tipologias', selectedProjects.value))
        .then((res) => {
          tipologias.value = Object.values(res.data.tipologias)
          // listTipologia.value++
        })
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
        porjectID: selectedProjects.value,
        tipologiaID: tipologia.value
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
  const startDate = option.start_date ? option.start_date : 'N/A';
  const endDate = option.end_date ? option.end_date : 'N/A';
  const gerencia = option.Gerencia ? option.Gerencia : 'N/A';
  const status = option.status ? option.status : 'N/A';

  return `Fecha Inicio: ${startDate}\nFecha Fin: ${endDate}\nGerencia: ${gerencia}\nEstado: ${status}`;
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

const truncateString = (string) => {
  let maxLength = 20;
  let truncatedString = string.length > maxLength ? string.substring(0, maxLength) + "..." : string
  return truncatedString
}
</script>
<template>
  <AppLayout>
    <!-- <ProjectsCard /> -->
    <main class="h-[89vh] overflow-y-auto">
      <section class="grid size-full max-w-full grid-cols-3 gap-4 p-4">
        <article class="col-span-1">
          <Listbox v-model="selectedProjects" @change="getTipologias()" :options="projectsOptions" filter
            :filterFields="['name', 'SAP_code', 'contract.name', 'gerencia', 'start_date', 'end_date']" optionLabel="name"
            optionValue="id" filterPlaceholder="Seleccione proyecto" :virtualScrollerOptions="{ itemSize: 38 }"
            listStyle="height:70vh" class="w-full md:w-14rem" :pt="{
              filterInput: '!h-8',
              item: '!h-20',
            }">
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
                      {{ slotProps.option.name }}
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
        <article class="col-span-1">
          <!-- <NoContentToShow subject="Tipologías" /> -->
          <Listbox :key="listTipologia" v-model="tipologia" :options="tipologias" filter optionValue="id"
            optionLabel="name" @click="selectedTipologia()" listStyle="max-height:60vh" class="w-full md:w-14rem" :pt="{
              filterInput: { class: 'rounded-md border !h-8 border-gray-200' },
              item: { class: 'hover:bg-blue-100 text-md !px-1 !py-0.5' },
            }">
            <template #option="slotProps">
              <div class="grid grid-cols-7 h-min">
                <p class="col-span-6 flex items-center">{{ slotProps.option.name }}</p>
                <!-- <div class="flex space-x-1 rounded-md p-1 justify-end text-right items-center"
                  v-if="slotProps.option.count != 0">
                  <p class="text-sm">{{ slotProps.option.count }}</p>
                  <i class="fa-regular fa-file text-danger border p-1 rounded-md border-danger">
                  </i>
                </div> -->
              </div>
            </template>
          </Listbox>
        </article>
        <article class="col-span-1">
          <div v-if="tipologia">
            <div class="border rounded-md">
              <div class="flex w-full  space-x-2 p-2">
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
          <Loading v-else class="col-span-3 h-full pt-10" message="Seleccione una tipologia" />
          <NoContentToShow class="mt-5" v-else subject="Tipologias" />
        </article>
      </section>
    </main>
  </AppLayout>
</template>
