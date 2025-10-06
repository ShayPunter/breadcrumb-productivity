<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Award, Calendar, Clock, TrendingUp, Zap } from 'lucide-vue-next';

interface Props {
    timerDuration: number;
    allTimeStats: {
        totalSessions: number;
        totalMinutes: number;
        totalHours: number;
        averagePerSession: number;
        firstSessionDate: string | null;
        daysActive: number;
    };
    todayStats: {
        sessions: number;
        minutes: number;
        hours: number;
    };
    weekStats: {
        sessions: number;
        minutes: number;
        hours: number;
        averagePerDay: number;
    };
    monthStats: {
        sessions: number;
        minutes: number;
        hours: number;
        averagePerDay: number;
    };
    mostProductiveDay: string | null;
    mostProductiveHour: number | null;
    milestones: Array<{
        count: number;
        achieved: boolean;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Statistics',
        href: '/stats',
    },
];

const formatHour = (hour: number | null) => {
    if (hour === null) return 'N/A';
    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:00 ${period}`;
};
</script>

<template>
    <Head title="Statistics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
            <div>
                <h1 class="text-3xl font-bold mb-2">Productivity Statistics</h1>
                <p class="text-muted-foreground">
                    Track your progress and analyze your productivity patterns
                </p>
            </div>

            <!-- All-Time Stats -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                    <TrendingUp class="w-5 h-5" />
                    All-Time Statistics
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="rounded-lg bg-gradient-to-br from-green-500 to-green-600 p-4 text-white">
                        <div class="text-3xl font-bold">{{ allTimeStats.totalSessions }}</div>
                        <div class="text-sm opacity-90">Total Sessions</div>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white">
                        <div class="text-3xl font-bold">{{ allTimeStats.totalHours }}h</div>
                        <div class="text-sm opacity-90">Total Hours</div>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 p-4 text-white">
                        <div class="text-3xl font-bold">{{ allTimeStats.totalMinutes }}</div>
                        <div class="text-sm opacity-90">Total Minutes</div>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 p-4 text-white">
                        <div class="text-3xl font-bold">{{ allTimeStats.averagePerSession }}</div>
                        <div class="text-sm opacity-90">Avg Min/Session</div>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-pink-500 to-pink-600 p-4 text-white">
                        <div class="text-3xl font-bold">{{ allTimeStats.daysActive }}</div>
                        <div class="text-sm opacity-90">Days Active</div>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 p-4 text-white">
                        <div class="text-xl font-bold">{{ timerDuration }}min</div>
                        <div class="text-sm opacity-90">Per Session</div>
                    </div>
                </div>
                <div v-if="allTimeStats.firstSessionDate" class="mt-4 text-sm text-muted-foreground">
                    Member since {{ allTimeStats.firstSessionDate }}
                </div>
            </div>

            <!-- Period Comparisons -->
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Today -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <Zap class="w-5 h-5 text-yellow-500" />
                        Today
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-3xl font-bold">{{ todayStats.sessions }}</div>
                            <div class="text-sm text-muted-foreground">Sessions</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ todayStats.hours }}h</div>
                            <div class="text-sm text-muted-foreground">Hours Tracked</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold">{{ todayStats.minutes }} min</div>
                            <div class="text-sm text-muted-foreground">Total Minutes</div>
                        </div>
                    </div>
                </div>

                <!-- This Week -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <Calendar class="w-5 h-5 text-blue-500" />
                        This Week
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-3xl font-bold">{{ weekStats.sessions }}</div>
                            <div class="text-sm text-muted-foreground">Sessions</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ weekStats.hours }}h</div>
                            <div class="text-sm text-muted-foreground">Hours Tracked</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold">{{ weekStats.averagePerDay }}</div>
                            <div class="text-sm text-muted-foreground">Avg Sessions/Day</div>
                        </div>
                    </div>
                </div>

                <!-- This Month -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <Clock class="w-5 h-5 text-purple-500" />
                        This Month
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-3xl font-bold">{{ monthStats.sessions }}</div>
                            <div class="text-sm text-muted-foreground">Sessions</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">{{ monthStats.hours }}h</div>
                            <div class="text-sm text-muted-foreground">Hours Tracked</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold">{{ monthStats.averagePerDay }}</div>
                            <div class="text-sm text-muted-foreground">Avg Sessions/Day</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insights -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Productivity Patterns -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-4">Productivity Patterns</h3>
                    <div class="space-y-4">
                        <div class="p-4 rounded-lg bg-muted">
                            <div class="text-sm text-muted-foreground mb-1">Most Productive Day</div>
                            <div class="text-2xl font-bold">{{ mostProductiveDay || 'N/A' }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-muted">
                            <div class="text-sm text-muted-foreground mb-1">Most Productive Hour</div>
                            <div class="text-2xl font-bold">{{ formatHour(mostProductiveHour) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Milestones -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <Award class="w-5 h-5 text-yellow-500" />
                        Milestones Achieved
                    </h3>
                    <div v-if="milestones.length > 0" class="flex flex-wrap gap-3">
                        <div
                            v-for="milestone in milestones"
                            :key="milestone.count"
                            class="px-4 py-2 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold"
                        >
                            {{ milestone.count }} sessions
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-muted-foreground">
                        Complete 10 sessions to unlock your first milestone!
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
