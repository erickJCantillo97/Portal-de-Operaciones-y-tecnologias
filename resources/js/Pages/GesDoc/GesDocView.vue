<script setup>
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CustomModal from '@/Components/CustomModal.vue'
import CustomDataTable from '@/Components/CustomDataTable.vue'
import Image from 'primevue/image'
import Listbox from 'primevue/listbox'
import Loading from '@/Components/Loading.vue'
import NoContentToShow from '@/Components/NoContentToShow.vue'
import OverlayPanel from 'primevue/overlaypanel'
import ProjectsCard from '@/Components/ProjectsCard.vue'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'

const { truncateString } = useCommonUtilities()

onMounted(() => {
  getProjects()
})

const selectedProjects = ref([])
const selectedFiles = ref()
const projectsOptions = ref([])
const loadingProjects = ref(false)
const showNoProjects = ref(true)
const showNoTipologies = ref(false)
const showNoImages = ref(false)
const openDialog = ref(false)

const showArticleListTipologies = ref(true)
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
      showArticleListTipologies.value = true
      showNoTipologies.value = false
      await axios.get(route('get.tipologias', selectedProjects.value.id))
        .then((res) => {
          tipologias.value = Object.values(res.data.tipologias)
            .filter(tipologia => tipologia.count !== 0)
          if (tipologias.value.length === 0) {
            showArticleListTipologies.value = false
            showNoTipologies.value = true
          }
          // loadingProjects.value = false
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

const tipologiaFiles = ref({})

const selectedTipologia = async () => {
  fileup.value = Math.random() * (10)

  if (tipologia.value) {
    await axios.get(route('get.files.project.tipologia',
    {
      projectID: selectedProjects.value.id,
        tipologiaID: tipologia.value.id
      }))
      .then((response) => {
        tipologiaFiles.value.files = response.data.files
        tipologiaFiles.value.images = response.data.filesImages
        if (tipologiaFiles.value.images.length > 0)
          openDialog.value = true
      })
  } else {
    tipologiaFiles.value = []
  }
}

const fileListboxSelected = ref()

const fileSelected = (event) => {
  selectedFiles.value = event.value?.filePath ?? null
}

const getProjects = async () => {
  try {
    await axios(route('projects.index'))
      .then(res => {
        projectsOptions.value = res.data.projects
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

const pdf = ref()
const archivo = ref()
const archivoData = ref()

const showPdf = (event, data) => {
  archivoData.value = data
  pdf.value.toggle(event)
  fetch(data.filePath).then(response => response.blob())
    .then(blob => {
      const file = new File([blob], 'Documento.pdf', { type: 'application/pdf' });
      archivo.value = URL.createObjectURL(file)
    })
}

//#region CustomDataTable
const columnas = [
  // { field: 'id', header: 'Id', frozen: true, filter: true, sortable: true },
  { field: 'tipologia_name', header: 'Nombre del Archivo', filter: true, sortable: true },
  { field: 'name_user', header: 'Usuario', filter: true, sortable: true },
  { field: 'created_at', header: 'Fecha Creación', filter: true, sortable: true, type: 'date' },
  { field: 'num_folios', header: 'No. Folio', filter: true, sortable: true },
  { field: 'file_size', header: 'Tamaño', filter: true, sortable: true, type: 'fileSize' },
]

const buttons = [
  { event: 'downloadFiles', severity: 'primary', class: '', icon: 'fa-solid fa-download', text: true, outlined: false, rounded: false },
]
//#endregion

//#region Utilities
const downloadFiles = async () => {
  await router.get(route('get.files.project.tipologia', tipologiaFiles.value.id), {}, {
    onSuccess: () => {
      toast.add({ summary: 'Asignación Descargada', life: 2000 });
      clearModal2()
    }
  });
}
//#endregion


</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
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
    <main class="h-[89vh] overflow-y-auto">
      <section class="grid grid-cols-2 size-full max-w-full gap-4 p-2">
        <!--Lista de Proyectos-->
        <article class="col-span-1">
          <Listbox v-model="selectedProjects" @change="getTipologias()" :options="projectsOptions" filter
            :filterFields="['name', 'SAP_code', 'status,', 'contract.name', 'gerencia', 'start_date', 'end_date']"
            optionLabel="name" filterPlaceholder="Buscar proyecto" :virtualScrollerOptions="{ itemSize: 38 }"
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
          }" class="flex flex-nowrap w-full space-x-1 size-12">
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
                  <!-- <li>
                    <p class="font-semibold">
                      Estado: {{ slotProps.option.status }}
                    </p>
                  </li> -->
                </div>
              </ul>
            </template>

            <template #empty>
              <div class="flex h-[67vh] items-center">
                <Loading :message="'Cargando proyectos'" />
              </div>
            </template>
          </Listbox>
        </article>

        <!--Tipologías-->
        <Transition name="slide-fade">
          <article v-if="tipologias && selectedProjects != null && showArticleListTipologies" class="col-span-1">
            <Listbox :key="listTipologia" v-model="tipologia" :options="tipologias" filter
              filterPlaceholder="Buscar tipología" optionLabel="name" @change="selectedTipologia()"
              listStyle="max-height:70vh" class="w-full md:w-14rem" :pt="{
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
        <div v-if="showNoProjects" class="flex size-full items-center justify-center rounded-lg border border-gray-300">
          <NoContentToShow class="mt-5" subject="Seleccione un proyecto para ver sus tipologías" />
        </div>
        <div v-if="showNoTipologies && selectedProjects"
          class="flex size-full items-center justify-center rounded-lg border border-gray-300">
          <NoContentToShow class="mt-5"
            :subject="`El proyecto ${selectedProjects.name}\n no tiene Archivos cargados`" />
        </div>
      </section>

      <!--MODAL DE ARCHIVOS-->
      <CustomModal v-model:visible="openDialog" :maximizable="true" width="90rem">

        <template #icon>
          <i class="fa-solid fa-file-contract"></i>
        </template>

        <template #titulo>
          <p>Archivos</p>
        </template>

        <template #body>
          <TabView :pt="{
            // nav: '!flex !justify-between !w-full'
          }">
            <TabPanel header="Documentos">
              <div class="w-full h-[89vh] overflow-y-auto">
                <CustomDataTable :data="tipologiaFiles.files" :rows-default="100" :columnas="columnas"
                  :actions="buttons" @downloadFiles="downloadFiles">
                </CustomDataTable>
              </div>
            </TabPanel>
            <TabPanel header="Imágenes">
              <article class="grid h-[70vh] w-full grid-cols-2 gap-x-4 overflow-y-auto">
                <div>
                  <Listbox :key="listTipologia" v-model="fileListboxSelected" :options="tipologiaFiles.images" filter
                    filterPlaceholder="Seleccione un archivo" optionLabel="name" @change="fileSelected($event)"
                    listStyle="max-height:61.5vh" class="w-full md:w-14rem" :pt="{
            item: '!p-2',
            filterInput: { class: 'rounded-md border !h-8 border-gray-200' },
          }">
                    <template #option="slotProps">
                      <div v-tooltip.top="slotProps.option.tipologia_name"
                        class="flex items-center justify-between h-min">
                        <div class="flex space-x-2 items-center px-1">
                          <i class="fa-solid fa-minus fa-xs"></i>
                          <p class="col-span-6 flex items-center font-semibold italic">
                            {{ truncateString(slotProps.option.tipologia_name, 50) }}
                          </p>
                        </div>
                      </div>
                    </template>

                    <template #empty>
                      <div class="flex h-[67vh] items-center">
                        <Loading :message="'Cargando archivos'" />
                      </div>
                    </template>
                  </Listbox>
                </div>

                <div>
                  <div v-if="selectedFiles != null" class="flex space-x-1 rounded-md p-1 justify-center items-center">
                    <img :src="'/' + selectedFiles" class="size-full object-cover rounded-lg" />
                  </div>
                  <div v-else class="flex size-full items-center justify-center rounded-lg border border-gray-300">
                    <NoContentToShow class="mt-5" :subject="'Por favor seleccione una imagen'" />
                  </div>
                </div>
              </article>
            </TabPanel>
          </TabView>
        </template>
      </CustomModal>
    </main>
  </AppLayout>
</template>
