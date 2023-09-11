<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive } from 'vue';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
import { useGanttConfig } from '@/AppConfig';
import "@bryntum/gantt/gantt.material.css";
import '@bryntum/gantt/locales/gantt.locale.Es.js';
import { LocaleManager } from '@bryntum/gantt/gantt.module.js';


const props = defineProps({
    project: Object,
})


LocaleManager.applyLocale('Es');
const barMargin = ref(5);
const ganttConfig = reactive(useGanttConfig(props.project));

</script>
<template>
    <AppLayout title="">
        <div class="">
            <div class="h-screen">
                <div class="overflow-hidden shadow-xl sm:rounded-lg h-full">
                    {{ project.name }}
                    <bryntum-gantt v-bind="ganttConfig" :barMargin="barMargin"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
