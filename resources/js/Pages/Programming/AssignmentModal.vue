<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { EyeIcon } from '@heroicons/vue/24/outline';
import { ref, onMounted } from 'vue';
import Button from '@/Components/Button.vue';
import MultiSelect from 'primevue/multiselect';

const assignments = ref()
const open = ref(false)
const personal = ref([]);
const props = defineProps({
    task: {
        type: Object,
        required: true
    }
})
const personalSelect = ref();

const cargoRecurso = async () => {
    await axios.get(route('get.cargos')).then((res) => {
        personal.value = res.data.personal;
        return res.data.personal;
    })
}

onMounted(() => {
    cargoRecurso()
});


</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-30" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 z-30 w-screen h-screen transition-opacity bg-gray-500 bg-opacity-75" />
            </TransitionChild>

            <div class="fixed inset-0 z-50 h-screen overflow-y-auto ">
                <div
                    class="flex items-center justify-center max-h-full overflow-y-auto custom-scroll  p-4 text-center  sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel :class="props.heigthDialog"
                            class="relative px-2 pt-2 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl w-3/4">
                            <div>
                                <div class="px-2 mt-2 text-center">
                                    <DialogTitle as="h3" class="text-xl font-semibold text-primary ">
                                        Asignar Recursos
                                    </DialogTitle> <!--Se puede usar {{ tittle }}-->
                                    <div class="p-2 mt-2 space-y-4 border border-gray-200 rounded-lg">
                                        <div class="col-span-1 py-2 md:col-span-4 p-fluid p-input-filled">
                                            <!-- Contenedor de datos -->
                                            <ul>
                                                <li v-for="assignment in assignments" :key="assignment.id"
                                                    class="text-center">
                                                    <label for="password"
                                                        class="block capitalize text-sm font-mediumtext-gray-900">
                                                        {{ assignment.name }}
                                                    </label>
                                                    <MultiSelect v-model="personalSelect"
                                                        :options="personal[assignment.name]" filter
                                                        optionLabel="Nombres_Apellidos" display="chip"
                                                        :placeholder="'Selecionar ' + assignment.name"
                                                        :maxSelectedLabels="4" class="w-full md:w-20rem" />
                                                </li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="flex px-2 mt-2 space-x-4">
                                <Button class="hover:bg-danger text-danger border-danger" severity="danger"
                                    @click="open = false">Cancelar</Button>
                                <Button severity="success" :loading="false"
                                    class="text-success hover:bg-success border-success" @click="submit()">
                                    {{ formData.id != 0 ? 'Actualizar ' : 'Guardar' }}
                                </Button>
                            </div> -->
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

