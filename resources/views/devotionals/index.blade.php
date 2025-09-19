{{-- resources/views/devotionals/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Devotionals</h1>
        @auth
            <a href="{{ route('devotionals.create') }}"
               class="rounded-xl border px-4 py-2 text-sm hover:bg-gray-50">
                + New
            </a>
        @endauth
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('devotionals.index') }}"
          class="mb-6 rounded-2xl border bg-white p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

            {{-- Search --}}
            <div class="md:col-span-2">
                <label for="q" class="block text-sm font-medium mb-1">Search</label>
                <input id="q" name="q" value="{{ $q ?? '' }}" placeholder="Title, excerpt, or body..."
                       class="block w-full rounded-xl border-gray-300 px-3 py-2
                              focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Scope --}}
            <div>
                <label class="block text-sm font-medium mb-1">Scope</label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="mine" value="1"
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ ($mine ?? false) ? 'checked' : '' }}>
                    <span class="text-sm">My posts</span>
                </label>
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium mb-1">Status</label>
                <select id="status" name="status"
                        class="block w-full rounded-xl border-gray-300 px-3 py-2
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Any</option>
                    <option value="published" {{ ($status ?? '')==='published' ? 'selected' : '' }}>Published</option>
                    <option value="draft"     {{ ($status ?? '')==='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ ($status ?? '')==='scheduled' ? 'selected' : '' }}>Scheduled</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Enable “My posts” to filter by status.</p>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-2 flex items-center gap-3">
                <button class="rounded-xl bg-indigo-600 text-white px-4 py-2 hover:bg-indigo-700">
                    Apply
                </button>
                <a href="{{ route('devotionals.index') }}"
                   class="rounded-xl border px-4 py-2 text-sm hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </div>

        {{-- Preserve selected tags so Apply doesn't clear them --}}
        @if(!empty($tagSlugs))
            @foreach($tagSlugs as $slug)
                <input type="hidden" name="tags[]" value="{{ $slug }}">
            @endforeach
        @endif
    </form>

    {{-- Tag cloud --}}
    @if(isset($allTags) && $allTags->isNotEmpty())
    <div class="mb-6 rounded-2xl border bg-white p-4">
        <div class="text-sm text-gray-600 mb-2">Filter by tag</div>
        <div class="flex flex-wrap gap-2">
            @foreach($allTags as $t)
                @php
                    $active   = in_array($t->slug, $tagSlugs ?? []);
                    $current  = request()->all();
                    $newTags  = $active
                        ? array_values(array_diff($tagSlugs ?? [], [$t->slug]))
                        : array_values(array_unique(array_merge($tagSlugs ?? [], [$t->slug])));
                    if (empty($newTags)) { unset($current['tags']); } else { $current['tags'] = $newTags; }
                    $url = route('devotionals.index', $current);
                @endphp
                <a href="{{ $url }}"
                   class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs
                          {{ $active ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    #{{ $t->name }}
                    @if($active)<span class="ml-1">&times;</span>@endif
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- List --}}
    <div class="space-y-4">
        @forelse ($devos as $devo)
            <article class="rounded-2xl bg-white border p-4 shadow-sm">
                <div class="flex gap-4">
                    @if($devo->cover_path)
                        <a href="{{ route('devotionals.show', $devo) }}" class="shrink-0">
                            <img src="{{ asset('storage/'.$devo->cover_path) }}" alt=""
                                 class="w-28 h-20 object-cover rounded-lg border">
                        </a>
                    @endif

                    <div class="flex-1">
                        <a href="{{ route('devotionals.show', $devo) }}">
                            <h2 class="text-lg font-semibold hover:underline">{{ $devo->title }}</h2>
                        </a>

                        <p class="mt-1 text-xs text-gray-500">
                            @if($devo->author) By {{ $devo->author->name }} • @endif
                            @if($devo->published_at) {{ $devo->published_at->format('M j, Y') }} @endif
                            @if($devo->status !== 'published')
                                • <span class="uppercase tracking-wide">{{ $devo->status }}</span>
                            @endif
                        </p>

                        @if($devo->excerpt)
                            <p class="mt-2 text-gray-700">{{ $devo->excerpt }}</p>
                        @endif

                        @if($devo->tags->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($devo->tags as $t)
                                    @php
                                        $active   = in_array($t->slug, $tagSlugs ?? []);
                                        $current  = request()->all();
                                        $newTags  = $active
                                            ? array_values(array_diff($tagSlugs ?? [], [$t->slug]))
                                            : array_values(array_unique(array_merge($tagSlugs ?? [], [$t->slug])));
                                        if (empty($newTags)) { unset($current['tags']); } else { $current['tags'] = $newTags; }
                                        $url = route('devotionals.index', $current);
                                    @endphp
                                    <a href="{{ $url }}"
                                       class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs
                                              {{ $active ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                        #{{ $t->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-3 flex items-center gap-3">
                            @can('update', $devo)
                                <a href="{{ route('devotionals.edit', $devo) }}"
                                   class="rounded-lg border px-3 py-1 text-sm hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('devotionals.destroy', $devo) }}"
                                      onsubmit="return confirm('Delete this devotional?');">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg border px-3 py-1 text-sm text-red-600 hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl bg-white border p-8 text-center text-gray-500">
                No devotionals found.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $devos->links() }}
    </div>
</div>
@endsection
