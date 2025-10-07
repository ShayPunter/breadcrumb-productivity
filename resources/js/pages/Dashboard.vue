<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ProductivityTimer from '../components/ProductivityTimer.vue';
import ActivityViews from '../components/ActivityViews.vue';
import { ref } from 'vue';

interface Props {
    timerDuration: number;
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
    yearData?: Record<string, {
        date: string;
        totalMinutes: number;
        sessionCount: number;
        dayOfWeek: number;
        weekOfYear: number;
        month: number;
    }>;
    recentSessions: Array<{
        id: number;
        task_description: string;
        duration: number;
        completed_at: string;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const recentSessions = ref(props.recentSessions);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6"
        >
            <!-- Timer Section -->
            <div class="grid gap-6 md:grid-cols-3">
                <div class="md:col-span-2">
                    <ProductivityTimer
                        :timer-duration="timerDuration"
                        @session-completed="(session) => {
                            recentSessions.unshift(session);
                        }"
                    />
                </div>

                <!-- Stats Card -->
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-semibold mb-4">Recent Sessions</h3>
                    <div class="space-y-3">
                        <div v-if="recentSessions.length === 0" class="text-sm text-muted-foreground">
                            No sessions yet. Start your first timer!
                        </div>
                        <div
                            v-for="session in recentSessions.slice(0, 5)"
                            :key="session.id"
                            class="text-sm"
                        >
                            <div class="font-medium truncate">{{ session.task_description }}</div>
                            <div class="text-xs text-muted-foreground">
                                {{ session.duration }} min • {{ session.completed_at }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Views with Tabs -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h3 class="text-lg font-semibold mb-6">Your Productivity Activity</h3>
                <ActivityViews
                    :today-data="todayData"
                    :week-data="weekData"
                    :month-data="monthData"
                    :year-data="yearData"
                    :timer-duration="timerDuration"
                />
            </div>
        </div>
    </AppLayout>
</template>
