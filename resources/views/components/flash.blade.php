{{-- Flash alerts + validation errors (dismissible, no Alpine needed) --}}

@php
    // Build a small list of alerts with LITERAL Tailwind classes (so the JIT picks them up)
    $alerts = [];

    if (session('ok') || session('success') || session('status')) {
        $alerts[] = [
            'text' => session('ok') ?? session('success') ?? session('status'),
            'classes' => 'bg-green-50 border-green-300 text-green-800',
        ];
    }

    if (session('warning')) {
        $alerts[] = [
            'text' => session('warning'),
            'classes' => 'bg-yellow-50 border-yellow-300 text-yellow-800',
        ];
    }

    if (session('error') || session('danger')) {
        $alerts[] = [
            'text' => session('error') ?? session('danger'),
            'classes' => 'bg-red-50 border-red-300 text-red-800',
        ];
    }
@endphp

<div class="mx-auto max-w-3xl px-6 mt-4 space-y-3">

    {{-- Validation errors (always red) --}}
    @if ($errors->any())
        <div class="flash rounded-xl border bg-red-50 border-red-300 p-4 text-red-800 relative" role="alert">
            <button type="button" aria-label="Dismiss"
                    onclick="this.closest('.flash').remove()"
                    class="absolute right-3 top-3 font-semibold text-red-700">
                &times;
            </button>
            <div class="font-semibold mb-2">Please fix the following:</div>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Session flashes --}}
    @foreach ($alerts as $a)
        <div class="flash rounded-xl border p-4 relative {{ $a['classes'] }}" role="alert">
            <button type="button" aria-label="Dismiss"
                    onclick="this.closest('.flash').remove()"
                    class="absolute right-3 top-3 font-semibold">
                &times;
            </button>
            <div class="text-sm">{{ $a['text'] }}</div>
        </div>
    @endforeach
</div>
