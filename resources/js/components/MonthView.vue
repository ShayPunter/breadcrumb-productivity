<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    monthData: Record<string, {
        date: string;
        totalMinutes: number;
        dayOfMonth: number;
    }>;
    timerDuration: number;
}

const props = defineProps<Props>();

const days = computed(() => Object.values(props.monthData));

const totalMinutes = computed(() => {
    return days.value.reduce((sum, day) => sum + day.totalMinutes, 0);
});

// GitHub-style contribution intensity (max 6 hours = 360 minutes)
const getIntensityClass = (totalMinutes: number) => {
    if (totalMinutes === 0) return 'bg-muted';
    const maxMinutes = 360; // 6 hours
    const percentage = Math.min((totalMinutes / maxMinutes) * 100, 100);

    if (percentage < 20) return 'bg-green-200 dark:bg-green-900';
    if (percentage < 40) return 'bg-green-300 dark:bg-green-700';
    if (percentage < 60) return 'bg-green-400 dark:bg-green-600';
    if (percentage < 80) return 'bg-green-500 dark:bg-green-500';
    return 'bg-green-600 dark:bg-green-400';
};

const formatHoursMinutes = (minutes: number) => {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours > 0) {
        return `${hours}h ${mins}m`;
    }
    return `${mins}m`;
};
</script>

<template>
    <div class="space-y-6">
        <!-- GitHub-style Contribution Graph -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-semibold">This Month</h4>
                <div class="text-sm text-muted-foreground">
                    {{ formatHoursMinutes(totalMinutes) }} total
                </div>
            </div>

            <div class="grid grid-cols-7 gap-2">
                <div
                    v-for="day in days"
                    :key="day.date"
                    class="flex flex-col gap-1"
                >
                    <!-- Day number -->
                    <div class="text-center">
                        <div class="text-xs text-muted-foreground">
                            {{ day.dayOfMonth }}
                        </div>
                    </div>

                    <!-- Contribution square -->
                    <div
                        class="aspect-square rounded-sm transition-all cursor-pointer hover:ring-2 hover:ring-primary/50"
                        :class="getIntensityClass(day.totalMinutes)"
                        :title="`${day.date}: ${day.totalMinutes} minutes tracked`"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-between pt-4 border-t">
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <span>Less</span>
                <div class="flex gap-1">
                    <div class="w-3 h-3 rounded-sm bg-muted"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-200 dark:bg-green-900"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-300 dark:bg-green-700"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-400 dark:bg-green-600"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-500 dark:bg-green-500"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-600 dark:bg-green-400"></div>
                </div>
                <span>More</span>
            </div>
            <div class="text-xs text-muted-foreground">
                Max: 6 hours/day
            </div>
        </div>
    </div>
</template>
