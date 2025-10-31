<div class="p-4 rounded-2xl border border-gray-200 bg-white">
  <div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-semibold text-gray-900">Popular Tags</h3>
    <a href="{{ route('devotionals.index') }}"
       class="text-xs text-indigo-700 hover:underline">Browse all →</a>
  </div>

  @if(($popularTags ?? collect())->isEmpty())
    <div class="text-sm text-gray-500 px-0.5 py-1.5">No tags yet.</div>
  @else
    <div class="flex flex-wrap gap-2">
      @foreach($popularTags as $tag)
        <a
          href="{{ route('devotionals.index', ['tag' => $tag->slug]) }}"
          title="{{ $tag->description ?? $tag->name }}"
          aria-label="Tag {{ $tag->name }}, {{ $tag->recent_published_count }} {{ Str::plural('devotional', $tag->recent_published_count) }}"
          class="group inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs text-amber-900 transition 
                 hover:bg-amber-100 hover:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300"
        >
          <span class="truncate max-w-[9rem]">#{{ $tag->name }}</span>
          <span class="rounded-md bg-amber-200/80 px-1.5 py-0.5 text-[10px] tabular-nums text-amber-800 group-hover:bg-amber-300">
            {{ $tag->recent_published_count }}
          </span>
        </a>
      @endforeach
    </div>
  @endif
</div>
