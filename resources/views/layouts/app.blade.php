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
<body class="font-sans antialiased text-gray-800 bg-gray-50">

@if(auth()->check() && auth()->user()->is_admin)
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm">
        <div class="max-w-7xl mx-auto px-4 py-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10 border border-white/20 text-[10px] font-semibold uppercase tracking-wide">
                    Admin
                </span>
                <span>
                    You are signed in as
                    <span class="font-semibold">
                        {{ auth()->user()->display_name ?? auth()->user()->name }}
                    </span>.
                    You can manage and delete any content.
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-xs">
                <a href="{{ route('devotionals.index') }}" class="underline hover:no-underline">
                    Devotionals
                </a>

                @if(Route::has('plans.index'))
                    <a href="{{ route('plans.index') }}" class="underline hover:no-underline">
                        Reading Plans
                    </a>
                @endif

                @if(Route::has('profile.me'))
                    <a href="{{ route('profile.me') }}" class="underline hover:no-underline">
                        My Profile
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif

    {{-- Top Navigation --}}
    @include('layouts.navigation')

    {{-- Flash messages --}}
    <x-flash />

    {{-- Optional page header --}}
    @isset($header)
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto py-6 px-6">
                {{ $header }}
            </div>
        </header>
    @endisset

    @php
        use Illuminate\Support\Facades\Route;

        // Only show streak on the home page
        $showStreak = Route::currentRouteNamed('home');

        $streaks = null;
        if ($showStreak && auth()->check() && class_exists(\App\Services\StreakService::class)) {
            try {
                $streaks = app(\App\Services\StreakService::class)->get(auth()->id());
            } catch (\Throwable $e) {
                $streaks = null;
            }
        }

        $current = $streaks['current'] ?? 0;
        $milestones = [1,3,7,14,30,60,100,200,365];
        $next = collect($milestones)->first(fn($m) => $m > $current);
        $prev = collect($milestones)->filter(fn($m) => $m <= $current)->last() ?? 0;
        $den = max(1, ($next ?? $current) - $prev);
        $progress = $next ? max(0, min(100, (int) round((($current - $prev) / $den) * 100))) : 100;
        $hitMilestone = $next === null || $current === $next;
        $ringClass = $hitMilestone ? 'ring-2 ring-amber-300/50' : '';
    @endphp

    <main class="min-h-screen">
        @if (View::hasSection('content-right'))
            {{-- TWO-COLUMN LAYOUT (8 / 4) --}}
            <div class="max-w-7xl mx-auto px-6 py-8 lg:py-10 grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- Left / Main --}}
                <div class="lg:col-span-8 space-y-8">
                    @yield('content-left')
                    @yield('content') {{-- fallback --}}
                </div>

                {{-- Right / Sidebar --}}
                <aside class="lg:col-span-4 space-y-6">
                    {{-- Streak card only on home page, full-width in sidebar --}}
                    @if($showStreak && $streaks)
                        <div>
                            <div class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-gray-700 shadow-sm {{ $ringClass }}"
                                 title="{{ $hitMilestone ? 'Milestone reached!' : 'Keep going to your next milestone' }}">
                                <div class="flex items-center gap-3">
                                    <div class="inline-flex items-center gap-1.5">
                                        <span class="text-base">🔥</span>
                                        <span class="text-xs uppercase tracking-wide text-gray-500">Streak</span>
                                        <span class="text-sm font-semibold tabular-nums">{{ $current }}d</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between text-[10px] text-gray-400">
                                            <span>{{ $prev }}d</span>
                                            <span>{{ $next ? $next.'d' : 'Max' }}</span>
                                        </div>
                                        <div class="mt-0.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full bg-gradient-to-r from-amber-300 via-orange-400 to-rose-400"
                                                 style="width: {{ $progress }}%"></div>
                                        </div>
                                        <div class="mt-1 text-[10px] text-gray-600">
                                            {{ $hitMilestone ? '🎉 Milestone reached!' : 'Next: '.($next ?? $current).' days' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content-right')
                </aside>

            </div>
        @else
            {{-- SINGLE-COLUMN LAYOUT (centered) --}}
            <div class="max-w-5xl mx-auto px-6 py-8 lg:py-10 space-y-6">

                {{-- Compact streak card inline, aligned right (home only) --}}
                @if($showStreak && $streaks)
                    <div class="flex justify-end">
                        <div class="w-full max-w-xs rounded-2xl border border-gray-200 bg-white px-4 py-3 text-gray-700 shadow-sm {{ $ringClass }}"
                             title="{{ $hitMilestone ? 'Milestone reached!' : 'Keep going to your next milestone' }}">
                            <div class="flex items-center gap-3">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="text-base">🔥</span>
                                    <span class="text-xs uppercase tracking-wide text-gray-500">Streak</span>
                                    <span class="text-sm font-semibold tabular-nums">{{ $current }}d</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between text-[10px] text-gray-400">
                                        <span>{{ $prev }}d</span>
                                        <span>{{ $next ? $next.'d' : 'Max' }}</span>
                                    </div>
                                    <div class="mt-0.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-amber-300 via-orange-400 to-rose-400"
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="mt-1 text-[10px] text-gray-600">
                                        {{ $hitMilestone ? '🎉 Milestone reached!' : 'Next: '.($next ?? $current).' days' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Page content --}}
                @yield('content-left')
                @yield('content') {{-- fallback --}}
            </div>
        @endif
    </main>

</body>
</html>