<?php

namespace App\Http\Controllers;

use App\Models\Devotional;
use App\Models\UserRead;
use App\Services\StreakService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class UserReadController extends Controller
{
    public function store(Request $request, Devotional $devotional, StreakService $streaks)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $today = Carbon::today()->toDateString();

        // Optional: only allow marking the devotional if it's today's published devotional.
        // If your Devotional has a 'publish_date' column, enforce it:
        if (method_exists($devotional, 'publish_date') || isset($devotional->publish_date)) {
            if (optional($devotional->publish_date)->toDateString() !== $today) {
                return back()->with('error', 'You can only mark today’s devotional as read.');
            }
        }

        try {
            UserRead::create([
                'user_id'       => $user->id,
                'devotional_id' => $devotional->id,
                'read_on'       => $today,
            ]);
        } catch (QueryException $e) {
            // Catches unique constraint violation if already marked today
            return back()->with('info', 'Already counted for today. 🔥 Keep it up!');
        }

        // Invalidate cached streak and recompute to show fresh numbers
        $streaks->forget($user->id);
        $current = $streaks->current($user->id);
        $longest = $streaks->longest($user->id);

        return back()->with('success', "Marked as read. Current streak: {$current} day(s). Longest: {$longest}.");
    }
}
