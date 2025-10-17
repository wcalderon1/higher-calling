<?php
namespace App\Http\Controllers;

use App\Models\PlanEntry;
use App\Models\UserPlan;
use App\Models\UserPlanEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserRead;               // uses your existing table
use App\Services\StreakService;        // recompute after marking read

class PlanProgressController extends Controller
{
    public function toggle(Request $request, PlanEntry $entry, StreakService $streaks)
    {
        $this->middleware('auth');

        $user = $request->user();

        // Ensure the user has this plan
        $userPlan = UserPlan::firstOrCreate(
            ['user_id' => $user->id, 'plan_id' => $entry->plan_id],
            ['status' => 'active', 'start_date' => now()->toDateString()]
        );

        $upe = UserPlanEntry::firstOrCreate(
            ['user_plan_id' => $userPlan->id, 'plan_entry_id' => $entry->id]
        );

        $completed = ! $upe->completed_at;

        DB::transaction(function () use ($upe, $completed, $entry, $user, $streaks) {
            $upe->update(['completed_at' => $completed ? now() : null]);

            // If tied to a Devotional and we're completing, also mark the devotional as "read today"
            if ($completed && $entry->devotional_id) {
                UserRead::firstOrCreate(
                    ['user_id' => $user->id, 'read_on' => now()->toDateString()],
                    ['devotional_id' => $entry->devotional_id]
                );
                // warm streaks cache
                $streaks->recomputeFor($user->id);
            }
        });

        return back()->with('status', $completed ? 'Day completed!' : 'Marked as not done.');
    }
}
