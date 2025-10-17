@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">{{ $plan->title }}</h1>
      @if($plan->description)<p class="text-gray-600">{{ $plan->description }}</p>@endif
    </div>
    @auth
      <form method="post" action="{{ route('plans.start',$plan) }}">
        @csrf
        <button class="px-3 py-2 rounded bg-amber-600 text-white">Start / Resume</button>
      </form>
    @endauth
  </div>

  @auth
    @if($userPlan)
      <div class="mb-4">
        <div class="text-sm">Progress: {{ $userPlan->progressPercent() }}%</div>
        <div class="w-full bg-gray-200 h-2 rounded">
          <div class="bg-amber-500 h-2 rounded" style="width: {{ $userPlan->progressPercent() }}%"></div>
        </div>
      </div>
    @endif
  @endauth

  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    @foreach($plan->entries as $entry)
      @php $done = isset($userEntries[$entry->id]) && $userEntries[$entry->id]->completed_at; @endphp
      <div class="border rounded-lg p-3 flex items-center justify-between">
        <div>
          <div class="font-semibold">Day {{ $entry->day_number }}</div>
          @if($entry->devotional)
            <a class="text-amber-700 underline" href="{{ route('devotionals.show',$entry->devotional->slug) }}">
              {{ $entry->devotional->title }}
            </a>
          @elseif($entry->title)
            <div>{{ $entry->title }}</div>
          @endif
          @if($entry->scripture_ref)
            <div class="text-sm text-gray-600">{{ $entry->scripture_ref }}</div>
          @endif
        </div>
        @auth
          <form method="post" action="{{ route('plan_entries.toggle',$entry) }}">
            @csrf
            <button class="px-3 py-2 rounded {{ $done ? 'bg-green-600' : 'bg-gray-300' }} text-white">
              {{ $done ? 'Done' : 'Mark Done' }}
            </button>
          </form>
        @endauth
      </div>
    @endforeach
  </div>
</div>
@endsection
