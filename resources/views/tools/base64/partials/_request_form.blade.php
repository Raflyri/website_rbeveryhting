{{-- Generic request form partial - renders fields from $endpoint->requestParams --}}
@php
$requestParams = $endpoint->requestParams()->get();
$hasFileInput = $requestParams->contains('field_type', 'file');
@endphp

<form method="POST"
    action="{{ route('tools.base64.handle', $endpoint->slug) }}"
    @if($hasFileInput) enctype="multipart/form-data" @endif
    class="space-y-4">
    @csrf

    @foreach($requestParams as $param)
    <div>
        @if($param->field_type !== 'hidden')
        <label for="{{ $param->field_key }}" class="block text-sm font-medium text-slate-200 mb-1">
            {{ $param->field_label }}
            @unless($param->is_required)
            <span class="text-slate-500 font-normal">(optional)</span>
            @endunless
        </label>
        @endif

        @switch($param->field_type)
        @case('textarea')
        <textarea
            id="{{ $param->field_key }}"
            name="{{ $param->field_key }}"
            rows="5"
            class="base64-input"
            @if($param->placeholder) placeholder="{{ $param->placeholder }}" @endif
                        @if($param->is_required) required @endif
                    >{{ old($param->field_key, $oldInput[$param->field_key] ?? $param->default_value ?? '') }}</textarea>
        @break

        @case('text')
        <input
            type="text"
            id="{{ $param->field_key }}"
            name="{{ $param->field_key }}"
            class="base64-input"
            @if($param->placeholder) placeholder="{{ $param->placeholder }}" @endif
        @if($param->is_required) required @endif
        value="{{ old($param->field_key, $oldInput[$param->field_key] ?? $param->default_value ?? '') }}"
        >
        @break

        @case('file')
        <input
            type="file"
            id="{{ $param->field_key }}"
            name="{{ $param->field_key }}"
            class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-blue-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-blue-500"
            @if($param->is_required) required @endif
        >
        @break

        @case('select')
        <select
            id="{{ $param->field_key }}"
            name="{{ $param->field_key }}"
            class="base64-input"
            @if($param->is_required) required @endif
            >
            @if($param->options && is_array($param->options))
            @foreach($param->options as $value => $label)
            <option
                value="{{ $value }}"
                @selected(old($param->field_key, $oldInput[$param->field_key] ?? $param->default_value) === (string) $value)
                >{{ $label }}</option>
            @endforeach
            @endif
        </select>
        @break

        @case('hidden')
        <input
            type="hidden"
            id="{{ $param->field_key }}"
            name="{{ $param->field_key }}"
            value="{{ old($param->field_key, $oldInput[$param->field_key] ?? $param->default_value ?? '') }}">
        @break
        @endswitch

        @if($param->helper_text && $param->field_type !== 'hidden')
        <p class="mt-1 text-xs text-slate-500">{{ $param->helper_text }}</p>
        @endif
    </div>
    @endforeach

    <button type="submit"
        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
        <i data-feather="{{ $endpoint->icon ?: 'play' }}" class="w-4 h-4 mr-2"></i>
        {{ $submitLabel ?? 'Submit' }}
    </button>
</form>