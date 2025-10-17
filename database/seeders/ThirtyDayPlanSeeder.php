<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Devotional;
use App\Models\Plan;
use App\Models\PlanEntry;

class ThirtyDayPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Create or reuse the plan
        $plan = Plan::firstOrCreate(
            ['slug' => '30-day-new-testament-starter'],
            [
                'title' => '30-Day New Testament Starter',
                'description' => 'A gentle 30-day journey pairing your devotionals with key New Testament readings.',
                'length_days' => 30,
            ]
        );

        // Get up to 30 most recent devotionals
        $devos = Devotional::orderByDesc('published_at')
            ->take(30)
            ->get()
            ->values();

        // Fill each day
        foreach (range(1, $plan->length_days) as $day) {
            PlanEntry::updateOrCreate(
                ['plan_id' => $plan->id, 'day_number' => $day],
                [
                    'devotional_id' => $devos[$day - 1]->id ?? null,
                    'title' => $devos[$day - 1]->title ?? "Reading Day {$day}",
                    'scripture_ref' => null,
                ]
            );
        }
    }
}
