@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div class="min-w-0">
      <h1 class="text-3xl font-bold text-gray-900 truncate">{{ $plan->title }}</h1>
      @if($plan->description)
        <p class="text-gray-600 text-sm md:text-base">{{ $plan->description }}</p>
      @endif
    </div>

    @auth
      <form method="post" action="{{ route('plans.start',$plan) }}">
        @csrf
        <button
          class="text-sm px-3 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 text-white hover:opacity-90">
          Start / Resume
        </button>
      </form>
    @endauth
  </div>

  {{-- Progress --}}
  @auth
    @if($userPlan)
      <div class="mb-6">
        <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
          <span>Progress</span>
          <span class="font-medium text-gray-800">{{ $userPlan->progressPercent() }}%</span>
        </div>
        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-rose-400"
               style="width: {{ $userPlan->progressPercent() }}%"></div>
        </div>
      </div>
    @endif
  @endauth

  {{-- Entries --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($plan->entries as $entry)
      @php
        $done = isset($userEntries[$entry->id]) && $userEntries[$entry->id]->completed_at;
        $devo = $entry->devotional ?? null;
      @endphp

      <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-5 border border-gray-100">
        <div class="flex items-start justify-between gap-4">
          {{-- Left column: day + linked title + meta --}}
          <div class="min-w-0 flex-1">
            <div class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium text-gray-800 bg-gray-100">
              Day {{ $entry->day_number }}
            </div>

            @if($devo)
              {{-- Title is a link to the devotional page --}}
              <a href="{{ route('devotionals.show', $devo) }}"
                 class="mt-1 block text-[15px] font-semibold text-gray-900 hover:text-indigo-700 transition truncate"
                 title="{{ $devo->title }}">
                <span class="break-words">{{ $devo->title }}</span>
              </a>

              {{-- Curated chip --}}
              @if($devo->is_curated)
                <div class="mt-1">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-emerald-100 text-emerald-800">
                    Curated
                  </span>
                </div>
              @endif

            @elseif(!empty($entry->title))
              <div class="mt-1 text-[15px] font-semibold text-gray-900 truncate" title="{{ $entry->title }}">
                <span class="break-words">{{ $entry->title }}</span>
              </div>
            @endif

            {{-- Scripture reference (optional) --}}
            @if(!empty($entry->scripture_ref))
              <div class="mt-1 text-sm text-gray-600 truncate">{{ $entry->scripture_ref }}</div>
            @endif
          </div>

          {{-- Right column: toggle button --}}
          @auth
            <form method="post" action="{{ route('plan_entries.toggle',$entry) }}" class="shrink-0">
              @csrf
              {{-- Keep the same endpoint; if your backend toggles, this will undo as well --}}
              @if($done)
                <button
                  class="inline-flex items-center justify-center rounded-lg text-xs px-3 py-1.5 bg-gray-200 text-gray-800 hover:bg-gray-300"
                  aria-label="Undo completion for day {{ $entry->day_number }}">
                  Undo
                </button>
              @else
                <button
                  class="inline-flex items-center justify-center rounded-lg text-xs px-3 py-1.5
                         bg-gradient-to-r from-amber-500 to-orange-600 text-white hover:opacity-90"
                  aria-label="Mark done for day {{ $entry->day_number }}">
                  Mark Done
                </button>
              @endif
            </form>
          @endauth
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
