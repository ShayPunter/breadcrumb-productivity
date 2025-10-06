<script setup lang="ts">
import { Timer } from 'lucide-vue-next';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-lg bg-gradient-to-br from-green-500 to-green-600"
    >
        <Timer class="size-5 text-white" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold"
            >Breadcrumb</span
        >
    </div>
</template>
