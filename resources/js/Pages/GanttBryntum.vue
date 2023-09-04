<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive } from 'vue';
import { BryntumGantt } from '@bryntum/gantt-vue-3';
import { useGanttConfig } from '@/AppConfig';
import "@bryntum/gantt/gantt.material.css";
import { LocaleManager } from '@bryntum/gantt/gantt.module.js';
import '@bryntum/gantt/locales/gantt.locale.Es.js'

LocaleManager.applyLocale('Es');

const barMargin = ref(2);
const ganttConfig = reactive(useGanttConfig());

</script>
<template>
    <AppLayout title="Prueba Cronograma">
        <div class="">
            <div class="h-screen">
                <div class="overflow-hidden shadow-xl sm:rounded-lg h-full">
                    <bryntum-gantt v-bind="ganttConfig" :barMargin="barMargin"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
