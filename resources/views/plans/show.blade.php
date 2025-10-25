@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-5">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold tracking-tight truncate">{{ $plan->title }}</h1>
      @if($plan->description)
        <p class="text-gray-600">{{ $plan->description }}</p>
      @endif
    </div>
    @auth
      <form method="post" action="{{ route('plans.start',$plan) }}">
        @csrf
        <button class="px-3 py-2 rounded-xl bg-amber-600 text-white hover:bg-amber-700 transition">
          Start / Resume
        </button>
      </form>
    @endauth
  </div>

  {{-- Progress --}}
  @auth
    @if($userPlan)
      <div class="mb-5 space-y-1">
        <div class="flex items-center justify-between text-sm text-gray-600">
          <span>Progress</span>
          <span class="font-medium text-gray-800">{{ $userPlan->progressPercent() }}%</span>
        </div>
        <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
          <div class="h-2 rounded-full"
               style="width: {{ $userPlan->progressPercent() }}%;
                      background: linear-gradient(90deg, #f59e0b, #f97316);">
          </div>
        </div>
      </div>
    @endif
  @endauth

  {{-- Entries --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    @foreach($plan->entries as $entry)
      @php
        $done   = isset($userEntries[$entry->id]) && $userEntries[$entry->id]->completed_at;
        $devo   = $entry->devotional ?? null;
      @endphp

      <div class="border rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition">
        <div class="flex items-center gap-3">
          {{-- Left: day + title + meta --}}
          <div class="flex-1 min-w-0">
            <div class="inline-flex items-center text-[11px] font-medium text-gray-700 bg-gray-100 rounded-full px-2 py-0.5 mb-1">
              Day {{ $entry->day_number }}
            </div>

            @if($devo)
              <a
                class="block font-medium text-amber-700 hover:text-amber-800 underline underline-offset-2 truncate"
                href="{{ route('devotionals.show', $devo) }}"
                title="{{ $devo->title }}"
              >
                {{ $devo->title }}
              </a>

              {{-- Curated badge (kept subtle) --}}
              @if($devo->is_curated)
                <div class="mt-1">
                  <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-[11px] text-green-700">Curated</span>
                </div>
              @endif
            @elseif(!empty($entry->title))
              <div class="font-medium truncate" title="{{ $entry->title }}">{{ $entry->title }}</div>
            @endif

            @if(!empty($entry->scripture_ref))
              <div class="mt-1 text-sm text-gray-600 truncate">{{ $entry->scripture_ref }}</div>
            @endif
          </div>

          {{-- Right: action pill (fixed size) --}}
          @auth
            @if($done)
              <span class="inline-flex items-center justify-center rounded-md bg-green-600 text-white text-xs px-3 py-1.5">
                Done
              </span>
            @else
              <form method="post" action="{{ route('plan_entries.toggle',$entry) }}">
                @csrf
                <button
                  class="inline-flex items-center justify-center rounded-md bg-gray-800 text-white text-xs px-3 py-1.5 hover:bg-gray-900"
                  type="submit"
                >
                  Mark Done
                </button>
              </form>
            @endif
          @endauth
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
