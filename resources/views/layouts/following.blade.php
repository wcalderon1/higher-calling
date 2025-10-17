@extends('layouts.app')

@section('content-left')
@php
    // Controller typically provides: $user and $following
    $list = $following ?? collect();
@endphp

<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Following</h1>
        <p class="text-gray-600">People followed by {{ $user->display_name ?? $user->name }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y">
        @forelse($list as $followed)
            <div class="p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ $followed->avatar_path ? asset('storage/'.$followed->avatar_path) : asset('images/avatar-default.png') }}"
                         class="w-9 h-9 rounded-full object-cover border" alt="">
                    <div class="min-w-0">
                        <a href="{{ route('profile.show', $followed) }}" class="font-medium text-gray-900 hover:text-indigo-700 truncate">
                            {{ $followed->display_name ?? $followed->name }}
                        </a>
                        <div class="text-xs text-gray-500 truncate">
                            @if(!empty($followed->bio)) {{ \Illuminate\Support\Str::limit($followed->bio, 80) }} @endif
                        </div>
                    </div>
                </div>

                @auth
                    @if(auth()->id() !== $followed->id)
                        @php $isFollowing = auth()->user()->isFollowing($followed->id ?? null); @endphp
                        <form action="{{ $isFollowing ? route('follow.destroy', $followed) : route('follow.store', $followed) }}"
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
            <div class="p-8 text-center text-gray-500">Not following anyone yet.</div>
        @endforelse
    </div>

    @if(method_exists($list, 'links'))
        <div>{{ $list->links() }}</div>
    @endif
</div>
@endsection
