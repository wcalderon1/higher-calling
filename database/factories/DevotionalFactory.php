<?php

namespace Database\Factories;

use App\Models\Devotional;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Devotional> */
class DevotionalFactory extends Factory
{
    protected $model = Devotional::class;

    public function definition(): array
    {
        $title  = $this->faker->sentence(5, true);
        $status = $this->faker->randomElement(['published', 'draft', 'scheduled']);

        if ($status === 'published') {
            $publishedAt = now()->subDays($this->faker->numberBetween(0, 45))
                                ->setTime($this->faker->numberBetween(6,20), [0,15,30,45][array_rand([0,1,2,3])]);
        } elseif ($status === 'scheduled') {
            $publishedAt = now()->addDays($this->faker->numberBetween(1, 21))
                                ->setTime($this->faker->numberBetween(6,20), [0,15,30,45][array_rand([0,1,2,3])]);
        } else {
            $publishedAt = null;
        }

        return [
            'user_id'      => User::factory(),
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . Str::random(6),
            'excerpt'      => $this->faker->sentence(18, true),
            'body'         => $this->faker->paragraphs(6, true),
            'status'       => $status,
            'published_at' => $publishedAt,
        ];
    }
}
