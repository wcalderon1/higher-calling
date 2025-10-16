@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Profile</h1>

    @if (session('status'))
        <div class="mb-4 rounded bg-green-100 text-green-800 p-3">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-100 text-red-700 p-3">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PATCH')

        {{-- Standard Breeze fields --}}
        <div>
            <label class="block text-sm font-medium">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="mt-1 w-full border rounded p-2" required maxlength="255">
        </div>

        <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="mt-1 w-full border rounded p-2" required maxlength="255">
        </div>

        {{-- NEW: Profile fields --}}
        <div>
            <label class="block text-sm font-medium">Display Name</label>
            <input type="text" name="display_name" value="{{ old('display_name', $user->display_name ?? $user->name) }}"
                   class="mt-1 w-full border rounded p-2" required maxlength="80">
            <p class="text-xs text-gray-500">2–80 chars; letters/numbers/spaces “- _ .” allowed.</p>
        </div>

        <div>
            <label class="block text-sm font-medium">Bio</label>
            <textarea name="bio" rows="4" class="mt-1 w-full border rounded p-2" maxlength="500">{{ old('bio', $user->bio) }}</textarea>
            <p class="text-xs text-gray-500">Max 500 characters.</p>
        </div>

        <div>
            <label class="block text-sm font-medium">Avatar</label>
            <div class="flex items-center gap-4">
                <img src="{{ $user->avatar_url }}" alt="Current avatar" class="w-16 h-16 rounded-full object-cover border">
                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp">
            </div>
            <p class="text-xs text-gray-500">PNG/JPG/WEBP • up to 2 MB.</p>
        </div>

        <div class="flex gap-3">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('profile.me') }}" class="px-4 py-2 rounded border">Cancel</a>
        </div>
    </form>
</div>
@endsection
