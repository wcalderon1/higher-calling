<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index() {
        $plans = Plan::withCount('entries')->get();
        $userPlans = auth()->check()
            ? UserPlan::with('plan')->where('user_id', auth()->id())->get()->keyBy('plan_id')
            : collect();
        return view('plans.index', compact('plans','userPlans'));
    }

    public function show(Plan $plan) {
        $plan->load('entries.devotional');
        $userPlan = auth()->check()
            ? UserPlan::firstOrCreate(
                ['user_id' => auth()->id(), 'plan_id' => $plan->id],
                ['start_date' => null, 'status' => 'paused']
              )
            : null;
        $userEntries = $userPlan
            ? $userPlan->entries()->get()->keyBy('plan_entry_id')
            : collect();

        return view('plans.show', compact('plan','userPlan','userEntries'));
    }

    public function start(Plan $plan) {
        $this->middleware('auth');
        $up = UserPlan::firstOrCreate(
            ['user_id' => auth()->id(), 'plan_id' => $plan->id],
            ['status' => 'active']
        );
        if (!$up->start_date) $up->update(['start_date' => now()->toDateString()]);
        $up->update(['status' => 'active']);
        return back()->with('status', 'Plan started!');
    }

    public function pause(Plan $plan) {
        $this->middleware('auth');
        $up = UserPlan::where('user_id', auth()->id())->where('plan_id', $plan->id)->firstOrFail();
        $up->update(['status' => 'paused']);
        return back()->with('status', 'Plan paused.');
    }
}
