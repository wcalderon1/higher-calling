<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\PlanEntry;
use App\Models\Devotional;

class ThemedPlansSeeder extends Seeder
{
    public function run(): void
    {
        // Pull all devotionals so we can link them
        $devos = Devotional::where('status', 'published')->get();

        // Helper to safely grab a devotional (recycling if we run out)
        $getDevo = function ($i) use ($devos) {
            if ($devos->isEmpty()) return null;
            return $devos[$i % $devos->count()];
        };

        // 🌿 1. 14-Day Psalms of Comfort
        $psalms = Plan::firstOrCreate(
            ['slug' => '14-day-psalms-of-comfort'],
            [
                'title' => '14-Day Psalms of Comfort',
                'description' => 'Meditate on key Psalms that bring peace and encouragement during hard times.',
                'length_days' => 14,
            ]
        );

        $psalmsVerses = [
            'Psalm 23:1–6' => 'The Lord Is My Shepherd',
            'Psalm 27:1–5' => 'The Lord Is My Light',
            'Psalm 34:17–19' => 'The Lord Delivers',
            'Psalm 37:3–7' => 'Trust in the Lord',
            'Psalm 46:1–11' => 'God Is Our Refuge',
            'Psalm 55:22' => 'Cast Your Burden',
            'Psalm 62:5–8' => 'Find Rest in God',
            'Psalm 63:1–8' => 'My Soul Thirsts for You',
            'Psalm 84:1–12' => 'Blessed Are Those Who Dwell with You',
            'Psalm 91:1–4' => 'Under His Wings',
            'Psalm 103:1–5' => 'Bless the Lord',
            'Psalm 121:1–8' => 'My Help Comes from the Lord',
            'Psalm 139:13–16' => 'Wonderfully Made',
            'Psalm 145:17–21' => 'The Lord Is Near',
        ];
        $this->seedEntries($psalms, $psalmsVerses, $getDevo);

        // 🌿 2. 7-Day Journey Through Proverbs
        $proverbs = Plan::firstOrCreate(
            ['slug' => '7-day-journey-through-proverbs'],
            [
                'title' => '7-Day Journey Through Proverbs',
                'description' => 'A week of practical wisdom and daily reflections from Proverbs.',
                'length_days' => 7,
            ]
        );

        $proverbsVerses = [
            'Proverbs 1:1–7' => 'The Beginning of Knowledge',
            'Proverbs 3:1–6' => 'Trust in the Lord',
            'Proverbs 4:20–27' => 'Guard Your Heart',
            'Proverbs 10:11–14' => 'Words of the Wise',
            'Proverbs 12:15–20' => 'The Way of the Righteous',
            'Proverbs 15:1–4' => 'Gentle Words and Peace',
            'Proverbs 31:10–31' => 'The Virtuous Life',
        ];
        $this->seedEntries($proverbs, $proverbsVerses, $getDevo);

        // 🌿 3. 21-Day Gospel of John
        $john = Plan::firstOrCreate(
            ['slug' => '21-day-gospel-of-john'],
            [
                'title' => '21-Day Gospel of John',
                'description' => 'Explore the life of Jesus through John’s Gospel in 3 short weeks.',
                'length_days' => 21,
            ]
        );

        $johnVerses = [
            'John 1:1–18' => 'The Word Became Flesh',
            'John 2:1–11' => 'Water to Wine',
            'John 3:16–21' => 'God So Loved the World',
            'John 4:1–26' => 'The Woman at the Well',
            'John 5:1–9' => 'Healing at the Pool',
            'John 6:35–40' => 'Bread of Life',
            'John 7:37–39' => 'Rivers of Living Water',
            'John 8:1–12' => 'Go and Sin No More',
            'John 9:1–7' => 'Jesus Heals the Blind Man',
            'John 10:11–18' => 'The Good Shepherd',
            'John 11:25–44' => 'Resurrection and Life',
            'John 12:23–36' => 'The Hour Has Come',
            'John 13:1–17' => 'Jesus Washes Feet',
            'John 14:1–14' => 'I Am the Way',
            'John 15:1–8' => 'The True Vine',
            'John 16:33' => 'Take Heart—I Have Overcome the World',
            'John 17:1–26' => 'Jesus’ Prayer for Us',
            'John 18:28–40' => 'Jesus Before Pilate',
            'John 19:16–30' => 'It Is Finished',
            'John 20:1–18' => 'He Is Risen',
            'John 21:15–25' => 'Feed My Sheep',
        ];
        $this->seedEntries($john, $johnVerses, $getDevo);

        // 🌿 4. 30-Day New Testament Starter (if not already linked)
        $starter = Plan::firstOrCreate(
            ['slug' => '30-day-new-testament-starter'],
            [
                'title' => '30-Day New Testament Starter',
                'description' => 'A gentle 30-day journey pairing your devotionals with key New Testament readings.',
                'length_days' => 30,
            ]
        );

        if ($starter->entries()->count() == 0) {
            foreach (range(1, 30) as $i) {
                $devotional = $getDevo($i);
                PlanEntry::create([
                    'plan_id' => $starter->id,
                    'day_number' => $i,
                    'title' => "Day {$i}",
                    'scripture_ref' => 'Various Readings',
                    'devotional_id' => $devotional?->id,
                ]);
            }
        }
    }

    private function seedEntries(Plan $plan, array $data, $getDevo): void
    {
        $i = 1;
        foreach ($data as $ref => $title) {
            $devotional = $getDevo($i);
            PlanEntry::updateOrCreate(
                ['plan_id' => $plan->id, 'day_number' => $i],
                [
                    'title' => $title,
                    'scripture_ref' => $ref,
                    'devotional_id' => $devotional?->id,
                ]
            );
            $i++;
        }
    }
}
