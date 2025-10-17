@extends('layouts.app')

@section('content-left')

    {{-- 🌿 Verse of the Day --}}
    <section class="rounded-3xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white p-6 shadow-sm">
        <p class="uppercase text-xs tracking-wide opacity-80 mb-1">Verse of the Day</p>
        <h3 class="text-2xl font-semibold leading-snug">{{ $votd['text'] }}</h3>
        <p class="mt-2 text-sm opacity-90">{{ $votd['ref'] }}</p>
    </section>

    {{-- ✨ Featured & Quick Actions --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Featured --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Featured</h2>
                <a href="{{ route('devotionals.index') }}" class="text-sm text-amber-700 hover:underline">Browse all →</a>
            </div>

            @if($featured)
                <a href="{{ route('devotionals.show', $featured) }}" class="block group">
                    @if($featured->cover_path)
                        <img src="{{ asset('storage/'.$featured->cover_path) }}" alt=""
                             class="w-full h-52 object-cover rounded-xl mb-4">
                    @endif
                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-700 transition">
                        {{ $featured->title }}
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">
                        @if($featured->author) By {{ $featured->author->name }} • @endif
                        {{ optional($featured->published_at)->format('M j, Y') }}
                    </p>
                    @if($featured->excerpt)
                        <p class="mt-3 text-gray-700 leading-relaxed">{{ $featured->excerpt }}</p>
                    @endif

                    @if($featured->tags->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($featured->tags as $t)
                                <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 font-medium">
                                    #{{ $t->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </a>
            @else
                <p class="text-sm text-gray-600">No published devotionals yet.</p>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('devotionals.create') }}" class="block rounded-xl border px-4 py-3 text-center hover:bg-gray-50">+ New Devotional</a>
                <a href="{{ route('devotionals.index', ['mine' => 1]) }}" class="block rounded-xl border px-4 py-3 text-center hover:bg-gray-50">My Posts</a>
                <a href="{{ route('dashboard') }}" class="block rounded-xl border px-4 py-3 text-center hover:bg-gray-50">Go to Dashboard</a>
            </div>
        </div>
    </section>

    {{-- 📰 Latest Devotionals --}}
    <section class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Latest Devotionals</h2>
            <a href="{{ route('devotionals.index') }}" class="text-sm text-amber-700 hover:underline">See more</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($latest as $d)
                <article class="rounded-xl border hover:border-amber-300 transition p-4 hover:bg-amber-50/30">
                    <a href="{{ route('devotionals.show', $d) }}" class="block">
                        <div class="flex gap-4">
                            @if($d->cover_path)
                                <img src="{{ asset('storage/'.$d->cover_path) }}" alt=""
                                     class="w-24 h-16 object-cover rounded-lg border">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $d->title }}</h3>
                                <div class="mt-1 text-xs text-gray-500">
                                    @if($d->author) By {{ $d->author->name }} • @endif
                                    {{ optional($d->published_at)->format('M j, Y') }}
                                </div>
                                @if($d->excerpt)
                                    <p class="mt-2 text-sm text-gray-700 leading-snug">{{ $d->excerpt }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <p class="text-sm text-gray-600">Nothing published yet.</p>
            @endforelse
        </div>
    </section>

    {{-- ✍️ Continue Writing --}}
    @auth
        <section class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Continue Writing</h2>
                <a href="{{ route('devotionals.index', ['mine' => 1]) }}" class="text-sm text-amber-700 hover:underline">All my posts</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($myDrafts as $d)
                    <a href="{{ route('devotionals.edit', $d) }}"
                       class="rounded-xl border hover:border-indigo-300 transition p-4 hover:bg-indigo-50/40">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-800">{{ $d->title }}</div>
                            <div class="text-xs uppercase tracking-wide {{ $d->status === 'draft' ? 'text-amber-600' : 'text-indigo-600' }}">
                                {{ $d->status }}
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            @if($d->status === 'scheduled' && $d->published_at)
                                Scheduled {{ $d->published_at->format('M j, Y g:ia') }}
                            @else
                                Updated {{ $d->updated_at->diffForHumans() }}
                            @endif
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-600">No drafts—great job! 🎉</p>
                @endforelse
            </div>
        </section>
    @endauth

    {{-- 💬 Community Comments --}}
    <section class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">From the Community</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($recentComments as $c)
                <div class="rounded-xl border hover:border-indigo-200 transition p-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $c->author->name ?? 'User' }}</span>
                        on
                        <a href="{{ route('devotionals.show', $c->devotional) }}" class="underline hover:text-indigo-600">
                            {{ $c->devotional->title }}
                        </a>
                        <span class="text-xs">• {{ $c->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-2 text-gray-800 leading-snug">{{ \Illuminate\Support\Str::limit($c->body, 160) }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-600">No comments yet.</p>
            @endforelse
        </div>
    </section>

    {{-- 🏷️ Tag Cloud --}}
    @if($topTags->isNotEmpty())
        <section class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Popular Tags</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($topTags as $t)
                    <a href="{{ route('devotionals.index', ['tags' => [$t->slug]]) }}"
                       class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-medium hover:bg-amber-200 transition">
                        #{{ $t->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

@endsection


@section('content-right')
    {{-- Sidebar: authors & tags --}}
    @include('components.sidebar.recent-authors', ['recentAuthors' => $recentAuthors ?? collect()])
    @include('components.sidebar.popular-tags', ['popularTags' => $popularTags ?? collect()])
@endsection
