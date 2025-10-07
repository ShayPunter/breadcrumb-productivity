<script setup lang="ts">
import { ref, computed, onUnmounted, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    timerDuration: number;
}

interface TimerState {
    taskDescription: string;
    timeRemaining: number;
    isRunning: boolean;
    startTime: number | null;
    timerDuration: number;
    customDuration: number;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    sessionCompleted: [session: any];
}>();

const STORAGE_KEY = 'productivity_timer_state';

const taskDescription = ref('');
const customDuration = ref(props.timerDuration);
const timeRemaining = ref(props.timerDuration * 60); // Convert to seconds
const isRunning = ref(false);
const intervalId = ref<number | null>(null);
const startTime = ref<number | null>(null);
const showCompletionDialog = ref(false);
const completedTaskName = ref('');
const taskError = ref('');

// Preset durations in minutes
const presets = [5, 10, 15, 25, 30, 45, 60];

const formattedTime = computed(() => {
    const minutes = Math.floor(timeRemaining.value / 60);
    const seconds = timeRemaining.value % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const progressPercentage = computed(() => {
    const totalSeconds = customDuration.value * 60;
    return ((totalSeconds - timeRemaining.value) / totalSeconds) * 100;
});

const setDuration = (minutes: number) => {
    if (isRunning.value) return;
    customDuration.value = Math.max(1, minutes);
    timeRemaining.value = customDuration.value * 60;
};

watch(customDuration, (newDuration) => {
    if (!isRunning.value) {
        // Ensure minimum of 1 minute
        const validDuration = Math.max(1, newDuration || 1);
        if (validDuration !== newDuration) {
            customDuration.value = validDuration;
        }
        timeRemaining.value = validDuration * 60;
    }
});

const saveState = () => {
    const state: TimerState = {
        taskDescription: taskDescription.value,
        timeRemaining: timeRemaining.value,
        isRunning: isRunning.value,
        startTime: startTime.value,
        timerDuration: props.timerDuration,
        customDuration: customDuration.value,
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
};

const loadState = () => {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (!saved) return;

    try {
        const state: TimerState = JSON.parse(saved);

        // Restore custom duration if saved
        if (state.customDuration) {
            customDuration.value = state.customDuration;
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
        taskError.value = 'Please enter what you are working on';
        return;
    }

    taskError.value = '';
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
    timeRemaining.value = customDuration.value * 60;
    taskDescription.value = '';
    clearState();
};

const playCompletionSound = () => {
    try {
        const audio = new Audio('/assets/done.mp3');
        audio.play().catch(err => {
            console.error('Failed to play completion sound:', err);
        });
    } catch (err) {
        console.error('Failed to create audio:', err);
    }
};

const completeSession = () => {
    pauseTimer();

    // Store the task description before clearing
    const completedTask = taskDescription.value;
    completedTaskName.value = completedTask;

    // Play completion sound
    playCompletionSound();

    // Save the session
    router.post('/sessions', {
        task_description: completedTask,
        duration: customDuration.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Show celebration dialog
            showCompletionDialog.value = true;

            // Reset for next session with same duration and save state
            taskDescription.value = '';
            timeRemaining.value = customDuration.value * 60;
            saveState();

            // Emit event
            emit('sessionCompleted', {
                task_description: completedTask,
                duration: customDuration.value,
                completed_at: 'Just now',
            });

            // Reload to refresh activity data
            router.reload({ preserveScroll: true });
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
            <!-- Duration Selection -->
            <div v-if="!isRunning" class="space-y-3">
                <Label>Session Duration</Label>
                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="preset in presets"
                        :key="preset"
                        :variant="customDuration === preset ? 'default' : 'outline'"
                        size="sm"
                        @click="setDuration(preset)"
                    >
                        {{ preset }}m
                    </Button>
                </div>
                <div class="flex items-center gap-2">
                    <Input
                        v-model.number="customDuration"
                        type="number"
                        min="1"
                        max="180"
                        class="w-24"
                    />
                    <span class="text-sm text-muted-foreground">minutes</span>
                </div>
            </div>

            <!-- Timer Display -->
            <div class="relative">
                <div class="flex items-center justify-center h-40">
                    <div class="text-center">
                        <div class="text-6xl font-bold font-mono mb-2">
                            {{ formattedTime }}
                        </div>
                        <div class="text-sm text-muted-foreground">
                            {{ customDuration }} minute session
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
                    :class="{ 'border-destructive': taskError }"
                    @keyup.enter="!isRunning && startTimer()"
                    @input="taskError = ''"
                />
                <p v-if="taskError" class="text-sm text-destructive">
                    {{ taskError }}
                </p>
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

        <!-- Completion Dialog -->
        <Dialog v-model:open="showCompletionDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>🎉 Session Complete!</DialogTitle>
                    <DialogDescription class="pt-4 space-y-2">
                        <p class="text-base">Great job! You completed a productive session.</p>
                        <p class="font-medium text-foreground">{{ completedTaskName }}</p>
                        <p class="text-sm text-muted-foreground">{{ customDuration }} minutes tracked</p>
                    </DialogDescription>
                </DialogHeader>
                <div class="flex justify-end pt-4">
                    <Button @click="showCompletionDialog = false">
                        Continue
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
