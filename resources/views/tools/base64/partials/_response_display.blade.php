{{-- Generic response display partial - renders fields from $endpoint->responseParams --}}
@php
$responseParams = $endpoint->responseParams()->get();
@endphp

@if($result && $responseParams->isNotEmpty())
<div class="space-y-4">
    @foreach($responseParams as $param)
    @php
    $value = data_get($result, $param->field_key);
    @endphp

    @if(!is_null($value))
    <div>
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                {{ $param->field_label }}
            </span>
            @if(in_array($param->field_type, ['code', 'string', 'json']))
            <button
                type="button"
                onclick="navigator.clipboard.writeText(this.closest('[data-copyable]').querySelector('[data-value]').textContent).then(() => { this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy', 1500); })"
                class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                Copy
            </button>
            @endif
        </div>

        <div data-copyable>
            @switch($param->field_type)
            @case('string')
            <div class="rounded-lg bg-slate-800/60 border border-white/5 px-4 py-3 text-sm text-slate-100 font-mono max-h-60 overflow-y-auto break-words whitespace-pre-wrap" data-value>{{ is_array($value) ? json_encode($value) : $value }}</div>
            @break

            @case('code')
            <pre class="base64-result-pre" data-value>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $value }}</pre>
            @break

            @case('json')
            <pre class="base64-result-pre" data-value>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $value }}</pre>
            @break

            @case('image_preview')
            @php
            $imgSrc = $value;
            if (!str_starts_with($imgSrc, 'data:')) {
            $imgSrc = 'data:image/png;base64,' . $imgSrc;
            }
            @endphp
            <div class="rounded-lg bg-slate-800/60 border border-white/5 p-3">
                <img src="{{ $imgSrc }}" alt="Preview" class="max-w-full rounded-md max-h-64 mx-auto">
            </div>
            <pre class="base64-result-pre mt-2 max-h-24 overflow-auto" data-value>{{ is_string($value) ? $value : '' }}</pre>
            @break

            @case('download_link')
            <a href="{{ $value }}" download
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 text-sm font-medium hover:bg-emerald-600/30 transition-colors">
                <i data-feather="download" class="w-4 h-4"></i>
                Download file
            </a>
            @break

            @default
            <div class="rounded-lg bg-slate-800/60 border border-white/5 px-4 py-3 text-sm text-slate-100 max-h-60 overflow-y-auto break-words whitespace-pre-wrap" data-value>{{ is_array($value) ? json_encode($value) : $value }}</div>
            @endswitch
        </div>

        @if($param->helper_text)
        <p class="mt-1 text-xs text-slate-500">{{ $param->helper_text }}</p>
        @endif
    </div>
    @endif
    @endforeach
</div>
@elseif($result && $responseParams->isEmpty())
{{-- Fallback: no response params defined, show raw JSON --}}
<pre class="base64-result-pre">{{ is_array($result) ? json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $result }}</pre>
@else
<p class="text-sm text-slate-500">
    {{ $emptyMessage ?? 'Submit the form to see the API response.' }}
</p>
@endif