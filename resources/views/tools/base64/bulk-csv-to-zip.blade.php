@extends('tools.base64._layout')

@section('content')
<div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6">
    <h2 class="text-xl font-semibold mb-2">{{ $endpoint->name }}</h2>
    <p class="text-sm text-slate-400 mb-4">
        Upload a CSV file containing an <code class="font-mono text-xs">id</code> column and an
        <code class="font-mono text-xs">image</code> column (Base64 strings). The API will return a ZIP with decoded files.
    </p>

    @include('tools.base64.partials._request_form', ['submitLabel' => 'Upload and generate ZIP'])

    <p class="mt-4 text-xs text-slate-500">
        On success, your browser should start downloading a ZIP archive containing the decoded images. If the API
        returns an error, it will be shown above.
    </p>
</div>
@endsection