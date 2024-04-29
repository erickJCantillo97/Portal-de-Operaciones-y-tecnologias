<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import Planning from "@/Pages/Dashboards/Projects/Planning.vue"
import QuotesCard from "@/Pages/Dashboards/Quotes/QuotesCards.vue"
import UserHeader from "@/Components/sections/UserHeader.vue"
import Quotes from "./Dashboards/Quotes.vue";
import Task from "./Dashboards/Personal/Task.vue";
import WareHouse from "./Dashboards/WareHouse.vue";

const props = defineProps({
    projects: Array,
    contracts: Array
})

// const broadcastChannel = () => {
//     setTimeout(() => {
//         window.Echo.private('testing')
//             .listen('.MyWebSocket', (e) => {
//                 alert(e.data);aa
//             })
//     }, 200);
// }
</script>

<template>
    <AppLayout>
        <div class="size-full overflow-y-scroll space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 max-w-full">
                <UserHeader class="col-span-1" />
                <QuotesCard class="col-span-1" :showIndicators="false"
                    v-if="$page.props.auth.user.oficina == 'DEPPC'" />
            </div>
            <!-- <div class="grid grid-cols-1 md:grid-cols-2 max-w-full">
                <div class="p-2">
                    <h2 class="text-primary font-extrabold text-lg py-2 border-b mb-2">Reqeurimientos Pendientes</h2>
                    <div class="flex justify-between w-full rounded-md shadow-md">
                        <div class="block"> 
                            <div class="text-primary font-bold text-md">RI-223-500-2001</div>
                            <div>TOP - Erick J cantillo</div>
                        </div>
                        <div class="flex space-x-2">
                            Informacion -
                            aprobar - rechazar
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- <Quotes v-if="$page.props.auth.user.oficina !== 'DEPPC'" />
            <Planning v-if="$page.props.auth.user.oficina != 'DEPPC'" :projects="props.projects" />
             <Tools v-if="hasPermission('tool edit')" :projects="props.projects" />
            <Planning v-if="$page.props.auth.user.oficina == 'DEPPC'" :projects="props.projects" /> -->

            <Task></Task>

            <!-- <Tools v-if="hasPermission('tool edit')" :projects="props.projects" /> -->
            <!-- <Projects v-else /> -->
            <!-- <CustomUpload mode="advanced" :multiple="true" accept=".xlsx,.xls" url="prueba"/> -->
        </div>
    </AppLayout>
</template>
