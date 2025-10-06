<?php

namespace App\Http\Controllers;

use App\Models\ProductivitySession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductivitySessionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_description' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $session = $request->user()->productivitySessions()->create([
            'task_description' => $validated['task_description'],
            'duration' => $validated['duration'],
            'completed_at' => Carbon::now(),
        ]);

        return back()->with([
            'message' => 'Session completed successfully!',
            'session' => $session,
        ]);
    }

    public function index(Request $request)
    {
        $sessions = $request->user()->productivitySessions()
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        return response()->json($sessions);
    }
}
