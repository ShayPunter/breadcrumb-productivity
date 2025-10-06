<?php

namespace App\Http\Controllers;

use App\Models\ProductivitySession;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingController extends Controller
{
    public function index()
    {
        // Get platform-wide statistics
        $totalSessions = ProductivitySession::count();
        $totalMinutes = ProductivitySession::sum('duration');
        $totalHours = round($totalMinutes / 60, 1);
        $totalUsers = User::count();

        // Calculate days since launch (you can adjust this date)
        $activeUsers = User::whereHas('productivitySessions')->count();

        return Inertia::render('Marketing', [
            'stats' => [
                'totalSessions' => number_format($totalSessions),
                'totalMinutes' => number_format($totalMinutes),
                'totalHours' => number_format($totalHours),
                'totalUsers' => number_format($totalUsers),
                'activeUsers' => number_format($activeUsers),
            ],
        ]);
    }
}
