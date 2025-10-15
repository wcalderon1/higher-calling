<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

{{-- 🔥 Streak (compact card with milestone progress) --}}
@auth
    @php
        $streaks = null;
        if (class_exists(\App\Services\StreakService::class)) {
            try { $streaks = app(\App\Services\StreakService::class)->get(auth()->id()); } catch (\Throwable $e) { $streaks = null; }
        }

        $current = $streaks['current'] ?? 0;

        // Milestones: feel free to tweak
        $milestones = [1,3,7,14,30,60,100,200,365];

        // Find next/prev milestones
        $next = null;
        foreach ($milestones as $m) { if ($m > $current) { $next = $m; break; } }
        $prev = 0;
        foreach ($milestones as $m) { if ($m <= $current) { $prev = $m; } }

        // Progress from prev -> next milestone
        $den = max(1, ($next ?? $current) - $prev);
        $progress = $next ? max(0, min(100, (int) round((($current - $prev) / $den) * 100))) : 100;

        $hitMilestone = $next === null || $current === $next; // glow when on a milestone
    @endphp

    @if ($streaks)
        <div class="bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex justify-end">
                <div
                    class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-700 shadow-sm
                           dark:bg-slate-900 dark:border-slate-700 dark:text-slate-200
                           {{ $hitMilestone ? 'ring-2 ring-amber-300/60 dark:ring-amber-400/40' : '' }}"
                    title="{{ $hitMilestone ? 'Milestone reached!' : 'Keep going to your next milestone' }}"
                >
                    <div class="inline-flex items-center gap-1.5">
                        <span class="text-base" aria-hidden>🔥</span>
                        <span class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Streak</span>
                        <span class="text-sm font-semibold tabular-nums">{{ $current }}d</span>
                    </div>

                    {{-- Progress to next milestone --}}
                    <div class="w-28">
                        <div class="flex justify-between text-[10px] text-slate-400 dark:text-slate-500">
                            <span>{{ $prev }}d</span>
                            <span>{{ $next ? $next.'d' : 'Max' }}</span>
                        </div>
                        <div class="mt-0.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-300 via-orange-400 to-rose-400"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="mt-1 text-[10px] text-slate-600 dark:text-slate-300">
                            {{ $hitMilestone ? '🎉 Milestone reached!' : 'Next: '.$next.' days' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endauth


    {{-- FLASH BANNER --}}
    <x-flash />

    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        @yield('content')
    </main>
</div>
</body>
</html>
