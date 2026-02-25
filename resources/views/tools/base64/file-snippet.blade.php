@extends('tools.base64._layout')

@section('content')
<div class="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
    <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6">
        <h2 class="text-xl font-semibold mb-2">{{ $endpoint->name }}</h2>
        <p class="text-sm text-slate-400 mb-4">
            Upload an image or other asset to generate a ready‑to‑use HTML snippet with Base64 data.
        </p>

        @include('tools.base64.partials._request_form', ['submitLabel' => 'Generate HTML snippet'])
    </div>

    <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6">
        <h3 class="text-sm font-semibold text-slate-200 mb-3">HTML snippet</h3>

        @include('tools.base64.partials._response_display', [
        'emptyMessage' => 'Upload a file to see the generated HTML snippet from the API.',
        ])
    </div>
</div>
@endsection