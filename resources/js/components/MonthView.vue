<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    monthData: Record<string, {
        weekLabel: string;
        dateRange: string;
        count: number;
        totalMinutes: number;
    }>;
    timerDuration: number;
}

const props = defineProps<Props>();

const weeks = computed(() => Object.values(props.monthData));

const totalSessions = computed(() => {
    return weeks.value.reduce((sum, week) => sum + week.count, 0);
});

const totalMinutes = computed(() => {
    return weeks.value.reduce((sum, week) => sum + week.totalMinutes, 0);
});

const getIntensityClass = (count: number) => {
    if (count === 0) return 'bg-muted';
    if (count <= 5) return 'bg-green-200 dark:bg-green-900';
    if (count <= 10) return 'bg-green-300 dark:bg-green-700';
    if (count <= 15) return 'bg-green-400 dark:bg-green-600';
    return 'bg-green-500 dark:bg-green-500';
};

const maxCount = computed(() => {
    return Math.max(...weeks.value.map(week => week.count), 1);
});

const getBarHeight = (count: number) => {
    const percentage = (count / maxCount.value) * 100;
    return `${Math.max(percentage, 5)}%`;
};
</script>

<template>
    <div class="space-y-6">
        <!-- Week Bars Chart -->
        <div>
            <h4 class="text-sm font-semibold mb-4">Weekly Progress</h4>
            <div class="h-64 flex items-end gap-4 px-2">
                <div
                    v-for="week in weeks"
                    :key="week.weekLabel"
                    class="flex-1 flex flex-col items-center gap-2"
                >
                    <!-- Bar -->
                    <div class="w-full flex items-end justify-center h-full">
                        <div
                            class="w-full rounded-t-lg transition-all hover:opacity-80 cursor-pointer flex items-end justify-center pb-2"
                            :class="getIntensityClass(week.count)"
                            :style="{ height: getBarHeight(week.count) }"
                            :title="`${week.weekLabel}: ${week.count} session${week.count !== 1 ? 's' : ''} (${week.totalMinutes} min)`"
                        >
                            <span class="text-sm font-bold" :class="week.count > 0 ? 'text-white' : 'text-muted-foreground'">
                                {{ week.count }}
                            </span>
                        </div>
                    </div>
                    <!-- Label -->
                    <div class="text-center">
                        <div class="text-xs font-medium">{{ week.weekLabel }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Week Grid View -->
        <div>
            <h4 class="text-sm font-semibold mb-3">Week by Week</h4>
            <div class="grid gap-3 md:grid-cols-2">
                <div
                    v-for="week in weeks"
                    :key="week.weekLabel"
                    class="rounded-lg p-4 transition-all hover:scale-105 cursor-pointer"
                    :class="getIntensityClass(week.count)"
                >
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="font-semibold" :class="week.count > 0 ? 'text-white' : ''">
                                {{ week.weekLabel }}
                            </div>
                            <div class="text-xs" :class="week.count > 0 ? 'text-white/80' : 'text-muted-foreground'">
                                {{ week.dateRange }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold" :class="week.count > 0 ? 'text-white' : 'text-muted-foreground'">
                                {{ week.count }}
                            </div>
                            <div class="text-xs" :class="week.count > 0 ? 'text-white/80' : 'text-muted-foreground'">
                                sessions
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t" :class="week.count > 0 ? 'border-white/20' : 'border-muted-foreground/20'">
                        <div class="text-sm" :class="week.count > 0 ? 'text-white/90' : 'text-muted-foreground'">
                            Total Time
                        </div>
                        <div class="text-sm font-medium" :class="week.count > 0 ? 'text-white' : ''">
                            {{ week.totalMinutes }} min
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-between pt-4 border-t">
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <span>Less Active</span>
                <div class="flex gap-1">
                    <div class="w-4 h-4 rounded bg-muted"></div>
                    <div class="w-4 h-4 rounded bg-green-200 dark:bg-green-900"></div>
                    <div class="w-4 h-4 rounded bg-green-300 dark:bg-green-700"></div>
                    <div class="w-4 h-4 rounded bg-green-400 dark:bg-green-600"></div>
                    <div class="w-4 h-4 rounded bg-green-500 dark:bg-green-500"></div>
                </div>
                <span>More Active</span>
            </div>
            <div class="text-xs text-muted-foreground">
                {{ timerDuration }} min per session
            </div>
        </div>
    </div>
</template>
