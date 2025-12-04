<?php

namespace App\Services;

use App\Models\UserRead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StreakService
{
    protected function key(int $userId): string
    {
        return "streaks:{$userId}";
    }

    /**
     * Main entry: returns an array like:
     * [
     *   'current'          => int,
     *   'longest'          => int,
     *   'today_has_read'   => bool,
     *   'last_read_on'     => Carbon|null,
     *   'next_target_days' => int|null,
     *   'progress_percent' => float,   // 0–100
     * ]
     */
    public function get(int $userId): array
    {
        return Cache::remember($this->key($userId), now()->addMinutes(10), function () use ($userId) {
            // Get all distinct read dates for this user
            $dates = UserRead::query()
                ->where('user_id', $userId)
                ->orderBy('read_on')
                ->pluck('read_on')
                ->map(fn ($d) => Carbon::parse($d)->startOfDay())
                ->unique()
                ->values();

            if ($dates->isEmpty()) {
                return [
                    'current'          => 0,
                    'longest'          => 0,
                    'today_has_read'   => false,
                    'last_read_on'     => null,
                    'next_target_days' => 1,
                    'progress_percent' => 0.0,
                ];
            }

            $today        = Carbon::today();
            $todayHasRead = $dates->contains(fn (Carbon $d) => $d->isSameDay($today));
            $lastRead     = $dates->last();

            //Longest streak over ALL time
            $longest = 1;
            $streak  = 1;

            for ($i = 1; $i < $dates->count(); $i++) {
                $prev = $dates[$i - 1];
                $curr = $dates[$i];

                if ($curr->isSameDay($prev->copy()->addDay())) {
                    $streak++;
                } else {
                    $streak = 1;
                }

                if ($streak > $longest) {
                    $longest = $streak;
                }
            }

            //Current streak
            $current = 0;

            // Current streak only exists if last read was today or yesterday
            if ($lastRead->isSameDay($today) || $lastRead->isSameDay($today->copy()->subDay())) {
                $current      = 1;
                $expectedPrev = $lastRead->copy()->subDay();

                for ($i = $dates->count() - 2; $i >= 0; $i--) {
                    $date = $dates[$i];

                    if ($date->isSameDay($expectedPrev)) {
                        $current++;
                        $expectedPrev->subDay();
                    } elseif ($date->lessThan($expectedPrev)) {
                        // We’ve gone past where the streak could continue
                        break;
                    }
                }
            }

            // Longest should never be less than current
            if ($current > $longest) {
                $longest = $current;
            }

            //Simple next target milestone for the progress bar
            if ($current < 7) {
                $nextTarget = 7;
            } elseif ($current < 30) {
                $nextTarget = 30;
            } else {
                $nextTarget = null; // no further target
            }

            $progress = 0.0;
            if ($nextTarget !== null && $nextTarget > 0) {
                $progress = min(100, round(($current / $nextTarget) * 100, 1));
            } elseif ($current > 0) {
                $progress = 100.0;
            }

            return [
                'current'          => $current,
                'longest'          => $longest,
                'today_has_read'   => $todayHasRead,
                'last_read_on'     => $lastRead,
                'next_target_days' => $nextTarget,
                'progress_percent' => $progress,
            ];
        });
    }

    public function current(int $userId): int
    {
        return $this->get($userId)['current'];
    }

    public function longest(int $userId): int
    {
        return $this->get($userId)['longest'];
    }

    // Clear cache for a user
    public function forget(int $userId): void
    {
        Cache::forget($this->key($userId));
    }

    public function recomputeFor(int $userId): void
    {
        $this->forget($userId);
        $this->get($userId);
    }
}