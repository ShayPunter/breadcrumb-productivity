<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get user settings
        $settings = $user->settings()->firstOrCreate(
            ['user_id' => $user->id],
            ['timer_duration' => 10]
        );

        // Get all sessions
        $allSessions = $user->productivitySessions()->get();

        // Today's sessions
        $todaySessions = $user->productivitySessions()
            ->whereDate('completed_at', Carbon::today())
            ->get();

        // This week's sessions
        $weekSessions = $user->productivitySessions()
            ->whereBetween('completed_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->get();

        // This month's sessions
        $monthSessions = $user->productivitySessions()
            ->whereBetween('completed_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->get();

        // All-time stats
        $allTimeStats = [
            'totalSessions' => $allSessions->count(),
            'totalMinutes' => $allSessions->sum('duration'),
            'totalHours' => round($allSessions->sum('duration') / 60, 1),
            'averagePerSession' => $allSessions->count() > 0 ? round($allSessions->sum('duration') / $allSessions->count(), 1) : 0,
            'firstSessionDate' => $allSessions->first()?->completed_at?->format('M d, Y'),
            'daysActive' => $allSessions->pluck('completed_at')->map(fn($date) => $date->format('Y-m-d'))->unique()->count(),
        ];

        // Today stats
        $todayStats = [
            'sessions' => $todaySessions->count(),
            'minutes' => $todaySessions->sum('duration'),
            'hours' => round($todaySessions->sum('duration') / 60, 1),
        ];

        // Week stats
        $weekStats = [
            'sessions' => $weekSessions->count(),
            'minutes' => $weekSessions->sum('duration'),
            'hours' => round($weekSessions->sum('duration') / 60, 1),
            'averagePerDay' => round($weekSessions->count() / 7, 1),
        ];

        // Month stats
        $monthStats = [
            'sessions' => $monthSessions->count(),
            'minutes' => $monthSessions->sum('duration'),
            'hours' => round($monthSessions->sum('duration') / 60, 1),
            'averagePerDay' => round($monthSessions->count() / Carbon::now()->day, 1),
        ];

        // Most productive day of week
        $dayOfWeekCounts = $allSessions->groupBy(function ($session) {
            return $session->completed_at->format('l'); // Monday, Tuesday, etc.
        })->map->count()->sortDesc();

        // Most productive hour
        $hourCounts = $allSessions->groupBy(function ($session) {
            return $session->completed_at->format('H'); // 00-23
        })->map->count()->sortDesc();

        // Recent milestones
        $milestones = [];
        $totalSessions = $allSessions->count();
        $milestoneNumbers = [10, 25, 50, 100, 250, 500, 1000];

        foreach ($milestoneNumbers as $milestone) {
            if ($totalSessions >= $milestone) {
                $milestones[] = [
                    'count' => $milestone,
                    'achieved' => true,
                ];
            }
        }

        return Inertia::render('Stats', [
            'timerDuration' => $settings->timer_duration,
            'allTimeStats' => $allTimeStats,
            'todayStats' => $todayStats,
            'weekStats' => $weekStats,
            'monthStats' => $monthStats,
            'mostProductiveDay' => $dayOfWeekCounts->keys()->first(),
            'mostProductiveHour' => $hourCounts->keys()->first() ? (int)$hourCounts->keys()->first() : null,
            'milestones' => $milestones,
        ]);
    }
}
