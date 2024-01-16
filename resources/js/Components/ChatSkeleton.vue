<script setup>
import Skeleton from 'primevue/skeleton'

</script>

<template>
    <div class="border-round border-1 surface-border p-4 mt-12 surface-card">
        <ul class="m-0 p-0 list-none">
            <li class="mb-3">
                <div class="flex">
                    <Skeleton shape="circle" size="3rem" class="mr-2"></Skeleton>
                    <div class="align-self-center" style="flex: 1">
                        <Skeleton width="25%" class="mb-2"></Skeleton>
                        <Skeleton width="100%" height="5rem"></Skeleton>
                    </div>
                </div>
            </li>
            <li>
                <div class="flex">
                    <Skeleton shape="circle" size="3rem" class="mr-2"></Skeleton>
                    <div class="align-self-center" style="flex: 1">
                        <Skeleton width="25%" class="mb-2"></Skeleton>
                        <Skeleton width="100%" height="5rem"></Skeleton>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
