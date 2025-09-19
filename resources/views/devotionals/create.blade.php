@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">
    <div class="bg-white rounded-2xl shadow-sm border">
        <div class="px-6 py-5 border-b">
            <h1 class="text-xl font-semibold">New Devotional</h1>
        </div>

        <div class="px-6 py-6">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('devotionals.store') }}" class="space-y-6" enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1" for="title">Title</label>
                    <input id="title" name="title" value="{{ old('title') }}" required
                           class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="excerpt">Short excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="2"
                              class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">{{ old('excerpt') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="cover">Cover image</label>
                    <input id="cover" type="file" name="cover" accept="image/*"
                           class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">PNG/JPG, up to 2 MB.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="body">Body</label>
                    <textarea id="body" name="body" rows="10" required
                              class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">{{ old('body') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="tags">Tags</label>
                    <input id="tags" name="tags" value="{{ old('tags') }}" placeholder="faith, prayer, gratitude"
                           class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Comma separated.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="status">Status</label>
                        <select id="status" name="status"
                                class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="draft">Draft</option>
                            <option value="published" selected>Published</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="published_at">Publish at</label>
                        <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                               class="block w-full rounded-xl border-gray-300 px-3 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <a href="{{ route('devotionals.index') }}" class="text-sm px-4 py-2 rounded-xl border hover:bg-gray-50">Cancel</a>
                    <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
