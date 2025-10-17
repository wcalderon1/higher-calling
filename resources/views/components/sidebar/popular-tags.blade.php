<div class="p-4 border rounded-2xl">
  <h3 class="text-sm font-semibold mb-3">Popular Tags</h3>
  @if(($popularTags ?? collect())->isEmpty())
    <div class="text-sm text-gray-500">No tags yet.</div>
  @else
    <div class="flex flex-wrap gap-2">
      @foreach($popularTags as $tag)
        <a href="{{ route('devotionals.index', ['tag' => $tag->slug]) }}"
           title="{{ $tag->description ?? $tag->name }}"
           class="px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs hover:bg-amber-200">
          #{{ $tag->name }}
          <span class="text-[10px] text-amber-700 ml-1">{{ $tag->recent_published_count }}</span>
        </a>
      @endforeach
    </div>
  @endif
</div>
