<script setup>
import {
    BuildingOfficeIcon,
    CheckCircleIcon,
} from '@heroicons/vue/20/solid'
</script>
<style scoped>
@media screen and (max-width: 640px) {
    .custom-image2 {
        display: none;
    }
}
</style>
<template>
    <div>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <!-- Profile -->
                    <div class="flex items-center">
                        <img class="size-[4rem] object-[50%_30%] object-cover rounded-full"
                            :src="$page.props.auth.user.photo" alt="" />
                        <div>
                            <div class="flex items-center">
                                <img class="hidden sm:hidden md:hidden lg:hidden xl:hidden"
                                    :src="$page.props.auth.user.photo" alt="" />
                                <h1 class="ml-3 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:leading-9">
                                    Saludos, {{ $page.props.auth.user.name }}</h1>
                            </div>
                            <dl class="flex flex-col mt-6 sm:ml-3 sm:mt-1 sm:flex-row sm:flex-wrap">
                                <dt class="sr-only">Company</dt>
                                <dd class="flex items-center text-sm font-medium text-gray-500 capitalize sm:mr-6">
                                    <BuildingOfficeIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400"
                                        aria-hidden="true" /> {{ $page.props.auth.user.gerencia }} -
                                    {{ $page.props.auth.user.oficina }}
                                </dd>
                                <dt class="sr-only">Account status</dt>
                                <dd
                                    class="flex items-center mt-3 text-sm font-medium text-gray-500 capitalize sm:mr-6 sm:mt-0">
                                    <CheckCircleIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-green-400"
                                        aria-hidden="true" /> {{ $page.props.auth.user.cargo }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <!-- <div class="flex mt-6 space-x-3 md:ml-4 md:mt-0">
                    <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Add money</button>
                    <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white rounded-md shadow-sm bg-cyan-600 hover:bg-cyan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600">Send money</button>
                </div> -->
            </div>
        </div>
    </div>
</template>
