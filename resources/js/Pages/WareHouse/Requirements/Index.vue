<script setup>
import CustomInput from '@/Components/CustomInput.vue';
import CustomModal from '@/Components/CustomModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import { ref } from 'vue';

const props = defineProps({
    projects: Array
})

const open = ref(false)
const formData = ref({
    requirement: {}
})
</script>

<template>
    <AppLayout>
        <div class="h-full w-full">
            <div class="flex justify-between items-center px-4 h-min">
                <span class="text-xl font-bold text-primary h-full items-center flex">
                    <p> Requerimientos de Materiales </p>
                </span>
                <div class="flex items-center space-x-2">
                    <Button label="Importar Requerimientos" severity="success" icon="fa-solid fa-plus"
                        :project="project" @click="open = !open" />
                </div>

            </div>
        </div>

        <CustomModal v-model:visible="open" width="100vh">
            <template #icon>
                <i class="fa-solid fa-file-contract"></i>
            </template>

            <template #titulo>
                <p>Importar Requerimientos</p>
            </template>

            <template #body>
                <span class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <CustomInput type="dropdown" optionLabel="name" optionValue="id" :options="projects"
                        label="Proyecto" placeholder="Selecione un proyecto" id="bloque"
                        v-model:input="formData.requirement.project" :invalid="$attrs.errors.supervisor != null"
                        :errorMessage="$attrs.errors.bloque">
                    </CustomInput>
                    <CustomInput label="Bloque" placeholder="Escriba Bloque" id="bloque"
                        v-model:input="formData.requirement.bloque" :invalid="$attrs.errors.supervisor != null"
                        :errorMessage="$attrs.errors.bloque">
                    </CustomInput>
                    <CustomInput label="Grupo/Sistema" placeholder="Escriba El grupo o sistema" id="grupo"
                        v-model:input="formData.requirement.sistema_grupo"
                        :invalid="$attrs.errors.sistema_grupo != null" :errorMessage="$attrs.errors.sistema_grupo">
                    </CustomInput>
                    <CustomInput label="Documento de Referencia" placeholder="Escriba Documento de Referencia"
                        id="document" v-model:input="formData.requirement.document"
                        :invalid="$attrs.errors.document != null" :errorMessage="$attrs.errors.document">
                    </CustomInput>
                    <CustomInput label="Adjuntar PDF" type="file" acceptFile=".pdf" id="pdf"
                        :invalid="$attrs.errors.pdf != null" :errorMessage="$attrs.errors.pdf">
                    </CustomInput>
                    <CustomInput class="mt-2 col-span-3" label="Notas" placeholder="Escriba la Nota del requerimiento"
                        type="textarea" v-model:input="formData.requirement.note" :invalid="$attrs.errors.note != null"
                        :errorMessage="$attrs.errors.note">
                    </CustomInput>
                </span>

            </template>

            <template #footer>
                <Button severity="danger" @click="open = false">Cancelar</Button>
                <Button severity="success" :loading="false" @click="submit()">
                    Guardar
                </Button>
            </template>
        </CustomModal>
    </AppLayout>
</template>