<?php

namespace App\Http\Controllers;

use App\Models\Base64ApiEndpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Base64ToolController extends Controller
{
    public function show(string $slug)
    {
        // Fix for legacy link/bookmark
        if ($slug === 'text-basic') {
            return redirect()->route('tools.base64.show', 'text-encode');
        }

        $endpoint = Base64ApiEndpoint::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $view = "tools.base64.$slug";

        if (! view()->exists($view)) {
            abort(404);
        }

        return view($view, [
            'endpoint' => $endpoint,
            'result' => null,
            'error' => null,
            'oldInput' => [],
        ]);
    }

    public function handle(Request $request, string $slug)
    {
        $endpoint = Base64ApiEndpoint::active()
            ->where('slug', $slug)
            ->firstOrFail();

        // Binary-download endpoints still need custom handling
        return match ($slug) {
            'file-decode', 'image-decode' => $this->handleBinaryDownload($request, $endpoint, $slug),
            'bulk-csv-to-zip' => $this->handleBulkCsvToZip($request, $endpoint),
            'health-check' => $this->handleHealthCheck($endpoint),
            default => $this->handleGeneric($request, $endpoint, $slug),
        };
    }

    /**
     * SPA: Return the tool-panel HTML for a given slug (used by fetch() navigation).
     */
    public function panel(string $slug)
    {
        $endpoint = Base64ApiEndpoint::active()
            ->with(['params'])
            ->where('slug', $slug)
            ->first();

        if (! $endpoint) {
            return response()->json(['error' => 'Tool not found.'], 404);
        }

        $html = view('tools.base64.partials._spa_panel', [
            'endpoint' => $endpoint,
            'result'   => null,
            'error'    => null,
            'oldInput' => [],
        ])->render();

        return response()->json([
            'html'        => $html,
            'title'       => $endpoint->name . ' - Base64 Tools - RBeverything',
            'description' => $endpoint->description ?? '',
        ]);
    }

    /**
     * SPA: Process an API form submission and return JSON (or a binary download for file endpoints).
     */
    public function apiHandle(Request $request, string $slug)
    {
        $startTime = microtime(true);

        $endpoint = Base64ApiEndpoint::active()
            ->where('slug', $slug)
            ->firstOrFail();

        Log::channel('stack')->info('[Base64 SPA] API call started', [
            'slug'   => $slug,
            'name'   => $endpoint->name,
            'url'    => $endpoint->api_url,
            'ip'     => $request->ip(),
            'method' => $request->method(),
        ]);

        // Binary download endpoints — stream the file back
        if (in_array($slug, ['file-decode', 'image-decode'])) {
            return $this->handleBinaryDownload($request, $endpoint, $slug);
        }

        if ($slug === 'bulk-csv-to-zip') {
            return $this->handleBulkCsvToZip($request, $endpoint);
        }

        // All other slugs: generic JSON handler
        $requestParams = $endpoint->requestParams()->get();

        // Build validation rules from DB params
        $rules = [];
        foreach ($requestParams as $param) {
            $paramRules   = [];
            $paramRules[] = $param->is_required ? 'required' : 'nullable';
            $paramRules[] = $param->field_type === 'file' ? 'file' : 'string';
            $rules[$param->field_key] = $paramRules;
        }

        $validated = $request->validate($rules);

        Log::channel('stack')->info('[Base64 SPA] Validation passed', [
            'slug'   => $slug,
            'fields' => array_keys($validated),
        ]);

        $apiUrl = $this->buildUrl($endpoint);

        try {
            $hasFile = $requestParams->contains('field_type', 'file');

            if ($hasFile) {
                $fileParam   = $requestParams->firstWhere('field_type', 'file');
                $file        = $validated[$fileParam->field_key];
                $httpRequest = Http::attach(
                    $fileParam->field_key,
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
                foreach ($requestParams as $param) {
                    if ($param->field_type !== 'file' && isset($validated[$param->field_key])) {
                        $httpRequest = $httpRequest->attach($param->field_key, $validated[$param->field_key]);
                    }
                }
                $response = $slug === 'health-check'
                    ? Http::timeout(10)->get($apiUrl)
                    : $httpRequest->post($apiUrl);
            } else {
                $payload = [];
                foreach ($requestParams as $param) {
                    if (isset($validated[$param->field_key])) {
                        $payload[$param->field_key] = $validated[$param->field_key];
                    }
                }
                $response = $slug === 'health-check'
                    ? Http::timeout(10)->get($apiUrl)
                    : Http::asMultipart()->post($apiUrl, $payload);
            }
        } catch (\Throwable $e) {
            $elapsed = round((microtime(true) - $startTime) * 1000);
            Log::channel('stack')->error('[Base64 SPA] External API exception', [
                'slug'       => $slug,
                'exception'  => $e->getMessage(),
                'elapsed_ms' => $elapsed,
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $elapsed = round((microtime(true) - $startTime) * 1000);

        if (! $response->successful()) {
            Log::channel('stack')->warning('[Base64 SPA] External API returned non-2xx', [
                'slug'        => $slug,
                'http_status' => $response->status(),
                'body_excerpt' => substr($response->body(), 0, 300),
                'elapsed_ms'  => $elapsed,
            ]);
            return response()->json(
                ['error' => "External API returned HTTP {$response->status()}."],
                $response->status()
            );
        }

        Log::channel('stack')->info('[Base64 SPA] Success', [
            'slug'        => $slug,
            'http_status' => $response->status(),
            'elapsed_ms'  => $elapsed,
        ]);

        // Render the response panel HTML so the SPA can inject it
        $result = $response->json() ?? $response->body();
        $html   = view('tools.base64.partials._response_display', [
            'endpoint'     => $endpoint,
            'result'       => $result,
            'error'        => null,
            'emptyMessage' => '',
        ])->render();

        return response()->json([
            'result' => $result,
            'html'   => $html,
        ]);
    }

    /**
     * Generic handler — builds validation & API request from DB params.
     */
    protected function handleGeneric(Request $request, Base64ApiEndpoint $endpoint, string $slug)
    {
        $requestParams = $endpoint->requestParams()->get();
        $view = "tools.base64.$slug";

        // Build validation rules from DB params
        $rules = [];
        foreach ($requestParams as $param) {
            $paramRules = [];
            if ($param->is_required) {
                $paramRules[] = 'required';
            } else {
                $paramRules[] = 'nullable';
            }

            if ($param->field_type === 'file') {
                $paramRules[] = 'file';
            } else {
                $paramRules[] = 'string';
            }

            $rules[$param->field_key] = $paramRules;
        }

        $data = $request->validate($rules);
        $url = $this->buildUrl($endpoint);

        try {
            // Check if any params are file uploads
            $hasFile = $requestParams->contains('field_type', 'file');

            if ($hasFile) {
                // Find the file param and attach it
                $fileParam = $requestParams->firstWhere('field_type', 'file');
                $file = $data[$fileParam->field_key];

                $httpRequest = Http::attach(
                    $fileParam->field_key,
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );

                // Add non-file params as multipart
                foreach ($requestParams as $param) {
                    if ($param->field_type !== 'file' && isset($data[$param->field_key])) {
                        $httpRequest = $httpRequest->attach($param->field_key, $data[$param->field_key]);
                    }
                }

                $response = $httpRequest->post($url);
            } else {
                // All text params — send as multipart
                $payload = [];
                foreach ($requestParams as $param) {
                    if (isset($data[$param->field_key])) {
                        $payload[$param->field_key] = $data[$param->field_key];
                    }
                }

                $response = Http::asMultipart()->post($url, $payload);
            }
        } catch (\Throwable $e) {
            return $this->renderResult($view, $endpoint, null, $e->getMessage(), $data);
        }

        if (! $response->successful()) {
            return $this->renderResult(
                $view,
                $endpoint,
                null,
                "Request failed with status {$response->status()}.",
                $data
            );
        }

        return $this->renderResult($view, $endpoint, $response->json() ?? $response->body(), null, $data);
    }

    protected function buildUrl(Base64ApiEndpoint $endpoint): string
    {
        $baseUrl = rtrim(config('services.base64_api.base_url'), '/');
        $path = ltrim($endpoint->api_url ?? '', '/');

        abort_if(empty($path), 500, 'API path is not configured for this tool.');

        return $baseUrl . '/' . $path;
    }

    protected function handleHealthCheck(Base64ApiEndpoint $endpoint)
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::timeout(10)->get($url);
        } catch (\Throwable $e) {
            return $this->renderResult('tools.base64.health-check', $endpoint, null, $e->getMessage());
        }

        if (! $response->successful()) {
            return $this->renderResult(
                'tools.base64.health-check',
                $endpoint,
                null,
                "Health check failed with status {$response->status()}."
            );
        }

        return $this->renderResult('tools.base64.health-check', $endpoint, [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ]);
    }

    protected function handleBinaryDownload(Request $request, Base64ApiEndpoint $endpoint, string $slug)
    {
        $requestParams = $endpoint->requestParams()->get();
        $view = "tools.base64.$slug";

        // Build validation from DB params
        $rules = [];
        foreach ($requestParams as $param) {
            $paramRules = [];
            $paramRules[] = $param->is_required ? 'required' : 'nullable';
            $paramRules[] = $param->field_type === 'file' ? 'file' : 'string';
            $rules[$param->field_key] = $paramRules;
        }

        $data = $request->validate($rules);
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::asMultipart()->post($url, $data);
        } catch (\Throwable $e) {
            return $this->renderResult($view, $endpoint, null, $e->getMessage(), $data);
        }

        if (! $response->successful()) {
            return $this->renderResult(
                $view,
                $endpoint,
                null,
                "Request failed with status {$response->status()}.",
                $data
            );
        }

        $filename = $data['filename'] ?? 'download.bin';
        $contentType = $response->header('Content-Type', 'application/octet-stream');
        $content = $response->body();

        return new StreamedResponse(function () use ($content) {
            echo $content;
        }, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function handleBulkCsvToZip(Request $request, Base64ApiEndpoint $endpoint)
    {
        $requestParams = $endpoint->requestParams()->get();

        // Build validation from DB params
        $rules = [];
        foreach ($requestParams as $param) {
            $paramRules = [];
            $paramRules[] = $param->is_required ? 'required' : 'nullable';
            if ($param->field_type === 'file') {
                $paramRules[] = 'file';
                $paramRules[] = 'mimetypes:text/plain,text/csv,text/tsv,text/*';
                $paramRules[] = 'max:20480';
            } else {
                $paramRules[] = 'string';
            }
            $rules[$param->field_key] = $paramRules;
        }

        $data = $request->validate($rules);
        $file = $data['file'];
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post($url);
        } catch (\Throwable $e) {
            return $this->renderResult('tools.base64.bulk-csv-to-zip', $endpoint, null, $e->getMessage());
        }

        if (! $response->successful()) {
            return $this->renderResult(
                'tools.base64.bulk-csv-to-zip',
                $endpoint,
                null,
                "Request failed with status {$response->status()}."
            );
        }

        $filename = 'converted-images.zip';
        $contentType = $response->header('Content-Type', 'application/zip');
        $content = $response->body();

        return new StreamedResponse(function () use ($content) {
            echo $content;
        }, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function renderResult(string $view, Base64ApiEndpoint $endpoint, $result = null, ?string $error = null, array $oldInput = [])
    {
        return view($view, [
            'endpoint' => $endpoint,
            'result' => $result,
            'error' => $error,
            'oldInput' => $oldInput,
        ]);
    }
}
