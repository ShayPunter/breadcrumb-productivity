<script setup lang="ts">
import { computed } from 'vue';

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
    timerDuration: number;
}

const props = defineProps<Props>();

// Show boxes for completed sessions + 1 gray box for "next session"
const totalBoxes = computed(() => props.todayData.completed + 1);

const getBoxClass = (index: number) => {
    if (index < props.todayData.completed) {
        return 'bg-green-500 dark:bg-green-500';
    }
    return 'bg-muted border-2 border-dashed border-muted-foreground/30';
};
</script>

<template>
    <div class="space-y-6">
        <!-- Visual Progress -->
        <div>
            <h4 class="text-sm font-semibold mb-3">Today's Progress</h4>
            <div class="flex flex-wrap gap-2">
                <div
                    v-for="index in totalBoxes"
                    :key="index"
                    class="w-12 h-12 rounded-lg transition-all hover:scale-110"
                    :class="getBoxClass(index - 1)"
                    :title="index <= todayData.completed ? `Session ${index}` : 'Next session'"
                >
                    <div class="flex items-center justify-center h-full text-sm font-bold text-white">
                        {{ index <= todayData.completed ? index : '?' }}
                    </div>
                </div>
            </div>
            <p class="text-xs text-muted-foreground mt-3">
                Each box represents one completed {{ timerDuration }}-minute session
            </p>
        </div>

        <!-- Session Details -->
        <div v-if="todayData.sessions.length > 0">
            <h4 class="text-sm font-semibold mb-3">Session Details</h4>
            <div class="space-y-2">
                <div
                    v-for="(session, index) in todayData.sessions"
                    :key="index"
                    class="flex items-center justify-between p-3 rounded-lg bg-muted"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center text-white text-sm font-bold">
                            {{ index + 1 }}
                        </div>
                        <div>
                            <div class="font-medium">{{ session.task_description }}</div>
                            <div class="text-xs text-muted-foreground">
                                Completed at {{ session.completed_at }}
                            </div>
                        </div>
                    </div>
                    <div class="text-sm font-medium">{{ session.duration }} min</div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-lg font-semibold mb-2">No sessions today yet</h3>
            <p class="text-sm text-muted-foreground">
                Start your first timer to begin tracking your productivity!
            </p>
        </div>
    </div>
</template>
