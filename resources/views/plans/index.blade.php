@extends('layouts.app')

@section('content-left')

<div class="space-y-10">

    {{-- Header --}}
    <div class="text-left space-y-3">
        <h1 class="text-3xl font-bold text-gray-900">Reading Plans</h1>
        <p class="text-gray-600 text-sm md:text-base">
            Grow deeper in your faith one day at a time. Track your progress and stay consistent on your spiritual journey.
        </p>
    </div>

    {{-- Active Plans --}}
    @if($userPlans->isNotEmpty())
        <section class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Active Plans</h2>

            <div class="space-y-6">
                @foreach($userPlans as $up)
                    @php
                        $completed = $up->entries()->whereNotNull('completed_at')->count();
                        $total = $up->plan->length_days;
                        $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                        $statusColor = [
                            'active' => 'bg-emerald-100 text-emerald-800',
                            'paused' => 'bg-amber-100 text-amber-800',
                            'completed' => 'bg-indigo-100 text-indigo-800',
                        ][$up->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                        <div class="flex justify-between items-start mb-4 gap-4">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 hover:text-indigo-700 transition truncate">
                                    <a href="{{ route('plans.show', $up->plan->slug) }}">
                                        {{ $up->plan->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    {{ $up->plan->length_days }} days
                                    <span class="mx-1">•</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $statusColor }} capitalize">
                                        {{ $up->status }}
                                    </span>
                                </p>
                            </div>

                            <a href="{{ route('plans.show', $up->plan->slug) }}"
                               class="text-sm px-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 text-white hover:opacity-90 shrink-0">
                                Resume
                            </a>
                        </div>

                        {{-- Progress --}}
                        <div>
                            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-rose-400"
                                     style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="mt-1.5 text-xs text-gray-600">
                                {{ $percent }}% complete • {{ $completed }} of {{ $total }} days
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Available Plans --}}
    <section class="space-y-6">
        <h2 class="text-xl font-semibold text-gray-800">Available Plans</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($plans as $plan)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6 border border-gray-100 flex flex-col">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 hover:text-indigo-700 transition">
                            <a href="{{ route('plans.show', $plan->slug) }}">{{ $plan->title }}</a>
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $plan->description }}</p>
                    </div>

                    <div class="mt-4 pt-3 flex items-center justify-between border-t border-gray-100">
                        <span class="text-xs text-gray-500">{{ $plan->length_days }} days</span>
                        <a href="{{ route('plans.show', $plan->slug) }}"
                           class="text-sm px-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 text-white hover:opacity-90">
                            Start Plan
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                    <div class="text-6xl mb-2">📖</div>
                    <p class="text-gray-700 font-medium">No reading plans available yet.</p>
                    <p class="mt-1 text-gray-500 text-sm">Check back soon for new spiritual journeys.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>

@endsection


{{-- 👉 Only define the sidebar section if it actually has content --}}
@php
    $hasAuthors = ($recentAuthors ?? collect())->isNotEmpty();
    $hasTags = ($popularTags ?? collect())->isNotEmpty();
@endphp

@if($hasAuthors || $hasTags)
    @section('content-right')
        @if($hasAuthors)
            @include('components.sidebar.recent-authors', ['recentAuthors' => $recentAuthors])
        @endif

        @if($hasTags)
            @include('components.sidebar.popular-tags', ['popularTags' => $popularTags])
        @endif
    @endsection
@endif
