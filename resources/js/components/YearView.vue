<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    yearData: Record<string, {
        date: string;
        totalMinutes: number;
        sessionCount: number;
        dayOfWeek: number; // 0-6, Sunday-Saturday
        weekOfYear: number;
        month: number; // 1-12
    }>;
    timerDuration: number;
}

const props = defineProps<Props>();

// Generate full year of days (last 365 days) if no data provided
const generateYearData = () => {
    const result: Record<string, {
        date: string;
        totalMinutes: number;
        sessionCount: number;
        dayOfWeek: number;
        weekOfYear: number;
        month: number;
    }> = {};

    const today = new Date();

    // Start from 52 weeks ago (364 days) to show full year
    const startDate = new Date(today);
    startDate.setDate(today.getDate() - 364); // 365 days including today

    // Adjust to start from Sunday of that week
    const dayOfWeek = startDate.getDay();
    startDate.setDate(startDate.getDate() - dayOfWeek);

    // Calculate total days to include (should be 7 * 53 = 371 days to fill the grid)
    const totalDays = 7 * 53;

    for (let i = 0; i < totalDays; i++) {
        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);

        const dateStr = currentDate.toISOString().split('T')[0];
        const currentDayOfWeek = currentDate.getDay(); // 0-6, Sunday-Saturday

        // Calculate week index (0-52) from start date
        const weekIndex = Math.floor(i / 7);

        result[dateStr] = {
            date: dateStr,
            totalMinutes: 0,
            sessionCount: 0,
            dayOfWeek: currentDayOfWeek,
            weekOfYear: weekIndex,
            month: currentDate.getMonth() + 1 // 1-12
        };
    }

    return result;
};

const allYearData = computed(() => {
    const generated = generateYearData();

    // Merge with actual data if provided
    if (props.yearData) {
        Object.keys(props.yearData).forEach(key => {
            if (generated[key]) {
                generated[key] = props.yearData![key];
            }
        });
    }

    return generated;
});

const days = computed(() => Object.values(allYearData.value));

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

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr);
    const day = date.getDate();
    const month = date.toLocaleDateString('en-US', { month: 'long' });
    const year = date.getFullYear();

    // Add ordinal suffix (st, nd, rd, th)
    const suffix = (day: number) => {
        if (day > 3 && day < 21) return 'th';
        switch (day % 10) {
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
            default: return 'th';
        }
    };

    return `${day}${suffix(day)} ${month} ${year}`;
};

// Organize days into weeks (rows) for GitHub-style display
const weeks = computed(() => {
    const weekMap: Record<number, (typeof days.value[0] | null)[]> = {};
    days.value.forEach(day => {
        if (!weekMap[day.weekOfYear]) {
            weekMap[day.weekOfYear] = Array(7).fill(null);
        }
        weekMap[day.weekOfYear][day.dayOfWeek] = day;
    });
    return Object.values(weekMap);
});

// Calculate month labels and their positions
const monthLabels = computed(() => {
    const labels: { month: string; weekIndex: number }[] = [];
    let lastMonth = -1;

    weeks.value.forEach((week, weekIndex) => {
        const firstDay = week.find(d => d !== null);
        if (firstDay && firstDay.month !== lastMonth) {
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            labels.push({
                month: monthNames[firstDay.month - 1],
                weekIndex
            });
            lastMonth = firstDay.month;
        }
    });

    return labels;
});

const dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold">Activity Overview</h4>
            <div class="text-sm text-muted-foreground">
                {{ formatHoursMinutes(totalMinutes) }} total this year
            </div>
        </div>

        <div class="overflow-x-auto pb-2">
            <div class="inline-flex flex-col gap-1 min-w-full">
                <!-- Month labels row -->
                <div class="flex gap-1 pl-[52px]">
                    <div class="flex gap-1">
                        <div
                            v-for="(label, index) in monthLabels"
                            :key="index"
                            class="text-xs text-muted-foreground font-medium"
                            :style="{
                                width: '16px',
                                marginLeft: index === 0 ? '0' : `${(label.weekIndex - (monthLabels[index - 1]?.weekIndex || 0) - 1) * 11 + (label.weekIndex - (monthLabels[index - 1]?.weekIndex || 0) - 1) * 4}px`
                            }"
                        >
                            {{ label.month }}
                        </div>
                    </div>
                </div>

                <!-- Contribution graph -->
                <div class="flex gap-1">
                    <!-- Day labels column -->
                    <div class="flex flex-col gap-1 w-12 pr-2">
                        <div
                            v-for="(label, index) in dayLabels"
                            :key="index"
                            class="h-3 flex items-center text-[11px] text-muted-foreground"
                            :class="{ 'opacity-0': index % 2 !== 0 }"
                        >
                            {{ label }}
                        </div>
                    </div>

                    <!-- Week columns -->
                    <div class="flex gap-1">
                        <div
                            v-for="(week, weekIndex) in weeks"
                            :key="weekIndex"
                            class="flex flex-col gap-1"
                        >
                            <div
                                v-for="(day, dayIndex) in week"
                                :key="dayIndex"
                                class="w-3 h-3 rounded-sm transition-all cursor-pointer hover:ring-2 hover:ring-primary/50"
                                :class="day ? getIntensityClass(day.totalMinutes) : 'bg-transparent'"
                                :title="day ? `${formatDate(day.date)}\n${day.sessionCount} session${day.sessionCount !== 1 ? 's' : ''}\n${formatHoursMinutes(day.totalMinutes)}` : ''"
                            ></div>
                        </div>
                    </div>
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
