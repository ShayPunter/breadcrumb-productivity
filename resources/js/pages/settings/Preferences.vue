<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { preferences } from '@/routes/settings';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import HeadingSmall from '@/components/HeadingSmall.vue';

interface Props {
    settings: {
        id: number;
        user_id: number;
        timer_duration: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Preferences',
        href: preferences().url,
    },
];

const form = useForm({
    timer_duration: props.settings.timer_duration,
});

const submit = () => {
    form.put('/settings/preferences', {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
        },
    });
};
</script>

<template>
    <Head title="Preferences" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Productivity Preferences"
                    description="Customize your productivity tracking settings"
                />

                <form @submit.prevent="submit" class="space-y-6">
                        <!-- Timer Duration -->
                        <div class="space-y-2">
                            <Label for="timer_duration">
                                Timer Duration (minutes)
                            </Label>
                            <Input
                                id="timer_duration"
                                v-model.number="form.timer_duration"
                                type="number"
                                min="1"
                                max="120"
                                required
                            />
                            <p class="text-sm text-muted-foreground">
                                Each completed timer session will light up a box on your activity grid.
                                Recommended: 10-25 minutes.
                            </p>
                            <div v-if="form.errors.timer_duration" class="text-sm text-red-500">
                                {{ form.errors.timer_duration }}
                            </div>
                        </div>

                    <!-- Current Settings Info -->
                    <div class="rounded-lg bg-muted p-4 space-y-2">
                        <h3 class="font-semibold text-sm">Current Settings</h3>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Timer Duration: {{ form.timer_duration }} minutes</li>
                            <li>• Each box = {{ form.timer_duration }} minutes of productive time</li>
                            <li>• Complete {{ Math.ceil(60 / form.timer_duration) }} sessions for 1 hour of productivity</li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="form.reset()"
                            :disabled="form.processing"
                        >
                            Reset
                        </Button>
                    </div>

                    <!-- Success Message -->
                    <div
                        v-if="form.recentlySuccessful"
                        class="text-sm text-green-600 dark:text-green-400"
                    >
                        Settings saved successfully!
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
