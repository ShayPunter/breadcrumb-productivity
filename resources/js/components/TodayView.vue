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

// Calculate boxes based on 10-minute increments
const fullBoxes = computed(() => Math.floor(props.todayData.totalMinutes / 10));
const remainingMinutes = computed(() => props.todayData.totalMinutes % 10);
const partialFill = computed(() => (remainingMinutes.value / 10) * 100);
const hasPartialBox = computed(() => remainingMinutes.value > 0);

// Total boxes to show: full boxes + partial box (if any) + 1 next box
const totalBoxes = computed(() => fullBoxes.value + (hasPartialBox.value ? 1 : 0) + 1);

const getBoxStyle = (index: number) => {
    const boxIndex = index - 1; // Convert to 0-based

    if (boxIndex < fullBoxes.value) {
        // Full green box
        return {
            class: 'bg-green-500 dark:bg-green-500',
            style: {},
            showNumber: true,
            number: boxIndex + 1,
        };
    } else if (boxIndex === fullBoxes.value && hasPartialBox.value) {
        // Partial box with gradient
        return {
            class: 'bg-muted relative overflow-hidden',
            style: {},
            showGradient: true,
            progress: partialFill.value,
            showNumber: true,
            number: boxIndex + 1,
        };
    } else {
        // Next box (dashed)
        return {
            class: 'bg-muted border-2 border-dashed border-muted-foreground/30',
            style: {},
            showNumber: false,
        };
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- Visual Progress -->
        <div v-if="todayData.totalMinutes > 0">
            <h4 class="text-sm font-semibold mb-3">Today's Progress</h4>
            <div class="flex flex-wrap gap-2">
                <div
                    v-for="index in totalBoxes"
                    :key="index"
                    class="w-12 h-12 rounded-lg transition-all hover:scale-110"
                    :class="getBoxStyle(index).class"
                    :title="getBoxStyle(index).showNumber ? `${index * 10} minutes` : 'Next 10 minutes'"
                >
                    <!-- Partial fill gradient -->
                    <div
                        v-if="getBoxStyle(index).showGradient"
                        class="absolute inset-0 bg-green-500 dark:bg-green-500 rounded-lg transition-all"
                        :style="{ height: `${getBoxStyle(index).progress}%`, bottom: 0, top: 'auto' }"
                    ></div>

                    <div class="flex items-center justify-center h-full text-sm font-bold relative z-10"
                         :class="getBoxStyle(index).showNumber ? 'text-white' : 'text-muted-foreground'">
                        {{ getBoxStyle(index).showNumber ? getBoxStyle(index).number : '?' }}
                    </div>
                </div>
            </div>
            <p class="text-xs text-muted-foreground mt-3">
                {{ todayData.totalMinutes }} minutes tracked today • Each box = 10 minutes
            </p>
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
