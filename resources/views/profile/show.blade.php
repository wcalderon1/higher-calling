@extends('layouts.app')

@section('content')
@php
    // Page context
    $isMe = auth()->check() && auth()->id() === $user->id;
    $joined = optional($user->created_at)->format('M Y');

    // Devotionals
    $recentDevos = isset($recentDevos) ? $recentDevos : $user->devotionals()->latest()->take(3)->get();
    $devoCount   = $user->devotionals()->count();

    // Followers/Following (guards in case relationships aren't added yet)
    $followersCount = method_exists($user, 'followers') ? $user->followers()->count() : 0;
    $followingCount = method_exists($user, 'following') ? $user->following()->count() : 0;

    // Simple plural helpers
    $devoLabel      = $devoCount === 1 ? 'devotional' : 'devotionals';
    $followerLabel  = $followersCount === 1 ? 'Follower' : 'Followers';
@endphp

<!-- HERO -->
<div class="bg-gradient-to-b from-slate-50 to-white border-b">
    <div class="max-w-4xl mx-auto px-6 py-10">
        <div class="flex items-center gap-6">
            <div class="relative">
                <img src="{{ $user->avatar_url }}"
                     alt="{{ $user->display_name ?? $user->name }}"
                     class="w-28 h-28 rounded-full object-cover ring-4 ring-white shadow-md" />
            </div>

            <div class="min-w-0">
                <h1 class="text-3xl font-semibold text-slate-900 truncate">
                    {{ $user->display_name ?? $user->name }}
                </h1>

                <div class="mt-1 text-sm text-slate-500 flex items-center gap-2 flex-wrap">
                    <span>Joined {{ $joined }}</span>
                    <span class="select-none">•</span>
                    <span>{{ $devoCount }} {{ $devoCount === 1 ? 'devotional' : 'devotionals' }}</span>
                    <span class="select-none">•</span>
                    <span>
                        <a class="hover:underline" href="{{ route('profile.followers', $user) }}">
                            {{ $followersCount }} {{ $followersCount === 1 ? 'Follower' : 'Followers' }}
                        </a>
                    </span>
                    <span class="select-none">•</span>
                    <span>
                        <a class="hover:underline" href="{{ route('profile.following', $user) }}">
                            {{ $followingCount }} Following
                        </a>
                    </span>
                </div>

                <div class="mt-4 flex gap-2">
                    @php
                        $amFollowing = auth()->check() ? auth()->user()->isFollowing($user) : false;
                    @endphp

                    @if($isMe)
                        <a href="{{ route('profile.edit') }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            Edit Profile
                        </a>
                    @elseif(auth()->check())
                        <form action="{{ $amFollowing ? route('follow.destroy', $user) : route('follow.store', $user) }}"
                              method="POST" class="inline">
                            @csrf
                            @if($amFollowing) @method('DELETE') @endif
                            <button class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm
                                           {{ $amFollowing ? 'bg-white border hover:bg-slate-50' : 'bg-slate-900 text-white hover:bg-slate-700' }}">
                                {{ $amFollowing ? 'Unfollow' : 'Follow' }}
                            </button>
                        </form>
                    @endif

                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ route('profile.show', $user) }}'); this.innerText='Link Copied!'; setTimeout(()=>this.innerText='Copy Link', 1500);"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg border text-sm hover:bg-slate-50">
                        Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENT -->
<div class="max-w-4xl mx-auto px-6 pt-8 pb-16">
    <div class="grid gap-6">
        <!-- About card -->
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">About</h2>
            @if($user->bio)
                <p class="mt-3 text-slate-800 leading-relaxed whitespace-pre-line">
                    {{ $user->bio }}
                </p>
            @else
                <p class="mt-3 text-slate-500">This user hasn’t written a bio yet.</p>
            @endif
        </div>

<!-- Latest Devotionals -->
<div class="bg-white rounded-xl border shadow-sm p-6">
    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Latest Devotionals</h2>

    @auth
        @php
            $todayRead = auth()->user()->reads()->whereDate('read_on', today())->first();
            $hasReadToday = (bool) $todayRead;
            $todayDevotionalId = optional($todayRead)->devotional_id;
        @endphp

        @if($hasReadToday)
            <div class="mt-2 inline-flex items-center gap-2 px-2 py-1 rounded bg-green-100 text-green-800 text-xs">
                ✓ You’ve already marked a devotional as read today.
            </div>
        @endif
    @endauth

    @if(($recentDevos ?? collect())->count())
        <ul class="mt-3 divide-y">
            @foreach ($recentDevos as $d)
                <li class="py-3 flex items-center justify-between">
                    <a href="{{ route('devotionals.show', $d) }}"
                       class="font-medium text-slate-900 hover:underline truncate">
                        {{ $d->title }}
                    </a>

                    <div class="flex items-center gap-3 shrink-0 ms-3">
                        <span class="text-sm text-slate-500">{{ optional($d->created_at)->format('M d, Y') }}</span>

                        @auth
                            @if(!$hasReadToday)
                                <form method="POST" action="{{ route('devotionals.read', $d) }}">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs hover:bg-blue-700">
                                        Mark as Read
                                    </button>
                                </form>
                            @else
                                @if(isset($todayDevotionalId) && $todayDevotionalId === $d->id)
                                    <span class="px-2 py-1 rounded-lg bg-green-100 text-green-800 text-xs">
                                        ✓ Read Today
                                    </span>
                                @endif
                            @endif
                        @endauth
                    </div>
                </li>
            @endforeach
        </ul>

        @if($isMe)
            <a href="{{ route('devotionals.index') }}"
               class="mt-4 inline-block text-sm text-slate-600 hover:underline">
               View all devotionals
            </a>
        @endif
    @else
        @if($isMe)
            <p class="mt-3 text-slate-500">You haven’t posted any devotionals yet.</p>
            <a href="{{ route('devotionals.create') }}"
               class="mt-4 inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-700">
               Write your first devotional
            </a>
        @else
            <p class="mt-3 text-slate-500">No devotionals yet.</p>
        @endif
    @endif
</div>

@endsection
