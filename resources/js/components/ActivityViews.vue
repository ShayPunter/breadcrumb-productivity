<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import TodayView from './TodayView.vue';
import YearView from './YearView.vue';

interface Props {
    todayData: {
        completed: number;
        totalMinutes: number;
        sessions: Array<{
            task_description: string;
            duration: number;
            completed_at: string;
        }>;
    };
    yearData?: Record<string, {
        date: string;
        totalMinutes: number;
        sessionCount: number;
        dayOfWeek: number;
        weekOfYear: number;
        month: number;
    }>;
    timerDuration: number;
}

const props = defineProps<Props>();

const activeView = ref<'today' | 'week' | 'month' | 'year'>('today');
</script>

<template>
    <div class="w-full">
        <!-- Tab Buttons -->
        <div class="flex gap-2 mb-6 border-b pb-2">
            <Button
                :variant="activeView === 'today' ? 'default' : 'ghost'"
                @click="activeView = 'today'"
            >
                Today
            </Button>
            <Button
                :variant="activeView === 'year' ? 'default' : 'ghost'"
                @click="activeView = 'year'"
            >
                Activity
            </Button>
        </div>

        <!-- Views -->
        <div>
            <TodayView v-if="activeView === 'today'" :today-data="todayData" :timer-duration="timerDuration" />
            <YearView v-if="activeView === 'year'" :year-data="yearData" :timer-duration="timerDuration" />
        </div>
    </div>
</template>
