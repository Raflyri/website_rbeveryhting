@extends('tools.base64._layout')

@section('content')
<div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6">
    <h2 class="text-xl font-semibold mb-2">{{ $endpoint->name }}</h2>
    <p class="text-sm text-slate-400 mb-4">
        Paste a Base64 string and desired filename. The API will decode it and this page will return an image download.
    </p>

    @include('tools.base64.partials._request_form', ['submitLabel' => 'Decode and download'])

    <p class="mt-4 text-xs text-slate-500">
        On success, you should see your browser start downloading the decoded image. If something goes wrong, an error
        message will appear above.
    </p>
</div>
@endsection