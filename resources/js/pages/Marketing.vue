<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { CheckCircle2, Timer, TrendingUp, Zap, Users, Clock } from 'lucide-vue-next';

interface Props {
    stats: {
        totalSessions: string;
        totalMinutes: string;
        totalHours: string;
        totalUsers: string;
        activeUsers: string;
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Get Stuff Done - Track Your Productivity" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <header class="border-b border-sidebar-border sticky top-0 z-50 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="container mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                        <Timer class="w-6 h-6 text-white" />
                    </div>
                    <span class="text-xl font-bold">Breadcrumb</span>
                </div>
                <div class="flex items-center gap-4">
                    <Link href="/login">
                        <Button variant="ghost">Log In</Button>
                    </Link>
                    <Link href="/register">
                        <Button>Get Started Free</Button>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="container mx-auto px-4 py-20 text-center">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-7xl font-bold mb-6">
                    Get Stuff Done, Simply
                </h1>
                <p class="text-xl md:text-2xl text-muted-foreground mb-8">
                    Track your productivity with customizable timers. See your progress grow with every completed session.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/register">
                        <Button size="lg" class="text-lg px-8 py-6">
                            Start Tracking Free
                        </Button>
                    </Link>
                    <Link href="/login">
                        <Button size="lg" variant="outline" class="text-lg px-8 py-6">
                            Sign In
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Platform Stats -->
        <section class="container mx-auto px-4 py-16">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12">Join Our Productive Community</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <div class="text-center p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="text-4xl font-bold text-green-600 mb-2">{{ stats.totalSessions }}</div>
                        <div class="text-sm text-muted-foreground">Sessions Completed</div>
                    </div>
                    <div class="text-center p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="text-4xl font-bold text-blue-600 mb-2">{{ stats.totalHours }}</div>
                        <div class="text-sm text-muted-foreground">Hours Logged</div>
                    </div>
                    <div class="text-center p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="text-4xl font-bold text-purple-600 mb-2">{{ stats.totalMinutes }}</div>
                        <div class="text-sm text-muted-foreground">Minutes Tracked</div>
                    </div>
                    <div class="text-center p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="text-4xl font-bold text-orange-600 mb-2">{{ stats.totalUsers }}</div>
                        <div class="text-sm text-muted-foreground">Total Users</div>
                    </div>
                    <div class="text-center p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="text-4xl font-bold text-pink-600 mb-2">{{ stats.activeUsers }}</div>
                        <div class="text-sm text-muted-foreground">Active Users</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="container mx-auto px-4 py-16">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12">Everything You Need to Stay Productive</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center mb-4">
                            <Timer class="w-6 h-6 text-green-600" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Customizable Timer</h3>
                        <p class="text-muted-foreground">
                            Set your ideal session length. Default 10 minutes, or customize from 1-120 minutes to match your workflow.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center mb-4">
                            <TrendingUp class="w-6 h-6 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Visual Progress</h3>
                        <p class="text-muted-foreground">
                            See your productivity grow with GitHub-style activity grids. Track daily, weekly, and monthly progress.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center mb-4">
                            <Zap class="w-6 h-6 text-purple-600" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Detailed Analytics</h3>
                        <p class="text-muted-foreground">
                            Understand your productivity patterns. See your most productive days, hours, and earn milestones.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900 flex items-center justify-center mb-4">
                            <CheckCircle2 class="w-6 h-6 text-orange-600" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Task Tracking</h3>
                        <p class="text-muted-foreground">
                            Log what you're working on with each session. Review your completed tasks and stay organized.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="w-12 h-12 rounded-lg bg-pink-100 dark:bg-pink-900 flex items-center justify-center mb-4">
                            <Clock class="w-6 h-6 text-pink-600" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Persistent Timer</h3>
                        <p class="text-muted-foreground">
                            Navigate away and come back - your timer keeps running. Never lose track of your current session.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="p-6 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                        <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mb-4">
                            <Users class="w-6 h-6 text-indigo-600" />
                        </div>
                        <h3 class="text-xl font-bold mb-2">Personal Dashboard</h3>
                        <p class="text-muted-foreground">
                            Your own productivity hub. Clean, simple interface that helps you focus on getting things done.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="container mx-auto px-4 py-16 rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border my-16">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
                <div class="space-y-8">
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold flex-shrink-0">1</div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Set Your Task</h3>
                            <p class="text-muted-foreground">Enter what you're working on - could be anything from "Writing docs" to "Learning Vue.js"</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold flex-shrink-0">2</div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Start the Timer</h3>
                            <p class="text-muted-foreground">Click start and focus on your task. The timer counts down from your configured duration.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold flex-shrink-0">3</div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Complete & Track</h3>
                            <p class="text-muted-foreground">When the timer finishes, your session is logged. Watch your activity grid light up!</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold flex-shrink-0">4</div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Analyze Your Progress</h3>
                            <p class="text-muted-foreground">Review your stats, find patterns, and celebrate your productivity milestones.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="container mx-auto px-4 py-20 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Ready to Get Productive?</h2>
                <p class="text-xl text-muted-foreground mb-8">
                    Join {{ stats.activeUsers }} active users tracking their productivity. It's free to start.
                </p>
                <Link href="/register">
                    <Button size="lg" class="text-lg px-12 py-6">
                        Start Your First Session
                    </Button>
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-sidebar-border mt-20 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="container mx-auto px-4 py-8 text-center text-sm text-muted-foreground">
                <p>&copy; 2025 Breadcrumb Productivity. Built to help you get stuff done.</p>
            </div>
        </footer>
    </div>
</template>
