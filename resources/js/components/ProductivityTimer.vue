<script setup lang="ts">
import { ref, computed, onUnmounted, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Props {
    timerDuration: number;
}

interface TimerState {
    taskDescription: string;
    timeRemaining: number;
    isRunning: boolean;
    startTime: number | null;
    timerDuration: number;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    sessionCompleted: [session: any];
}>();

const STORAGE_KEY = 'productivity_timer_state';

const taskDescription = ref('');
const timeRemaining = ref(props.timerDuration * 60); // Convert to seconds
const isRunning = ref(false);
const intervalId = ref<number | null>(null);
const startTime = ref<number | null>(null);

const formattedTime = computed(() => {
    const minutes = Math.floor(timeRemaining.value / 60);
    const seconds = timeRemaining.value % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const progressPercentage = computed(() => {
    const totalSeconds = props.timerDuration * 60;
    return ((totalSeconds - timeRemaining.value) / totalSeconds) * 100;
});

const saveState = () => {
    const state: TimerState = {
        taskDescription: taskDescription.value,
        timeRemaining: timeRemaining.value,
        isRunning: isRunning.value,
        startTime: startTime.value,
        timerDuration: props.timerDuration,
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
};

const loadState = () => {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (!saved) return;

    try {
        const state: TimerState = JSON.parse(saved);

        // Check if the timer duration hasn't changed
        if (state.timerDuration !== props.timerDuration) {
            localStorage.removeItem(STORAGE_KEY);
            return;
        }

        taskDescription.value = state.taskDescription;

        // If timer was running, calculate elapsed time and resume
        if (state.isRunning && state.startTime) {
            const elapsed = Math.floor((Date.now() - state.startTime) / 1000);
            const remaining = state.timeRemaining - elapsed;

            if (remaining > 0) {
                timeRemaining.value = remaining;
                isRunning.value = true;
                startTime.value = Date.now() - (elapsed * 1000);
                startTimerInterval();
            } else {
                // Timer completed while away
                completeSession();
            }
        } else {
            timeRemaining.value = state.timeRemaining;
        }
    } catch (e) {
        console.error('Failed to load timer state:', e);
        localStorage.removeItem(STORAGE_KEY);
    }
};

const clearState = () => {
    localStorage.removeItem(STORAGE_KEY);
};

const startTimerInterval = () => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
    }

    intervalId.value = window.setInterval(() => {
        timeRemaining.value--;
        saveState();

        if (timeRemaining.value <= 0) {
            completeSession();
        }
    }, 1000);
};

const startTimer = () => {
    if (!taskDescription.value.trim()) {
        alert('Please enter what you are working on');
        return;
    }

    isRunning.value = true;
    startTime.value = Date.now();
    saveState();
    startTimerInterval();
};

const pauseTimer = () => {
    isRunning.value = false;
    startTime.value = null;
    if (intervalId.value) {
        clearInterval(intervalId.value);
        intervalId.value = null;
    }
    saveState();
};

const resetTimer = () => {
    pauseTimer();
    timeRemaining.value = props.timerDuration * 60;
    taskDescription.value = '';
    clearState();
};

const completeSession = () => {
    pauseTimer();
    clearState();

    // Save the session
    router.post('/sessions', {
        task_description: taskDescription.value,
        duration: props.timerDuration,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            emit('sessionCompleted', {
                task_description: taskDescription.value,
                duration: props.timerDuration,
                completed_at: 'Just now',
            });

            // Show celebration
            alert('🎉 Great job! You completed a productive session!');

            // Reset for next session
            taskDescription.value = '';
            timeRemaining.value = props.timerDuration * 60;
        },
    });
};

onMounted(() => {
    loadState();
});

onUnmounted(() => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
    }
    // Save state when component unmounts (navigation)
    if (isRunning.value) {
        saveState();
    }
});
</script>

<template>
    <div class="rounded-xl border border-sidebar-border/70 bg-card p-8 dark:border-sidebar-border">
        <h2 class="text-2xl font-bold mb-6">Productivity Timer</h2>

        <div class="space-y-6">
            <!-- Timer Display -->
            <div class="relative">
                <div class="flex items-center justify-center h-48">
                    <div class="text-center">
                        <div class="text-6xl font-bold font-mono mb-2">
                            {{ formattedTime }}
                        </div>
                        <div class="text-sm text-muted-foreground">
                            {{ timerDuration }} minute session
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full h-2 bg-muted rounded-full overflow-hidden mt-4">
                    <div
                        class="h-full bg-primary transition-all duration-1000 ease-linear"
                        :style="{ width: `${progressPercentage}%` }"
                    ></div>
                </div>
            </div>

            <!-- Task Input -->
            <div class="space-y-2">
                <Label for="task">What are you working on?</Label>
                <Input
                    id="task"
                    v-model="taskDescription"
                    placeholder="e.g., Writing documentation, Coding feature X..."
                    :disabled="isRunning"
                    @keyup.enter="!isRunning && startTimer()"
                />
            </div>

            <!-- Controls -->
            <div class="flex gap-3">
                <Button
                    v-if="!isRunning"
                    @click="startTimer"
                    class="flex-1"
                    size="lg"
                >
                    Start Timer
                </Button>
                <Button
                    v-else
                    @click="pauseTimer"
                    variant="secondary"
                    class="flex-1"
                    size="lg"
                >
                    Pause
                </Button>
                <Button
                    @click="resetTimer"
                    variant="outline"
                    size="lg"
                >
                    Reset
                </Button>
            </div>
        </div>
    </div>
</template>
