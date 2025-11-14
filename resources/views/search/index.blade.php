@extends('layouts.app')

@section('title', 'Search')

@section('content-left')
    <div class="space-y-8 p-6">
        {{-- Heading --}}
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Search</h1>
            <p class="mt-2 text-sm text-slate-500">
                Look up devotionals, people, tags, and plans in one place.
            </p>
        </div>

        {{-- Search form --}}
        <form method="GET" action="{{ route('search.index') }}" class="flex flex-col sm:flex-row gap-3 max-w-2xl">
            <input
                type="text"
                name="q"
                value="{{ old('q', $q) }}"
                placeholder="Search devotionals, users, tags..."
                class="flex-1 rounded-full border border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-200 outline-none bg-white"
                autofocus
            >
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-full px-5 py-2.5 text-sm font-medium bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition"
            >
                Search
            </button>
        </form>

        @php
            $devCount  = $devotionals->count();
            $userCount = $users->count();
            $tagCount  = $tags->count();
            $planCount = $plans->count();
            $totalCount = $devCount + $userCount + $tagCount + $planCount;
        @endphp

        @if ($q === '')
            <p class="text-sm text-slate-500">
                Try searching for “faith”, “gratitude”, a devotional title, a tag, or someone’s display name.
            </p>
        @else
            {{-- Summary + section "tabs" --}}
            <div class="space-y-3">
                <p class="text-sm text-slate-500">
                    Showing
                    <span class="font-semibold text-slate-800">{{ $totalCount }}</span>
                    result{{ $totalCount === 1 ? '' : 's' }}
                    for <span class="font-medium text-slate-800">"{{ $q }}"</span>.
                </p>

                <div class="flex flex-wrap gap-2 text-xs">
                    <a href="#devotionals"
                       class="inline-flex items-center gap-1 rounded-full border px-3 py-1
                              {{ $devCount ? 'border-amber-400 bg-amber-50 text-amber-800' : 'border-slate-200 text-slate-400' }}">
                        <span>Devotionals</span>
                        <span class="text-[10px]">{{ $devCount }}</span>
                    </a>

                    <a href="#people"
                       class="inline-flex items-center gap-1 rounded-full border px-3 py-1
                              {{ $userCount ? 'border-amber-400 bg-amber-50 text-amber-800' : 'border-slate-200 text-slate-400' }}">
                        <span>People</span>
                        <span class="text-[10px]">{{ $userCount }}</span>
                    </a>

                    <a href="#tags"
                       class="inline-flex items-center gap-1 rounded-full border px-3 py-1
                              {{ $tagCount ? 'border-amber-400 bg-amber-50 text-amber-800' : 'border-slate-200 text-slate-400' }}">
                        <span>Tags</span>
                        <span class="text-[10px]">{{ $tagCount }}</span>
                    </a>

                    <a href="#plans"
                       class="inline-flex items-center gap-1 rounded-full border px-3 py-1
                              {{ $planCount ? 'border-amber-400 bg-amber-50 text-amber-800' : 'border-slate-200 text-slate-400' }}">
                        <span>Plans</span>
                        <span class="text-[10px]">{{ $planCount }}</span>
                    </a>
                </div>
            </div>

            <div class="mt-6 space-y-10">
                {{-- Devotionals --}}
                <section id="devotionals">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
                            Devotionals
                        </h2>
                        @if ($devCount)
                            <span class="text-xs text-slate-400">{{ $devCount }} result{{ $devCount === 1 ? '' : 's' }}</span>
                        @endif
                    </div>

                    @if ($devCount === 0)
                        <p class="text-sm text-slate-400">No devotionals matched this search.</p>
                    @else
                        <ul class="space-y-3">
                            @foreach ($devotionals as $devotional)
                                <li>
                                    <a href="{{ route('devotionals.show', $devotional->slug) }}"
                                       class="block rounded-2xl border border-slate-100 bg-white px-4 py-3 hover:border-amber-300 hover:shadow-sm transition">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <h3 class="text-sm font-semibold text-slate-900 underline">
                                                    {{ $devotional->title }}
                                                </h3>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    @if ($devotional->user)
                                                        By {{ $devotional->user->display_name ?? $devotional->user->name }}
                                                        <span aria-hidden="true">•</span>
                                                    @endif
                                                    @if ($devotional->published_at)
                                                        {{ $devotional->published_at->format('M j, Y') }}
                                                    @endif
                                                </p>
                                                <p class="mt-2 text-xs text-slate-500 line-clamp-2">
                                                    {{ $devotional->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($devotional->body), 160) }}
                                                </p>

                                                @if ($devotional->tags->isNotEmpty())
                                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                                        @foreach ($devotional->tags as $tag)
                                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700">
                                                                #{{ $tag->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- People --}}
                <section id="people">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
                            People
                        </h2>
                        @if ($userCount)
                            <span class="text-xs text-slate-400">{{ $userCount }} result{{ $userCount === 1 ? '' : 's' }}</span>
                        @endif
                    </div>

                    @if ($userCount === 0)
                        <p class="text-sm text-slate-400">No people matched this search.</p>
                    @else
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($users as $user)
                                <a href="{{ route('profile.show', $user->id) }}"
                                   class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white px-3 py-2.5 hover:border-amber-300 hover:shadow-sm transition">
                                    <img
                                        src="{{ $user->avatar_path ? asset('storage/'.$user->avatar_path) : asset('images/avatar-default.png') }}"
                                        class="h-9 w-9 rounded-full object-cover"
                                        alt="{{ $user->display_name ?? $user->name }}"
                                    >
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate">
                                            {{ $user->display_name ?? $user->name }}
                                        </p>
                                        @if ($user->bio)
                                            <p class="text-xs text-slate-500 line-clamp-2">
                                                {{ $user->bio }}
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Tags --}}
                <section id="tags">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
                            Tags
                        </h2>
                        @if ($tagCount)
                            <span class="text-xs text-slate-400">{{ $tagCount }} result{{ $tagCount === 1 ? '' : 's' }}</span>
                        @endif
                    </div>

                    @if ($tagCount === 0)
                        <p class="text-sm text-slate-400">No tags matched this search.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <a href="{{ route('tags.show', $tag->slug) }}"
                                   class="inline-flex flex-col rounded-2xl border border-slate-100 bg-white px-3 py-2 hover:border-amber-300 hover:shadow-sm transition">
                                    <span class="text-xs font-semibold text-slate-900">
                                        #{{ $tag->name }}
                                    </span>
                                    @if ($tag->description)
                                        <span class="mt-0.5 text-[11px] text-slate-500 line-clamp-2">
                                            {{ $tag->description }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Plans --}}
                <section id="plans">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
                            Plans
                        </h2>
                        @if ($planCount)
                            <span class="text-xs text-slate-400">{{ $planCount }} result{{ $planCount === 1 ? '' : 's' }}</span>
                        @endif
                    </div>

                    @if ($planCount === 0)
                        <p class="text-sm text-slate-400">No plans matched this search.</p>
                    @else
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($plans as $plan)
                                <a href="{{ route('plans.show', $plan->slug) }}"
                                   class="block rounded-2xl border border-slate-100 bg-white px-4 py-3 hover:border-amber-300 hover:shadow-sm transition">
                                    <h3 class="text-sm font-semibold text-slate-900">
                                        {{ $plan->name ?? $plan->title ?? ucfirst(str_replace('-', ' ', $plan->slug)) }}
                                    </h3>
                                    @if ($plan->description)
                                        <p class="mt-2 text-xs text-slate-500 line-clamp-2">
                                            {{ $plan->description }}
                                        </p>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Overall empty state --}}
                @if ($totalCount === 0)
                    <p class="text-sm text-slate-400">
                        No results found. Try a different keyword or a simpler phrase.
                    </p>
                @endif
            </div>
        @endif
    </div>
@endsection
