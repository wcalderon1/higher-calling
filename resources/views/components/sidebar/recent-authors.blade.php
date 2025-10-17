<div class="p-4 border rounded-2xl">
  <h3 class="text-sm font-semibold mb-3">Recent Authors</h3>
  <ul class="space-y-3">
    @forelse($recentAuthors as $u)
      <li class="flex items-center gap-3">
        <img src="{{ asset($u->avatar_path ?? 'images/avatar-default.png') }}"
             alt="{{ $u->display_name ?? $u->name }}"
             class="w-8 h-8 rounded-full object-cover">
        <div class="min-w-0">
          <a href="{{ route('profile.show', $u->id) }}" class="block font-medium truncate">
            {{ $u->display_name ?? $u->name }}
          </a>
          <div class="text-xs text-gray-600">
            {{ $u->recent_published_count }} {{ Str::plural('devotional', $u->recent_published_count) }} • 60d
          </div>
        </div>
      </li>
    @empty
      <li class="text-sm text-gray-500">No recent authors yet.</li>
    @endforelse
  </ul>
</div>
