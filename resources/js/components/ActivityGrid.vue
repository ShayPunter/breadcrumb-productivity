<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    activityData: Record<string, {
        count: number;
        totalMinutes: number;
        sessions: Array<{
            task_description: string;
            duration: number;
            completed_at: string;
        }>;
    }>;
    timerDuration: number;
}

const props = defineProps<Props>();

const hoveredDay = ref<string | null>(null);

// Generate grid data for the last year (52 weeks)
const gridData = computed(() => {
    const weeks: Array<Array<{ date: string; count: number; sessions: any[] }>> = [];
    const today = new Date();
    const startDate = new Date(today);
    startDate.setFullYear(today.getFullYear() - 1);

    let currentWeek: Array<{ date: string; count: number; sessions: any[] }> = [];

    for (let d = new Date(startDate); d <= today; d.setDate(d.getDate() + 1)) {
        const dateStr = d.toISOString().split('T')[0];
        const dayData = props.activityData[dateStr] || { count: 0, sessions: [] };

        currentWeek.push({
            date: dateStr,
            count: dayData.count || 0,
            sessions: dayData.sessions || [],
        });

        // Start a new week on Sunday
        if (d.getDay() === 6 || d.getTime() === today.getTime()) {
            weeks.push([...currentWeek]);
            currentWeek = [];
        }
    }

    return weeks;
});

const getIntensityClass = (count: number) => {
    if (count === 0) return 'bg-muted';
    if (count === 1) return 'bg-green-200 dark:bg-green-900';
    if (count === 2) return 'bg-green-300 dark:bg-green-700';
    if (count === 3) return 'bg-green-400 dark:bg-green-600';
    return 'bg-green-500 dark:bg-green-500';
};

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const monthLabels = computed(() => {
    const labels: Array<{ month: string; index: number }> = [];
    const today = new Date();
    const startDate = new Date(today);
    startDate.setFullYear(today.getFullYear() - 1);

    let currentMonth = startDate.getMonth();
    let weekIndex = 0;

    gridData.value.forEach((week, index) => {
        const firstDay = new Date(week[0].date);
        const month = firstDay.getMonth();

        if (month !== currentMonth || index === 0) {
            labels.push({
                month: firstDay.toLocaleDateString('en-US', { month: 'short' }),
                index: weekIndex,
            });
            currentMonth = month;
        }
        weekIndex++;
    });

    return labels;
});
</script>

<template>
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full">
            <!-- Month labels -->
            <div class="flex gap-1 mb-2 text-xs text-muted-foreground">
                <div
                    v-for="(label, index) in monthLabels"
                    :key="index"
                    :style="{ marginLeft: label.index === 0 ? '20px' : `${(label.index - (monthLabels[index - 1]?.index || 0)) * 14}px` }"
                    class="inline-block"
                >
                    {{ label.month }}
                </div>
            </div>

            <!-- Activity grid -->
            <div class="flex gap-1">
                <!-- Day labels -->
                <div class="flex flex-col gap-1 text-xs text-muted-foreground justify-around mr-2">
                    <div>Mon</div>
                    <div>Wed</div>
                    <div>Fri</div>
                </div>

                <!-- Grid -->
                <div class="flex gap-1">
                    <div
                        v-for="(week, weekIndex) in gridData"
                        :key="weekIndex"
                        class="flex flex-col gap-1"
                    >
                        <div
                            v-for="(day, dayIndex) in week"
                            :key="dayIndex"
                            class="w-3 h-3 rounded-sm cursor-pointer transition-all hover:ring-2 hover:ring-primary"
                            :class="getIntensityClass(day.count)"
                            :title="`${formatDate(day.date)}: ${day.count} session${day.count !== 1 ? 's' : ''}`"
                            @mouseenter="hoveredDay = day.date"
                            @mouseleave="hoveredDay = null"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex items-center gap-2 mt-4 text-xs text-muted-foreground">
                <span>Less</span>
                <div class="flex gap-1">
                    <div class="w-3 h-3 rounded-sm bg-muted"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-200 dark:bg-green-900"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-300 dark:bg-green-700"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-400 dark:bg-green-600"></div>
                    <div class="w-3 h-3 rounded-sm bg-green-500 dark:bg-green-500"></div>
                </div>
                <span>More</span>
            </div>

            <!-- Hovered day details -->
            <div v-if="hoveredDay && activityData[hoveredDay]" class="mt-4 p-3 rounded-lg bg-muted">
                <div class="font-semibold mb-2">{{ formatDate(hoveredDay) }}</div>
                <div class="text-sm mb-2">
                    {{ activityData[hoveredDay].count }} session{{ activityData[hoveredDay].count !== 1 ? 's' : '' }}
                    ({{ activityData[hoveredDay].totalMinutes }} minutes)
                </div>
                <div class="space-y-1 text-sm">
                    <div
                        v-for="(session, index) in activityData[hoveredDay].sessions"
                        :key="index"
                        class="text-muted-foreground"
                    >
                        • {{ session.task_description }} ({{ session.duration }} min at {{ session.completed_at }})
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
