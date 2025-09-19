@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10 space-y-8">

    {{-- Verse of the Day --}}
    <section class="rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white p-6 shadow-sm">
        <div class="text-xs uppercase opacity-90 tracking-wider">Verse of the Day</div>
        <div class="mt-2 text-lg leading-relaxed">{{ $votd['text'] }}</div>
        <div class="mt-2 text-sm opacity-90">{{ $votd['ref'] }}</div>
    </section>

    {{-- Featured + Quick actions --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-2xl border bg-white p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold">Featured</h2>
                <a href="{{ route('devotionals.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Browse all →</a>
            </div>

            @if($featured)
                <a href="{{ route('devotionals.show', $featured) }}" class="block">
                    @if($featured->cover_path)
                        <img src="{{ asset('storage/'.$featured->cover_path) }}" alt="" class="w-full h-48 object-cover rounded-xl border mb-4">
                    @endif
                    <h3 class="text-xl font-semibold">{{ $featured->title }}</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        @if($featured->author) By {{ $featured->author->name }} • @endif
                        @if($featured->published_at) {{ $featured->published_at->format('M j, Y') }} @endif
                    </p>
                    @if($featured->excerpt)
                        <p class="mt-3 text-gray-700">{{ $featured->excerpt }}</p>
                    @endif
                    @if($featured->tags->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($featured->tags as $t)
                                <a href="{{ route('devotionals.index', ['tags' => [$t->slug]]) }}"
                                   class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs text-gray-700 hover:bg-gray-50">#{{ $t->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </a>
            @else
                <p class="text-sm text-gray-600">No published devotionals yet.</p>
            @endif
        </div>

        <div class="rounded-2xl border bg-white p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold">Quick actions</h2>
            </div>
            <div class="space-y-3">
                <a href="{{ route('devotionals.create') }}" class="block rounded-xl border px-4 py-3 hover:bg-gray-50">+ New Devotional</a>
                <a href="{{ route('devotionals.index', ['mine' => 1]) }}" class="block rounded-xl border px-4 py-3 hover:bg-gray-50">My Posts</a>
                <a href="{{ route('dashboard') }}" class="block rounded-xl border px-4 py-3 hover:bg-gray-50">Go to Dashboard</a>
            </div>
        </div>
    </section>

    {{-- Latest Devotionals --}}
    <section class="rounded-2xl border bg-white p-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold">Latest devotionals</h2>
            <a href="{{ route('devotionals.index') }}" class="text-sm text-gray-600 hover:text-gray-800">See more</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($latest as $d)
                <article class="rounded-xl border p-4 hover:bg-gray-50">
                    <a href="{{ route('devotionals.show', $d) }}" class="block">
                        <div class="flex gap-4">
                            @if($d->cover_path)
                                <img src="{{ asset('storage/'.$d->cover_path) }}" alt="" class="w-24 h-16 object-cover rounded-lg border">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-medium">{{ $d->title }}</h3>
                                <div class="mt-1 text-xs text-gray-500">
                                    @if($d->author) By {{ $d->author->name }} • @endif
                                    @if($d->published_at) {{ $d->published_at->format('M j, Y') }} @endif
                                </div>
                                @if($d->excerpt)
                                    <p class="mt-2 text-sm text-gray-700">{{ $d->excerpt }}</p>
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

    {{-- Your drafts / scheduled --}}
    @auth
    <section class="rounded-2xl border bg-white p-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold">Continue writing</h2>
            <a href="{{ route('devotionals.index', ['mine' => 1]) }}" class="text-sm text-gray-600 hover:text-gray-800">All my posts</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($myDrafts as $d)
                <a href="{{ route('devotionals.edit', $d) }}" class="rounded-xl border p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="font-medium">{{ $d->title }}</div>
                        <div class="text-xs uppercase tracking-wide {{ $d->status === 'draft' ? 'text-amber-600' : 'text-indigo-600' }}">{{ $d->status }}</div>
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

    {{-- Community comments --}}
    <section class="rounded-2xl border bg-white p-6">
        <h2 class="text-lg font-semibold mb-3">From the community</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($recentComments as $c)
                <div class="rounded-xl border p-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $c->author->name ?? 'User' }}</span>
                        on
                        <a href="{{ route('devotionals.show', $c->devotional) }}" class="underline">{{ $c->devotional->title }}</a>
                        <span class="text-xs">• {{ $c->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-2 text-gray-800">{{ \Illuminate\Support\Str::limit($c->body, 160) }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-600">No comments yet.</p>
            @endforelse
        </div>
    </section>

    {{-- Tag cloud --}}
    @if($topTags->isNotEmpty())
    <section class="rounded-2xl border bg-white p-6">
        <h2 class="text-lg font-semibold mb-3">Popular tags</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($topTags as $t)
                <a href="{{ route('devotionals.index', ['tags' => [$t->slug]]) }}"
                   class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs text-gray-700 hover:bg-gray-50">
                    #{{ $t->name }}
                </a>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
