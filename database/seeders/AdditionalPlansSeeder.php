<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class AdditionalPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'title' => '14-Day Psalms of Comfort',
                'slug' => '14-day-psalms-of-comfort',
                'description' => 'Meditate on key Psalms that bring peace and encouragement during hard times.',
                'length_days' => 14,
            ],
            [
                'title' => '7-Day Journey Through Proverbs',
                'slug' => '7-day-journey-through-proverbs',
                'description' => 'A week of practical wisdom and daily reflections from Proverbs.',
                'length_days' => 7,
            ],
            [
                'title' => '21-Day Gospel of John',
                'slug' => '21-day-gospel-of-john',
                'description' => 'Explore the life of Jesus through John’s Gospel in 3 short weeks.',
                'length_days' => 21,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
