@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10 space-y-8">

    {{-- Header + Quick action --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
        <a href="{{ route('devotionals.create') }}"
           class="rounded-xl border px-4 py-2 text-sm hover:bg-gray-50">
            + New Devotional
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-gray-500">My posts</div>
            <div class="mt-2 text-2xl font-semibold">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-gray-500">Published</div>
            <div class="mt-2 text-2xl font-semibold text-emerald-600">{{ $stats['published'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-gray-500">Drafts</div>
            <div class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['draft'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl border bg-white p-5">
            <div class="text-sm text-gray-500">Scheduled</div>
            <div class="mt-2 text-2xl font-semibold text-indigo-600">{{ $stats['scheduled'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Two columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent my posts --}}
        <div class="rounded-2xl border bg-white p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold">Your recent devotionals</h2>
                <a href="{{ route('devotionals.index', ['mine' => 1]) }}"
                   class="text-sm text-gray-600 hover:text-gray-800">View all →</a>
            </div>

            @forelse ($recentMine as $d)
                <a href="{{ route('devotionals.show', $d) }}"
                   class="block rounded-xl border p-4 mb-3 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="font-medium">{{ $d->title }}</div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">
                            {{ $d->status }}
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        @if($d->published_at) Published {{ $d->published_at->format('M j, Y') }} · @endif
                        Updated {{ $d->updated_at->diffForHumans() }}
                    </div>
                </a>
            @empty
                <div class="text-sm text-gray-600">No posts yet. Create your first one!</div>
            @endforelse
        </div>

        {{-- Latest comments on my posts --}}
        <div class="rounded-2xl border bg-white p-5">
            <h2 class="text-lg font-semibold mb-3">Latest comments on your posts</h2>

            @forelse ($recentComments as $c)
                <div class="rounded-xl border p-4 mb-3">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $c->author->name ?? 'User' }}</span>
                        <span>on</span>
                        <a href="{{ route('devotionals.show', $c->devotional) }}"
                           class="underline">{{ $c->devotional->title }}</a>
                        <span class="text-xs">• {{ $c->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-2 text-gray-800">
                        {{ \Illuminate\Support\Str::limit($c->body, 160) }}
                    </p>
                </div>
            @empty
                <div class="text-sm text-gray-600">No comments yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Needs attention --}}
    <div class="rounded-2xl border bg-white p-5">
        <h2 class="text-lg font-semibold mb-3">Needs attention</h2>
        @forelse ($needsAttention as $d)
            <a href="{{ route('devotionals.edit', $d) }}"
               class="block rounded-xl border p-4 mb-3 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="font-medium">{{ $d->title }}</div>
                    <div class="text-xs uppercase tracking-wide {{ $d->status === 'draft' ? 'text-amber-600' : 'text-indigo-600' }}">
                        {{ $d->status }}
                    </div>
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    @if($d->status === 'scheduled')
                        Scheduled for {{ optional($d->published_at)->format('M j, Y g:ia') ?? '—' }}
                        @if($d->published_at && $d->published_at->isPast()) • <span class="text-red-600">Due</span> @endif
                    @else
                        Draft · last updated {{ $d->published_at?->diffForHumans() ?? $d->updated_at->diffForHumans() }}
                    @endif
                </div>
            </a>
        @empty
            <div class="text-sm text-gray-600">Nothing urgent right now.</div>
        @endforelse
    </div>

</div>
@endsection
