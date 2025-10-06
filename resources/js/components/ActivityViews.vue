<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import TodayView from './TodayView.vue';
import WeekView from './WeekView.vue';
import MonthView from './MonthView.vue';

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
    weekData: Record<string, {
        date: string;
        dayName: string;
        count: number;
        totalMinutes: number;
        isToday: boolean;
    }>;
    monthData: Record<string, {
        weekLabel: string;
        dateRange: string;
        count: number;
        totalMinutes: number;
    }>;
    timerDuration: number;
}

defineProps<Props>();

const activeView = ref<'today' | 'week' | 'month'>('today');
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
                :variant="activeView === 'week' ? 'default' : 'ghost'"
                @click="activeView = 'week'"
            >
                This Week
            </Button>
            <Button
                :variant="activeView === 'month' ? 'default' : 'ghost'"
                @click="activeView = 'month'"
            >
                This Month
            </Button>
        </div>

        <!-- Views -->
        <div>
            <TodayView v-if="activeView === 'today'" :today-data="todayData" :timer-duration="timerDuration" />
            <WeekView v-if="activeView === 'week'" :week-data="weekData" :timer-duration="timerDuration" />
            <MonthView v-if="activeView === 'month'" :month-data="monthData" :timer-duration="timerDuration" />
        </div>
    </div>
</template>
