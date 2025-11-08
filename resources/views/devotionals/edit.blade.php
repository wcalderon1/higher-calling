@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">
    <div class="bg-white rounded-2xl shadow-sm border">
        <div class="px-6 py-5 border-b flex items-center justify-between">
            <h1 class="text-xl font-semibold">Edit Devotional</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('devotionals.show', $devotional) }}" class="text-sm rounded-lg border px-3 py-1 hover:bg-gray-50">View</a>
                <form method="POST" action="{{ route('devotionals.destroy', $devotional) }}" onsubmit="return confirm('Delete this devotional?');">
                    @csrf @method('DELETE')
                    <button class="text-sm rounded-lg border px-3 py-1 text-red-600 hover:bg-red-50">Delete</button>
                </form>
            </div>
        </div>

        <div class="px-6 py-6">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('devotionals.update', $devotional) }}" class="space-y-6" enctype="multipart/form-data">
                @csrf @method('PATCH')

                <div>
                    <label class="block text-sm font-medium mb-1" for="title">Title</label>
                    <input id="title" name="title" value="{{ old('title', $devotional->title) }}" required
                           class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="excerpt">Short excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="2"
                              class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">{{ old('excerpt', $devotional->excerpt) }}</textarea>
                </div>

                {{-- Cover image --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Cover image</label>
                    @if($devotional->cover_path)
                        <img src="{{ asset('storage/'.$devotional->cover_path) }}" alt="" class="mb-3 rounded-xl border max-h-56 object-cover">
                        <label class="inline-flex items-center gap-2 mb-2">
                            <input type="checkbox" name="remove_cover" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm">Remove current image</span>
                        </label>
                    @endif
                    <input id="cover" type="file" name="cover" accept="image/*"
                           class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">PNG/JPG, up to 2 MB.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="body">Body</label>
                    <textarea id="body" name="body" rows="10" required
                              class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">{{ old('body', $devotional->body) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="tags">Tags</label>
                    <input id="tags" name="tags" value="{{ old('tags', $devotional->tags->pluck('name')->implode(', ')) }}"
                           placeholder="faith, prayer, gratitude"
                           class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Comma separated.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @php $status = old('status', $devotional->status); @endphp
                    <div>
                        <label class="block text-sm font-medium mb-1" for="status">Status</label>
                        <select id="status" name="status"
                                class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="draft"     {{ $status==='draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $status==='published' ? 'selected' : '' }}>Published</option>
                            <option value="scheduled" {{ $status==='scheduled' ? 'selected' : '' }}>Scheduled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="published_at">Publish at</label>
                        <input id="published_at" type="datetime-local" name="published_at"
                               value="{{ old('published_at', optional($devotional->published_at)->format('Y-m-d\TH:i')) }}"
                               class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <a href="{{ route('devotionals.show', $devotional) }}" class="text-sm px-4 py-2 rounded-xl border hover:bg-gray-50">Cancel</a>
                    <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
