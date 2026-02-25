@extends('tools.base64._layout')

@section('content')
<div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
    <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6">
        <h2 class="text-xl font-semibold mb-2">{{ $endpoint->name }}</h2>
        <p class="text-sm text-slate-400 mb-4">
            Ping the Base64 API to ensure it is reachable and healthy.
        </p>

        @include('tools.base64.partials._request_form', ['submitLabel' => 'Run health check'])
    </div>

    <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6">
        <h3 class="text-sm font-semibold text-slate-200 mb-3">Result</h3>

        @include('tools.base64.partials._response_display', [
        'emptyMessage' => 'No result yet. Run a health check to see the API status and response payload.',
        ])
    </div>
</div>
@endsection