@extends('layouts.app')

@section('content-left')
@php
    // Controller typically provides: $user and $followers (LengthAwarePaginator or Collection)
    $list = $followers ?? collect();
@endphp

<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Followers</h1>
        <p class="text-gray-600">People who follow {{ $user->display_name ?? $user->name }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y">
        @forelse($list as $follower)
            <div class="p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ $follower->avatar_path ? asset('storage/'.$follower->avatar_path) : asset('images/avatar-default.png') }}"
                         class="w-9 h-9 rounded-full object-cover border" alt="">
                    <div class="min-w-0">
                        <a href="{{ route('profile.show', $follower) }}" class="font-medium text-gray-900 hover:text-indigo-700 truncate">
                            {{ $follower->display_name ?? $follower->name }}
                        </a>
                        <div class="text-xs text-gray-500 truncate">
                            @if(!empty($follower->bio)) {{ \Illuminate\Support\Str::limit($follower->bio, 80) }} @endif
                        </div>
                    </div>
                </div>

                @auth
                    @if(auth()->id() !== $follower->id)
                        @php $isFollowing = auth()->user()->isFollowing($follower->id ?? null); @endphp
                        <form action="{{ $isFollowing ? route('follow.destroy', $follower) : route('follow.store', $follower) }}"
                              method="POST" class="shrink-0">
                            @csrf
                            @if($isFollowing)
                                @method('DELETE')
                            @endif
                            <button
                                class="px-3 py-1.5 rounded-lg text-sm
                                       {{ $isFollowing ? 'border text-gray-700 hover:bg-gray-50' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">No followers yet.</div>
        @endforelse
    </div>

    @if(method_exists($list, 'links'))
        <div>{{ $list->links() }}</div>
    @endif
</div>
@endsection
