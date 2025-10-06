<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UserSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $settings = $user->settings()->firstOrCreate(
            ['user_id' => $user->id],
            ['timer_duration' => 10]
        );

        return Inertia::render('settings/Preferences', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'timer_duration' => 'required|integer|min:1|max:120',
        ]);

        $user = $request->user();
        $settings = $user->settings()->firstOrCreate(['user_id' => $user->id]);
        $settings->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
