<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    weekData: Record<string, {
        date: string;
        dayName: string;
        count: number;
        totalMinutes: number;
        isToday: boolean;
    }>;
    timerDuration: number;
}

const props = defineProps<Props>();

const weekDays = computed(() => Object.values(props.weekData));

const totalSessions = computed(() => {
    return weekDays.value.reduce((sum, day) => sum + day.count, 0);
});

const totalMinutes = computed(() => {
    return weekDays.value.reduce((sum, day) => sum + day.totalMinutes, 0);
});

const getIntensityClass = (count: number) => {
    if (count === 0) return 'bg-muted';
    if (count <= 2) return 'bg-green-200 dark:bg-green-900';
    if (count <= 4) return 'bg-green-300 dark:bg-green-700';
    if (count <= 6) return 'bg-green-400 dark:bg-green-600';
    return 'bg-green-500 dark:bg-green-500';
};

const maxCount = computed(() => {
    return Math.max(...weekDays.value.map(day => day.count), 1);
});
</script>

<template>
    <div class="space-y-6">
        <!-- Week Grid -->
        <div>
            <h4 class="text-sm font-semibold mb-4">This Week's Activity</h4>
            <div class="grid grid-cols-7 gap-3">
                <div
                    v-for="day in weekDays"
                    :key="day.date"
                    class="relative"
                >
                    <!-- Day Header -->
                    <div class="text-center mb-2">
                        <div class="text-xs font-semibold" :class="day.isToday ? 'text-primary' : ''">
                            {{ day.dayName }}
                        </div>
                        <div class="text-xs text-muted-foreground">{{ day.date }}</div>
                    </div>

                    <!-- Activity Box -->
                    <div
                        class="aspect-square rounded-lg transition-all hover:scale-105 flex flex-col items-center justify-center cursor-pointer"
                        :class="[
                            getIntensityClass(day.count),
                            day.isToday ? 'ring-2 ring-primary ring-offset-2' : ''
                        ]"
                        :title="`${day.count} session${day.count !== 1 ? 's' : ''} (${day.totalMinutes} min)`"
                    >
                        <div class="text-2xl font-bold" :class="day.count > 0 ? 'text-white' : 'text-muted-foreground'">
                            {{ day.count }}
                        </div>
                        <div class="text-xs" :class="day.count > 0 ? 'text-white/80' : 'text-muted-foreground'">
                            {{ day.count === 1 ? 'session' : 'sessions' }}
                        </div>
                    </div>

                    <!-- Minutes Badge -->
                    <div v-if="day.totalMinutes > 0" class="text-center mt-1">
                        <div class="text-xs font-medium">{{ day.totalMinutes }}m</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-between pt-4 border-t">
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <span>Less</span>
                <div class="flex gap-1">
                    <div class="w-4 h-4 rounded bg-muted"></div>
                    <div class="w-4 h-4 rounded bg-green-200 dark:bg-green-900"></div>
                    <div class="w-4 h-4 rounded bg-green-300 dark:bg-green-700"></div>
                    <div class="w-4 h-4 rounded bg-green-400 dark:bg-green-600"></div>
                    <div class="w-4 h-4 rounded bg-green-500 dark:bg-green-500"></div>
                </div>
                <span>More</span>
            </div>
            <div class="text-xs text-muted-foreground">
                {{ timerDuration }} min per session
            </div>
        </div>

        <!-- Daily Breakdown -->
        <div>
            <h4 class="text-sm font-semibold mb-3">Daily Breakdown</h4>
            <div class="space-y-2">
                <div
                    v-for="day in weekDays"
                    :key="day.date"
                    class="flex items-center justify-between p-3 rounded-lg"
                    :class="day.isToday ? 'bg-primary/10 border border-primary' : 'bg-muted'"
                >
                    <div class="flex items-center gap-3">
                        <div class="text-sm font-medium min-w-[60px]">
                            {{ day.dayName }}
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ day.date }}
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-sm font-bold">{{ day.count }}</div>
                            <div class="text-xs text-muted-foreground">sessions</div>
                        </div>
                        <div class="text-right min-w-[60px]">
                            <div class="text-sm font-bold">{{ day.totalMinutes }}</div>
                            <div class="text-xs text-muted-foreground">minutes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
