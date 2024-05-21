<script setup>
import CustomModal from '@/Components/CustomModal.vue';
import { DateHelper } from '@bryntum/gantt';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import { ref } from 'vue';



const gantt = defineModel('gantt', {
    type: Object,
})

const props = defineProps({
    project: Object
})

const pdfExport = ref({
    exportServer: 'https://dev.bryntum.com:8082',
    orientation: 'landscape',
    paperFormat: 'Letter',
    keepRegionSizes: { locked: true },
    columns: ['wbs', 'name', 'percentdone', 'duration', 'startdate', 'enddate'],
    repeatHeader: true,
    exporterType: 'multipagevertical',
    fileFormat: 'pdf',
    fileName: 'Cronograma-' + props.project.name + '-' + DateHelper.format(new Date(), 'YYYY-MM-DD')
})


const visibleExport = ref({
    modal: false,
    load: false
})

const columnsExport = ref([])
function showModalExport() {
    columnsExport.value = gantt.value.columns.map((c) => {
        if (c.type != 'addnew' && c.text != null) {
            return { text: c.text, type: c.id }
        }
    }).filter((c) => c !== undefined)
    visibleExport.value.modal = true
}

const exportSchedule = async () => {
    visibleExport.value.load = true
    await gantt.value.features.pdfExport.export({ columns: pdfExport.value.columns })
        .then(result => {
            // console.log(result)
            toast.add({ text: 'Exportando...', severity: 'success', group: 'customToast', life: 3000 });
            visibleExport.value.modal = false
        })
        .catch((error) => {
            toast.add({ text: 'Ha ocurrido un error', severity: 'error', group: 'customToast', life: 3000 });
            console.log(error)
        });
    visibleExport.value.load = false
}


</script>

<template>
    <Button raised v-tooltip.bottom="'Exportar a PDF'" icon="fa-solid fa-file-pdf" @click="showModalExport()" />
    <CustomModal v-model:visible="visibleExport.modal" icon="fa-solid fa-file-export" titulo="Exportar cronograma"
        width="40vw">
        <template #body>
            <div class="space-y-4">
                <div class="w-full">
                    <label for="exportName">Nombre del archivo</label>
                    <InputText id="exportName" class="w-full" v-model="pdfExport.fileName" />
                </div>
                <div class="w-full">
                    <label for="columns">Seleccionar columnas a exportar</label>
                    <MultiSelect v-model="pdfExport.columns" option-value="type" option-label="text" class="w-full"
                        id="columns" display="chip" :options="columnsExport">
                    </MultiSelect>
                    <!-- {{ columnsExport }} -->
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="w-full">
                        <label for="orientation">Orientación</label>
                        <Dropdown v-model="pdfExport.orientation" option-value="value" option-label="text"
                            class="w-full" id="orientation"
                            :options="[{ text: 'Panoramica', value: 'landscape' }, { text: 'Retrato', value: 'portrait' }]">
                        </Dropdown>
                    </div>
                    <div class="w-full">
                        <label for="format">Formato de hoja</label>
                        <Dropdown v-model="pdfExport.paperFormat" option-value="value" option-label="text"
                            class="w-full" id="format"
                            :options="[{ text: 'A1', value: 'A1' }, { text: 'A2', value: 'A2' }, { text: 'A3', value: 'A3' }, { text: 'A4', value: 'A4' }, { text: 'A5', value: 'A5' }, { text: 'Oficio', value: 'Legal' }, { text: 'Carta', value: 'Letter' }]">
                        </Dropdown>
                    </div>
                    <div class="w-full">
                        <label for="fileformat">Formato de archivo</label>
                        <Dropdown v-model="pdfExport.fileFormat" option-value="value" option-label="text" class="w-full"
                            id="fileformat" :options="[{ text: 'PDF', value: 'pdf' }, { text: 'PNG', value: 'png' }]">
                        </Dropdown>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <span class="pr-4 mt-4 space-x-2">
                <Button label="Cancelar" :loading="visibleExport.load" severity="danger"
                    icon="fa-regular fa-circle-xmark" @click="visibleExport.modal = false" />
                <Button label="Exportar" :loading="visibleExport.load" severity="success" icon="fa-solid fa-download"
                    @click="exportSchedule()" />
            </span>
        </template>
    </CustomModal>
</template>