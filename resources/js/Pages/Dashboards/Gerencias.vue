<script setup>
import { ref, onMounted } from 'vue';
import { usePermissions } from '@/composable/permission';
import AppLayout from '@/Layouts/AppLayout.vue';

const { hasRole, hasPermission } = usePermissions();

const colors = { GEDIN: 'bg-blue-500', VPEXE: 'bg-gray-500', GEMAM: 'bg-teal-500', 'VPT&O': 'bg-yellow-500', GEBOC: 'bg-cyan-500', GECTI: 'bg-indigo-500', GETHU: 'bg-red-500', PCTMAR: 'bg-purple-500', GEFAD: 'bg-sky-500', GECON: 'bg-pink-500' }

const personal = ref([])
const totalMembers = ref(0)

const props = defineProps({
    personal: Array,
    GERENCIA: String
})

onMounted(() => {
    for (var gerencia of Object.values(props.personal)) {
        personal.value.push(
            {
                title: gerencia[0].OFICINA,
                initials: gerencia.length,
                totalMembers: gerencia.length,
                bgColorClass: gerencia[0].GERENCIA != 'GECON' ? colors[gerencia[0].GERENCIA] : 'bg-' + gerencia[0].GERENCIA,
            },
        )
        totalMembers.value += gerencia.length
    }
})
</script>
<template>
    <AppLayout>
        <div class=" h-full w-full mt-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between">
                <h2 class="font-medium text-primary text-xl">
                    Personal Corporativo {{ GERENCIA }}
                </h2>
                <h2 class="font-medium text-primary text-xl">
                    Total: {{ totalMembers }} Personas
                </h2>
            </div>
            <div role="list"
                class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-2 lg:grid-cols-5 h-32 sm:h-auto overflow-y-auto custom-scroll snap-y snap-proximity">
                <div v-for="project in personal" :key="project.title"
                    class="relative col-span-1 flex rounded-md snap-center shadow-sm">
                    <div
                        :class="[project.bgColorClass, 'flex w-16 flex-shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white']">
                        {{ project.initials }}
                    </div>
                    <div
                        class="flex flex-1 items-center justify-between truncate rounded-r-md border-b border-r border-t border-gray-200 bg-white">
                        <div class="flex-1 truncate px-4 py-2 text-sm">
                            <a href="#" class="font-medium text-gray-900 hover:text-gray-600">{{ project.title }}</a>
                            <p>Personas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
