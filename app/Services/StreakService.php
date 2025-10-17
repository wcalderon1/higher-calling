<?php

namespace App\Services;

use App\Models\UserRead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class StreakService
{
    protected function key(int $userId): string
    {
        return "streaks:{$userId}";
    }

    public function forget(int $userId): void
    {
        Cache::forget($this->key($userId));
    }

    protected function build(int $userId): array
    {
        // Pull last 365 days for safety (adjust as you like)
        $reads = UserRead::where('user_id', $userId)
            ->orderByDesc('read_on')
            ->limit(400)
            ->pluck('read_on')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $today = Carbon::today();
        $set = collect($reads)->flip(); // O(1) membership check

        // CURRENT: count back from today while consecutive dates exist
        $current = 0;
        $cursor = $today->copy();
        while ($set->has($cursor->toDateString())) {
            $current++;
            $cursor->subDay();
        }

        // LONGEST: scan all sequences
        $longest = 0;
        $readsDesc = $reads; // already desc
        $i = 0;
        while ($i < $readsDesc->count()) {
            $len = 1;
            $cur = Carbon::parse($readsDesc[$i]);
            $j = $i + 1;
            while ($j < $readsDesc->count()) {
                $next = Carbon::parse($readsDesc[$j]);
                if ($cur->diffInDays($next) === 1) {
                    $len++;
                    $cur = $next;
                    $j++;
                } else {
                    break;
                }
            }
            $longest = max($longest, $len);
            $i = $j;
        }

        return ['current' => $current, 'longest' => $longest];
    }

    public function get(int $userId): array
    {
        return Cache::remember($this->key($userId), now()->addMinutes(30), fn() => $this->build($userId));
    }

    public function current(int $userId): int
    {
        return $this->get($userId)['current'];
    }

    public function longest(int $userId): int
    {
        return $this->get($userId)['longest'];
    }

    // ✅ Added helper so controller call works
    public function recomputeFor(int $userId): void
    {
        $this->forget($userId);       // clear cached streak data
        $this->get($userId);          // rebuild and cache again
    }
}
