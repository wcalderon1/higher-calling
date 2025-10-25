@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-5">
    <div>
      <h1 class="text-2xl font-bold tracking-tight">{{ $plan->title }}</h1>
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
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($plan->entries as $entry)
      @php $done = isset($userEntries[$entry->id]) && $userEntries[$entry->id]->completed_at; @endphp

      <div class="border rounded-2xl p-4 bg-white/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition border-gray-200">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            {{-- Day pill --}}
            <div class="inline-flex items-center text-xs font-medium text-gray-700 bg-gray-100 rounded-full px-2 py-0.5 mb-1">
              Day {{ $entry->day_number }}
            </div>

            @if($entry->devotional)
              <a class="group inline-flex items-center gap-1 text-amber-700 hover:text-amber-800 underline underline-offset-2"
                 href="{{ route('devotionals.show', $entry->devotional) }}">
                <span class="truncate">{{ $entry->devotional->title }}</span>
                <svg class="w-4 h-4 transition group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path d="M12.293 3.293a1 1 0 011.414 0L18 7.586a1 1 0 010 1.414l-4.293 4.293a1 1 0 11-1.414-1.414L14.586 9H4a1 1 0 110-2h10.586l-2.293-2.293a1 1 0 010-1.414z"/>
                </svg>
              </a>

      {{-- Curated badge only (temporarily hide author to avoid null crash) --}}
  <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
   @if($entry->devotional && $entry->devotional->is_curated)
    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-green-700">Curated</span>
   @endif
  </div>

            @elseif($entry->title)
              <div class="font-medium">{{ $entry->title }}</div>
            @endif

            @if($entry->scripture_ref)
              <div class="mt-1 text-sm text-gray-600">{{ $entry->scripture_ref }}</div>
            @endif
          </div>

          @auth
            @if($done)
              <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 text-xs px-3 py-1 border border-green-200">
                Done
              </span>
            @else
              <form method="post" action="{{ route('plan_entries.toggle',$entry) }}">
                @csrf
                <button class="px-3 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-900 text-sm">
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
