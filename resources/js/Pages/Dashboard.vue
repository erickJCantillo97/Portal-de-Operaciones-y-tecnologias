<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import UserHeader from "@/Components/sections/UserHeader.vue"
import QuotesCard from "@/Pages/Dashboards/Quotes/QuotesCards.vue"
import Projects from "@/Pages/Dashboards/Projects/Projects.vue"
import Planning from "@/Pages/Dashboards/Projects/Planning.vue"
import Quotes from "@/Pages/Dashboards/Quotes.vue"
import CustomUpload from "@/Components/CustomUpload.vue"

import { useToast } from "primevue/usetoast"

const toast = useToast();

const props = defineProps({
    projects: Array,
    contracts: Array
})

// const broadcastChannel = () => {
//     setTimeout(() => {
//         window.Echo.private('testing')
//             .listen('.MyWebSocket', (e) => {
//                 alert(e.data);
//             })
//     }, 200);
// }
//#endregion
</script>

<template>
    <AppLayout>
        <div class="overflow-y-scroll">
            <div class="grid grid-cols-1 md:grid-cols-2 max-w-full">
                <UserHeader class="col-span-1" />
                <QuotesCard class="col-span-1" :showIndicators="false" v-if="$page.props.auth.user.oficina == 'DEPPC'" />
            </div>
            <Quotes v-if="$page.props.auth.user.oficina == 'DEEST'" />
            <Planning v-if="$page.props.auth.user.oficina == 'DEPPC'" :projects="props.projects" />
            <!-- <Projects v-else /> -->
            <!-- <CustomUpload mode="advanced" :multiple="true" accept=".xlsx,.xls" url="prueba"/> -->
        </div>
    </AppLayout>
</template>
