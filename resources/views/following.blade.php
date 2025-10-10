@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-semibold">Following · {{ $user->display_name ?? $user->name }}</h1>

    @if($following->count())
        <ul class="mt-6 divide-y bg-white rounded-xl border shadow-sm">
            @foreach($following as $u)
                <li class="p-4 flex items-center justify-between">
                    <a href="{{ route('profile.show', $u) }}" class="flex items-center gap-3">
                        <img src="{{ $u->avatar_url }}" class="w-10 h-10 rounded-full object-cover" alt="">
                        <div>
                            <div class="font-medium text-slate-900">{{ $u->display_name ?? $u->name }}</div>
                            <div class="text-sm text-slate-500">{{ '@'.$u->id }}</div>
                        </div>
                    </a>

                    @auth
                        @if(auth()->id() !== $u->id)
                            @php $amFollowing = auth()->user()->isFollowing($u); @endphp
                            <form action="{{ $amFollowing ? route('follow.destroy', $u) : route('follow.store', $u) }}" method="POST">
                                @csrf @if($amFollowing) @method('DELETE') @endif
                                <button class="px-3 py-1.5 rounded-lg text-sm
                                    {{ $amFollowing ? 'bg-white border hover:bg-slate-50' : 'bg-slate-900 text-white hover:bg-slate-700' }}">
                                    {{ $amFollowing ? 'Unfollow' : 'Follow' }}
                                </button>
                            </form>
                        @endif
                    @endauth
                </li>
            @endforeach
        </ul>

        <div class="mt-4">{{ $following->links() }}</div>
    @else
        <p class="mt-6 text-slate-500">Not following anyone yet.</p>
    @endif
</div>
@endsection
