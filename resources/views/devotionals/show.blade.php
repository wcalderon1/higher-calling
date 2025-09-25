{{-- resources/views/devotionals/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('devotionals.index') }}" class="text-sm text-gray-600 hover:text-gray-800">← Back to Devotionals</a>

        @can('update', $devotional)
        <div class="flex items-center gap-3">
            <a href="{{ route('devotionals.edit', $devotional) }}" class="rounded-lg border px-3 py-1 text-sm hover:bg-gray-50">Edit</a>
            <form method="POST" action="{{ route('devotionals.destroy', $devotional) }}"
                  onsubmit="return confirm('Delete this devotional?');">
                @csrf @method('DELETE')
                <button class="rounded-lg border px-3 py-1 text-sm text-red-600 hover:bg-red-50">Delete</button>
            </form>
        </div>
        @endcan
    </div>

    <article class="rounded-2xl border bg-white p-7 shadow-sm">
        @if($devotional->cover_path)
            <img src="{{ asset('storage/'.$devotional->cover_path) }}"
                 alt="{{ $devotional->title }} cover"
                 class="w-full rounded-xl border mb-5 object-cover max-h-96">
        @endif

        <header>
            <h1 class="text-3xl font-semibold leading-tight">{{ $devotional->title }}</h1>
            <p class="mt-2 text-sm text-gray-500">
                By {{ optional($devotional->author)->name ?? 'Unknown' }}
                @if($devotional->published_at) • {{ $devotional->published_at->format('M j, Y') }} @endif
                @if($devotional->status !== 'published') • <span class="uppercase tracking-wide">{{ $devotional->status }}</span> @endif
            </p>

            @if($devotional->excerpt)
                <p class="mt-4 text-gray-700">{{ $devotional->excerpt }}</p>
            @endif

            @if($devotional->tags->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($devotional->tags as $t)
                        <a href="{{ route('devotionals.index', ['tags' => [$t->slug]]) }}"
                           class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs text-gray-700 hover:bg-gray-50">
                           #{{ $t->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </header>

        <div class="prose max-w-none mt-6">
            {!! nl2br(e($devotional->body)) !!}
        </div>
    </article>

    {{-- Comments --}}
    <section id="comments" class="mt-8">
        <h2 class="text-lg font-semibold mb-3">Discussion</h2>

        @auth
            <form method="POST" action="{{ route('comments.store', $devotional) }}"
                  class="mb-5 rounded-2xl border bg-white p-4">
                @csrf
                <label for="comment-body" class="block text-sm font-medium mb-1">Add a comment</label>
                <textarea id="comment-body" name="body" rows="3" required
                          class="w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                          placeholder="Share your thoughts...">{{ old('body') }}</textarea>
                @error('body')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="mt-3 flex justify-end">
                    <button class="rounded-xl bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700">
                        Post Comment
                    </button>
                </div>
            </form>
        @else
            <div class="mb-5 rounded-2xl border bg-white p-4 text-sm text-gray-600">
                <a class="underline" href="{{ route('login') }}">Log in</a> to join the discussion.
            </div>
        @endauth

        @forelse ($comments as $c)
            <article class="mb-3 rounded-2xl border bg-white p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $c->author->name ?? 'User' }}</span>
                        <span>• {{ $c->created_at->diffForHumans() }}</span>
                    </div>
                    @can('delete', $c)
                    <form method="POST" action="{{ route('comments.destroy', ['devotional' => $devotional, 'comment' => $c]) }}"
                          onsubmit="return confirm('Delete this comment?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs rounded border px-2 py-1 text-red-600 hover:bg-red-50">Delete</button>
                    </form>

                    @endcan
                </div>
                <p class="mt-2 text-gray-800 whitespace-pre-line">{{ $c->body }}</p>
            </article>
        @empty
            <div class="rounded-2xl border bg-white p-4 text-sm text-gray-600">
                Be the first to comment.
            </div>
        @endforelse
    </section>

    {{-- Related --}}
    @if(isset($related) && $related->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-lg font-semibold mb-3">Related Devotionals</h2>
            <div class="space-y-4">
                @foreach($related as $r)
                    <article class="rounded-2xl bg-white border p-5">
                        <a href="{{ route('devotionals.show', $r) }}">
                            <h3 class="text-base font-semibold hover:underline">{{ $r->title }}</h3>
                        </a>
                        <p class="mt-1 text-xs text-gray-500">
                            @if($r->author) By {{ $r->author->name }} • @endif
                            @if($r->published_at) {{ $r->published_at->format('M j, Y') }} @endif
                        </p>
                        @if($r->excerpt)
                            <p class="mt-2 text-sm text-gray-700">
                                {{ \Illuminate\Support\Str::limit($r->excerpt, 160) }}
                            </p>
                        @endif
                        @if($r->tags->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($r->tags as $t)
                                    <a href="{{ route('devotionals.index', ['tags' => [$t->slug]]) }}"
                                       class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs text-gray-700 hover:bg-gray-50">
                                        #{{ $t->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
