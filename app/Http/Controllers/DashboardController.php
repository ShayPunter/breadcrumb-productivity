<?php

namespace App\Http\Controllers;

use App\Models\ProductivitySession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get or create user settings
        $settings = $user->settings()->firstOrCreate(
            ['user_id' => $user->id],
            ['timer_duration' => 10]
        );

        // Get today's sessions
        $todaySessions = $user->productivitySessions()
            ->whereDate('completed_at', Carbon::today())
            ->get();

        // Get this week's sessions
        $weekSessions = $user->productivitySessions()
            ->whereBetween('completed_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->get();

        // Get this month's sessions
        $monthSessions = $user->productivitySessions()
            ->whereBetween('completed_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->get();

        // Get all recent sessions for the list
        $recentSessions = $user->productivitySessions()
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Today's data - session count
        $todayData = [
            'completed' => $todaySessions->count(),
            'totalMinutes' => $todaySessions->sum('duration'),
            'sessions' => $todaySessions->map(function ($session) {
                return [
                    'task_description' => $session->task_description,
                    'duration' => $session->duration,
                    'completed_at' => $session->completed_at->format('H:i'),
                ];
            })->toArray(),
        ];

        // Week data - group by day
        $weekData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->startOfWeek()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $daySessions = $weekSessions->filter(function ($session) use ($dateStr) {
                return $session->completed_at->format('Y-m-d') === $dateStr;
            });

            $weekData[$dateStr] = [
                'date' => $date->format('M d'),
                'dayName' => $date->format('D'),
                'count' => $daySessions->count(),
                'totalMinutes' => $daySessions->sum('duration'),
                'isToday' => $date->isToday(),
            ];
        }

        // Month data - group by week
        $monthData = [];
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $currentWeekStart = $startOfMonth->copy()->startOfWeek();

        $weekNumber = 1;
        while ($currentWeekStart <= $endOfMonth) {
            $weekEnd = $currentWeekStart->copy()->endOfWeek();

            $weekSessions = $monthSessions->filter(function ($session) use ($currentWeekStart, $weekEnd) {
                return $session->completed_at->between($currentWeekStart, $weekEnd);
            });

            $monthData["week_{$weekNumber}"] = [
                'weekLabel' => "Week {$weekNumber}",
                'dateRange' => $currentWeekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                'count' => $weekSessions->count(),
                'totalMinutes' => $weekSessions->sum('duration'),
            ];

            $currentWeekStart->addWeek();
            $weekNumber++;
        }

        return Inertia::render('Dashboard', [
            'timerDuration' => $settings->timer_duration,
            'todayData' => $todayData,
            'weekData' => $weekData,
            'monthData' => $monthData,
            'recentSessions' => $recentSessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'task_description' => $session->task_description,
                    'duration' => $session->duration,
                    'completed_at' => $session->completed_at->diffForHumans(),
                ];
            }),
        ]);
    }
}
