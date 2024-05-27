<script setup>
import { ref, onMounted } from 'vue'
import { useCommonUtilities } from '@/composable/useCommonUtilities'
import AppLayout from "@/Layouts/AppLayout.vue"
import KanbanQuotes from "@/Pages/Dashboards/Quotes/KanbanQuotes.vue"
import PayrollTable from "./PayrollTable.vue"
import Planning from "@/Pages/Dashboards/Projects/Planning.vue"
import Quotes from "./Dashboards/Quotes.vue"
import QuotesCard from "@/Pages/Dashboards/Quotes/QuotesCards.vue"
import Task from "./Dashboards/Personal/Task.vue"
import UserHeader from "@/Components/sections/UserHeader.vue"
import WareHouse from "./Dashboards/WareHouse.vue"

const { currencyFormat } = useCommonUtilities()

onMounted(() => {
    getTotalQuotesHired()
})

const props = defineProps({
    projects: Array,
    contracts: Array
})

const totalQuotesHired = ref()

const getTotalQuotesHired = () => {
    axios.get(route('get.quotes.cost.contratadas'))
        .then((res) => {
            totalQuotesHired.value = res.data
        })
}
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
                <div class="border rounded-lg shadow-md">
                    <QuotesCard class="col-span-1" :showIndicators="false"
                        v-if="$page.props.auth.user.oficina == 'DEPPC' || $page.props.auth.user.oficina == 'OFTIC'" />
                    <div class="flex items-center justify-center rounded-b-lg bg-primary pb-1.5">
                        <h3 class="flex space-x-4 italic text-white pt-2">
                            <span class="mr-2">
                                <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                            </span>
                            Valor Total Contratado:
                            <span class="text-yellow-300 font-semibold">
                                {{ totalQuotesHired == null ? 'Cargando...' : currencyFormat(totalQuotesHired) }}
                            </span>
                        </h3>
                    </div>
                </div>
            </div>

            <!-- <Task v-if="$page.props.auth.user.username == 'ecantillo' || $page.props.auth.user.oficina=='OFTIC'" /> -->
            <KanbanQuotes
                v-if="$page.props.auth.user.username == 'ecantillo' || $page.props.auth.user.oficina == 'OFTIC'" />
            <!-- <PayrollTable /> -->

            <Quotes
                v-if="$page.props.auth.user.oficina == 'DEEST' || $page.props.auth.user.username == 'elara' || $page.props.auth.user.oficina == 'DEPPC'" />
            <Planning v-if="$page.props.auth.user.oficina == 'DEPPC' || $page.props.auth.user.oficina == 'OFTIC'"
                :projects="props.projects" />
            <!-- <Quotes
                v-if="$page.props.auth.user.oficina == 'DEEST' || $page.props.auth.user.username == 'elara' || $page.props.auth.user.oficina == 'OFTIC'" /> -->
            <!-- <Planning v-if="$page.props.auth.user.oficina == 'DEPPC' || $page.props.auth.user.oficina=='OFTIC'" :projects="props.projects" /> -->

</template>
