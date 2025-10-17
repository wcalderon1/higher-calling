<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tag;
use App\Models\Devotional;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------------
        // 1. USERS (with avatars + bios)
        // --------------------------------------------------
        $wendy = User::firstOrCreate(
            ['email' => 'wendycalderon3443@gmail.com'],
            [
                'name' => 'Wendy Calderon',
                'password' => bcrypt('password'),
                'display_name' => 'Wendy C.',
                'avatar_path' => 'images/avatar-default.png',
                'bio' => 'Founder of Higher Calling — passionate about helping others grow in faith through daily reflection and community.'
            ]
        );

        $john = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'display_name' => 'John D.',
                'avatar_path' => 'images/avatar-default.png',
                'bio' => 'Youth pastor and writer who enjoys sharing uplifting messages about perseverance and prayer.'
            ]
        );

        // Create two extra realistic users
        $extraUsers = [
            [
                'name' => 'Grace Lee',
                'email' => 'grace@example.com',
                'display_name' => 'Grace L.',
                'password' => bcrypt('password'),
                'avatar_path' => 'images/avatar-default.png',
                'bio' => 'Devotional writer exploring God’s grace in daily life.'
            ],
            [
                'name' => 'Michael Rivera',
                'email' => 'michael@example.com',
                'display_name' => 'Mike R.',
                'password' => bcrypt('password'),
                'avatar_path' => 'images/avatar-default.png',
                'bio' => 'Musician and believer — passionate about blending worship and storytelling.'
            ],
        ];

        foreach ($extraUsers as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        $users = User::all();

        // --------------------------------------------------
        // 2. TAGS (with friendly descriptions)
        // --------------------------------------------------
        $tagData = [
            'faith' => 'Encouragement to trust God’s plan even when it’s unclear.',
            'prayer' => 'Moments of conversation with God and deep reflection.',
            'gratitude' => 'Reminders to stay thankful through all seasons.',
            'hope' => 'Inspiration to keep believing during trials.',
            'love' => 'Lessons about compassion and selfless living.',
            'patience' => 'Learning to wait gracefully and faithfully.',
            'joy' => 'Finding happiness in small everyday blessings.',
            'kindness' => 'Living out the love of Christ in practical ways.',
            'wisdom' => 'Biblical insight for making sound life decisions.',
            'peace' => 'Finding rest and calm through spiritual grounding.'
        ];

        $tags = collect($tagData)->map(function ($desc, $name) {
            return Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => ucfirst($name), 'description' => $desc]
            );
        });

        // --------------------------------------------------
        // 3. DEVOTIONALS (spread across 30 days)
        // --------------------------------------------------
        $titles = [
            'Finding Strength in Surrender',
            'Patience in the Waiting',
            'Joy Through Trials',
            'The Power of Forgiveness',
            'Walking in His Timing',
            'Peace Beyond Understanding',
            'Learning to Listen',
            'Faith That Moves Mountains',
            'Hope Renewed Each Morning',
            'Love in Action'
        ];

        foreach (range(1, 30) as $i) {
            $title = $titles[array_rand($titles)] . ' #' . $i;
            $devotional = Devotional::factory()->make([
                'user_id' => $users->random()->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'status' => $i <= 25 ? 'published' : 'draft',
                'published_at' => Carbon::now()->subDays(31 - $i)->setTime(rand(6, 10), 0),
                'excerpt' => fake()->sentence(14),
            ]);
            $devotional->save();
            $devotional->tags()->sync($tags->random(rand(2, 4))->pluck('id')->all());
        }

        // Featured devotional for homepage
        Devotional::factory()->state([
            'user_id' => $wendy->id,
            'title' => 'Walking in Faith Each Day',
            'slug' => Str::slug('Walking in Faith Each Day') . '-' . Str::random(6),
            'status' => 'published',
            'published_at' => now()->subDays(2)->setTime(9, 0),
            'excerpt' => 'Trusting God in daily moments strengthens our hearts for greater challenges ahead.',
        ])->create()->tags()->sync(
            $tags->whereIn('name', ['Faith','Wisdom'])->pluck('id')->all()
        );

        // --------------------------------------------------
        // 4. 30-DAY PLAN
        // --------------------------------------------------
        $this->call(\Database\Seeders\ThirtyDayPlanSeeder::class);
        $this->call(\Database\Seeders\AdditionalPlansSeeder::class);
        $this->call(\Database\Seeders\ThemedPlansSeeder::class);


    }
}
