<div class="p-4 rounded-2xl border border-gray-200 bg-white">
  <div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-semibold text-gray-900">Recent Authors</h3>
    <a href="{{ route('devotionals.index') }}"
       class="text-xs text-indigo-700 hover:underline">Browse all →</a>
  </div>

  <ul class="space-y-2.5">
    @forelse($recentAuthors as $u)
      <li>
        <a href="{{ route('profile.show', $u) }}"
           class="group flex items-center gap-3 rounded-xl px-2.5 py-2 transition hover:bg-gray-50">
          <img
            src="{{ $u->avatar_path ? asset('storage/'.$u->avatar_path) : asset('images/avatar-default.png') }}"
            alt="{{ $u->display_name ?? $u->name }}"
            class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-200"
          >
          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
              <span class="font-medium text-gray-900 truncate group-hover:text-indigo-700">
                {{ $u->display_name ?? $u->name }}
              </span>
              <svg class="w-4 h-4 shrink-0 text-gray-300 group-hover:text-indigo-500"
                   viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707A1 1 0 118.707 5.293l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                      clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="text-xs text-gray-500 truncate">
              {{ $u->recent_published_count }}
              {{ Str::plural('devotional', $u->recent_published_count) }} • 60d
            </div>
          </div>
        </a>
      </li>
    @empty
      <li class="text-sm text-gray-500 px-2.5 py-2">No recent authors yet.</li>
    @endforelse
  </ul>
</div>
