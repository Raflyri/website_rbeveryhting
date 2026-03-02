{{--
    _spa_panel.blade.php
    Renders the two-column tool panel (form + response) for ANY endpoint.
    Used by Base64ToolController::panel() — returned as rendered HTML inside a JSON payload.
    Also used directly from the SPA shell for the initial paint.
    Variables: $endpoint, $result, $error, $oldInput
--}}
@php
$requestParams = $endpoint->requestParams()->get();
$hasFileInput = $requestParams->contains('field_type', 'file');
@endphp

<div class="spa-panel" data-slug="{{ $endpoint->slug }}" id="spa-tool-panel">

    {{-- Tool Header --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 shrink-0">
            <i data-feather="{{ $endpoint->icon ?: 'code' }}" class="w-6 h-6"></i>
        </div>
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-bold tracking-widest uppercase mb-1">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                {{ ucfirst($endpoint->category ?? 'tool') }}
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-white">{{ $endpoint->name }}</h2>
            @if(!empty($endpoint->description))
            <p class="text-sm text-slate-400 mt-0.5">{{ $endpoint->description }}</p>
            @endif
        </div>
    </div>

    {{-- Validation errors (shown when no-JS fallback POST fails validation) --}}
    @if(!empty($errors) && $errors->any())
    <div class="mb-5 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200 overflow-x-auto">
        <div class="font-semibold mb-1">{{ __('text.validation_error') }}</div>
        <ul class="list-disc list-inside space-y-0.5 whitespace-nowrap">
            @foreach($errors->all() as $errorMessage)
            <li>{{ $errorMessage }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- API error --}}
    @if(!empty($error))
    <div class="mb-5 rounded-xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-100 spa-error-banner overflow-x-auto">
        <div class="font-semibold mb-1">{{ __('text.api_error') }}</div>
        <p class="whitespace-nowrap">{{ $error }}</p>
    </div>
    @endif

    {{-- Two-column layout --}}
    <div class="grid gap-6 lg:grid-cols-1 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">

        {{-- Left: Request form --}}
        <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6 flex flex-col min-w-0">
            <h3 class="text-base font-semibold text-slate-200 mb-4">{{ __('text.request') }}</h3>

            <form
                method="POST"
                action="{{ route('tools.base64.handle', $endpoint->slug) }}"
                @if($hasFileInput) enctype="multipart/form-data" @endif
                class="space-y-4"
                data-spa-form
                data-spa-slug="{{ $endpoint->slug }}"
                data-spa-has-file="{{ $hasFileInput ? 'true' : 'false' }}">
                @csrf

                @foreach($requestParams as $param)
                <div>
                    @if($param->field_type !== 'hidden')
                    <label for="spa-{{ $param->field_key }}" class="block text-sm font-medium text-slate-200 mb-1">
                        {{ $param->field_label }}
                        @unless($param->is_required)
                        <span class="text-slate-500 font-normal">({{ __('text.optional') }})</span>
                        @endunless
                    </label>
                    @endif

                    @switch($param->field_type)
                    @case('textarea')
                    <textarea
                        id="spa-{{ $param->field_key }}"
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
                        id="spa-{{ $param->field_key }}"
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
                        id="spa-{{ $param->field_key }}"
                        name="{{ $param->field_key }}"
                        class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-blue-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-blue-500"
                        @if($param->is_required) required @endif
                    >
                    @break

                    @case('select')
                    <select
                        id="spa-{{ $param->field_key }}"
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
                        id="spa-{{ $param->field_key }}"
                        name="{{ $param->field_key }}"
                        value="{{ old($param->field_key, $oldInput[$param->field_key] ?? $param->default_value ?? '') }}">
                    @break
                    @endswitch

                    @if($param->helper_text && $param->field_type !== 'hidden')
                    <p class="mt-1 text-xs text-slate-500">{{ $param->helper_text }}</p>
                    @endif
                </div>
                @endforeach

                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 text-sm font-semibold text-white hover:bg-blue-500 active:scale-95 transition-all duration-150 gap-2 w-full sm:w-auto"
                    data-spa-submit-btn>
                    <i data-feather="{{ $endpoint->icon ?: 'play' }}" class="w-4 h-4"></i>
                    <span data-spa-btn-label>{{ __('text.submit') }}</span>
                </button>
            </form>
        </div>

        {{-- Right: Response display --}}
        <div class="glass-card rounded-2xl border border-white/10 bg-slate-900/60 p-6 flex flex-col min-w-0">
            <h3 class="text-base font-semibold text-slate-200 mb-4">{{ __('text.response') }}</h3>
            <div id="spa-response" class="w-full">
                @include('tools.base64.partials._response_display', [
                'emptyMessage' => __('text.empty_response')
                ])
            </div>
        </div>
    </div>
</div>