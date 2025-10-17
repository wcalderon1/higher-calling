@extends('layouts.app')

@section('content-left')
@php
    /** Expected from controller: $user, $followers */
    $list = $followers ?? collect();
    $followersCount = method_exists($user, 'followers') ? ($user->followers_count ?? $user->followers()->count()) : 0;
    $followingCount = method_exists($user, 'following') ? ($user->following_count ?? $user->following()->count()) : 0;
@endphp

<div class="space-y-8">

    {{-- Header + Tabs --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Followers · {{ $user->display_name ?? $user->name }}
                </h1>
                <p class="text-gray-600 text-sm mt-1">People who follow this profile.</p>
            </div>
            <a href="{{ route('profile.show', $user) }}"
               class="text-sm text-indigo-700 hover:underline">Back to profile →</a>
        </div>

        <div class="inline-flex rounded-xl border border-gray-200 bg-white p-1">
            <a href="{{ route('profile.followers', $user) }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('profile.followers') ? 'bg-gray-100 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                Followers <span class="ml-1 rounded-md bg-gray-200 px-1.5 py-0.5 text-[11px] tabular-nums">{{ $followersCount }}</span>
            </a>
            <a href="{{ route('profile.following', $user) }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('profile.following') ? 'bg-gray-100 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                Following <span class="ml-1 rounded-md bg-gray-200 px-1.5 py-0.5 text-[11px] tabular-nums">{{ $followingCount }}</span>
            </a>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y">
        @forelse($list as $follower)
            <div class="p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ $follower->avatar_path ? asset('storage/'.$follower->avatar_path) : asset('images/avatar-default.png') }}"
                         class="w-10 h-10 rounded-full object-cover border" alt="">
                    <div class="min-w-0">
                        <a href="{{ route('profile.show', $follower) }}"
                           class="font-medium text-gray-900 hover:text-indigo-700 truncate">
                            {{ $follower->display_name ?? $follower->name }}
                        </a>
                        <div class="text-xs text-gray-500 truncate">@{{ $follower->id }}</div>
                        @if(!empty($follower->bio))
                            <div class="text-xs text-gray-500 truncate mt-0.5">
                                {{ \Illuminate\Support\Str::limit($follower->bio, 80) }}
                            </div>
                        @endif
                    </div>
                </div>

                @auth
                    @if(auth()->id() !== $follower->id)
                        @php $isFollowing = auth()->user()->isFollowing($follower); @endphp
                        <form action="{{ $isFollowing ? route('follow.destroy', $follower) : route('follow.store', $follower) }}"
                              method="POST" class="shrink-0">
                            @csrf
                            @if($isFollowing) @method('DELETE') @endif
                            <button
                                class="px-3 py-1.5 rounded-lg text-sm transition
                                       {{ $isFollowing ? 'border text-gray-700 hover:bg-gray-50' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        @empty
            <div class="p-10 text-center">
                <div class="text-5xl mb-2">🕊️</div>
                <p class="text-gray-700 font-medium">No followers yet.</p>
                <p class="text-gray-500 text-sm mt-1">Share your profile to invite others to follow.</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($list, 'links'))
        <div>{{ $list->links() }}</div>
    @endif
</div>
@endsection
