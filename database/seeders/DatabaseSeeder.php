<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tag;
use App\Models\Devotional;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a couple of known users
        $wendy = User::firstOrCreate(
            ['email' => 'wendycalderon3443@gmail.com'],
            ['name' => 'Wendy Calderon', 'password' => bcrypt('password')]
        );

        $john = User::firstOrCreate(
            ['email' => 'john@example.com'],
            ['name' => 'John Doe', 'password' => bcrypt('password')]
        );

        $users = collect([$wendy, $john])->merge(User::factory()->count(2)->create());

        // Tag set
        $names = ['faith','prayer','gratitude','hope','love','patience','joy','kindness','wisdom','peace'];
        $tags  = collect($names)->map(function ($n) {
            return Tag::firstOrCreate(['slug' => Str::slug($n)], ['name' => $n]);
        });

        // 20 devotionals with mixed statuses, random authors, and 1–3 tags each
        Devotional::factory()
            ->count(20)
            ->make()
            ->each(function (Devotional $d) use ($users, $tags) {
                $d->user_id = $users->random()->id;
                $d->save();

                $attach = $tags->random(rand(1, 3))->pluck('id')->all();
                $d->tags()->sync($attach);
            });

        // A couple of clearly published pieces for the homepage/demo feel
        Devotional::factory()->state([
            'user_id'      => $wendy->id,
            'title'        => 'Walking in Faith Each Day',
            'slug'         => Str::slug('Walking in Faith Each Day') . '-' . Str::random(6),
            'status'       => 'published',
            'published_at' => now()->subDays(3)->setTime(9, 0),
            'excerpt'      => 'Trusting God in small daily choices strengthens us for bigger challenges.',
        ])->create()->tags()->sync(
            $tags->whereIn('name', ['faith','wisdom'])->pluck('id')->all()
        );
    }
}
