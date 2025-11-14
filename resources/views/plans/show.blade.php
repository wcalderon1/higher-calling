@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 bg-gray-50 rounded-3xl shadow-inner">

<a href="{{ route('plans.index') }}"
   class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-800 mb-4">
   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
       <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
   </svg>
   Back to Reading Plans
</a>


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

        @php
            $totalDays = $plan->entries->count();
            $completedDays = 0;
            if (isset($userEntries)) {
                foreach ($plan->entries as $e) {
                    if (isset($userEntries[$e->id]) && $userEntries[$e->id]->completed_at) {
                        $completedDays++;
                    }
                }
            }
        @endphp

        <div class="mt-2 text-xs md:text-sm text-gray-600">
            You’ve completed
            <span class="font-semibold">{{ $completedDays }}</span>
            of
            <span class="font-semibold">{{ $totalDays }}</span>
            days. Keep going!
        </div>
      </div>
    @endif
  @endauth

  {{-- Determine "current" entry (next incomplete) --}}
  @php
      $currentEntryId = null;
      if ($plan->entries) {
          foreach ($plan->entries as $e) {
              $doneLocal = isset($userEntries)
                  && isset($userEntries[$e->id])
                  && $userEntries[$e->id]->completed_at;
              if (!$doneLocal) {
                  $currentEntryId = $e->id;
                  break;
              }
          }
      }
  @endphp

  {{-- Entries --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($plan->entries as $entry)
      @php
        $done = isset($userEntries[$entry->id]) && $userEntries[$entry->id]->completed_at;
        $devo = $entry->devotional ?? null;
        $isCurrent = $currentEntryId === $entry->id;


        $cardClasses = 'bg-white rounded-2xl transition p-5 border';
        if ($isCurrent) {
    $cardClasses .= ' border-amber-200 ring-2 ring-amber-200/70 shadow-sm bg-amber-50/40';
       }
         else {
            $cardClasses .= ' border-gray-100 shadow-sm hover:shadow-md';
        }
      @endphp

      <div class="{{ $cardClasses }}">
        {{-- center button vertically with the content --}}
        <div class="flex items-center justify-between gap-4">
          {{-- Left column: day + linked title + meta --}}
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <div class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium text-gray-800 bg-gray-100">
                  Day {{ $entry->day_number }}
                </div>

                @if($isCurrent)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-100 text-amber-800">
                        Today
                    </span>
                @endif
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
              <button
                type="submit"
                class="inline-flex items-center justify-center w-28 rounded-full px-4 py-2 text-xs font-semibold tracking-tight
                       transition-colors duration-150
                       {{ $done
                          ? 'bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200'
                          : 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm hover:opacity-95' }}"
                aria-label="{{ $done ? 'Undo completion for day '.$entry->day_number : 'Mark done for day '.$entry->day_number }}">
                {{ $done ? 'Done' : 'Mark Done' }}
              </button>
            </form>
          @endauth
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
