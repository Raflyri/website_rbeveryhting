<?php

namespace App\Http\Controllers;

use App\Jobs\ResolveIpCountry;
use App\Models\ApiCallLog;
use App\Models\Base64ApiEndpoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * JSON API controller for the base64tools static subdomain.
 *
 * Two responsibilities:
 *  1. index()  — expose the tool catalogue (endpoint + params) as JSON
 *  2. proxy()  — validate input, forward to the external base64 API,
 *                log the call, and return the result as JSON.
 *
 * The external API URL comes from BASE64_API_BASE_URL in .env and is
 * NEVER sent to the browser, keeping it secret from public users.
 */
class Base64ApiController extends Controller
{
    // ─── Tool catalogue ──────────────────────────────────────────────────────

    /**
     * Return all active tools with their field definitions as JSON.
     * Used by the static frontend to build the sidebar + forms dynamically.
     */
    public function index(): JsonResponse
    {
        $tools = Base64ApiEndpoint::active()
            ->with([
                'params' => fn($q) => $q->ordered(),
            ])
            ->ordered()
            ->get()
            ->map(fn($endpoint) => [
                'id'          => $endpoint->id,
                'name'        => $endpoint->name,
                'slug'        => $endpoint->slug,
                'description' => $endpoint->description,
                'icon'        => $endpoint->icon,
                'category'    => $endpoint->category,
                'has_api_url' => ! empty($endpoint->api_url),
                'params'      => $endpoint->params->map(fn($p) => [
                    'direction'    => $p->direction,
                    'field_key'    => $p->field_key,
                    'field_label'  => $p->field_label,
                    'field_type'   => $p->field_type,
                    'placeholder'  => $p->placeholder,
                    'helper_text'  => $p->helper_text,
                    'is_required'  => $p->is_required,
                    'default_value' => $p->default_value,
                    'options'      => $p->options,  // already cast to array
                ]),
            ]);

        return response()->json($tools);
    }

    // ─── Proxy ───────────────────────────────────────────────────────────────

    /**
     * Forward a tool call to the external base64 API and return JSON.
     * Binary-download endpoints stream files back directly.
     */
    public function proxy(Request $request, string $slug): JsonResponse|StreamedResponse
    {
        $startTime = microtime(true);

        $endpoint = Base64ApiEndpoint::active()
            ->where('slug', $slug)
            ->firstOrFail();

        Log::channel('stack')->info('[Base64 API] Proxy call started', [
            'slug'   => $slug,
            'ip'     => $request->ip(),
            'origin' => $request->header('Origin'),
        ]);

        // ── Binary endpoints → stream file back ─────────────────────────────
        if (in_array($slug, ['file-decode', 'image-decode', 'bulk-csv-to-zip'])) {
            return $this->handleBinaryDownload($request, $endpoint, $slug, $startTime);
        }

        // ── Text/JSON endpoints ──────────────────────────────────────────────
        $requestParams = $endpoint->requestParams()->get();

        // Build validation rules from DB param definitions
        $rules = [];
        foreach ($requestParams as $param) {
            $rules[$param->field_key] = [
                $param->is_required ? 'required' : 'nullable',
                $param->field_type === 'file' ? 'file' : 'string',
            ];
        }

        $validated = $request->validate($rules);

        $apiUrl = $this->buildUrl($endpoint);

        try {
            $hasFile = $requestParams->contains('field_type', 'file');
            $response = $hasFile
                ? $this->sendWithFile($requestParams, $validated, $apiUrl)
                : $this->sendJson($requestParams, $validated, $apiUrl);
        } catch (\Throwable $e) {
            $elapsed = $this->elapsed($startTime);
            Log::channel('stack')->error('[Base64 API] External API exception', [
                'slug'      => $slug,
                'exception' => $e->getMessage(),
                'elapsed_ms' => $elapsed,
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $elapsed = round((microtime(true) - $startTime) * 1000);
        $requestSnippet  = substr(json_encode($validated, JSON_UNESCAPED_UNICODE), 0, 500);

        if (! $response->successful()) {
            Log::channel('stack')->warning('[Base64 API] External API non-2xx', [
                'slug'        => $slug,
                'http_status' => $response->status(),
            ]);

            $apiLog = ApiCallLog::create([
                'ip'               => $request->ip(),
                'url'              => $request->fullUrl(),
                'api_endpoint'     => $slug,
                'method'           => $request->method(),
                'http_status'      => $response->status(),
                'duration_ms'      => $elapsed,
                'request_snippet'  => $requestSnippet,
                'response_snippet' => substr($response->body(), 0, 500),
                'level'            => 'warning',
                'message'          => "[Base64 API] External API returned HTTP {$response->status()} for {$slug}",
            ]);
            ResolveIpCountry::dispatch($apiLog->id);

            return response()->json(
                ['error' => "External API returned HTTP {$response->status()}."],
                $response->status()
            );
        }

        $result = $response->json() ?? $response->body();
        $responseSnippet = substr(
            is_string($result) ? $result : json_encode($result, JSON_UNESCAPED_UNICODE),
            0,
            500
        );

        $apiLog = ApiCallLog::create([
            'ip'               => $request->ip(),
            'url'              => $request->fullUrl(),
            'api_endpoint'     => $slug,
            'method'           => $request->method(),
            'http_status'      => $response->status(),
            'duration_ms'      => $elapsed,
            'request_snippet'  => $requestSnippet,
            'response_snippet' => $responseSnippet,
            'level'            => 'info',
            'message'          => "[Base64 API] {$endpoint->name} completed in {$elapsed}ms",
        ]);
        ResolveIpCountry::dispatch($apiLog->id);

        Log::channel('stack')->info('[Base64 API] Success', [
            'slug'        => $slug,
            'http_status' => $response->status(),
            'elapsed_ms'  => $elapsed,
        ]);

        return response()->json(['result' => $result]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    protected function buildUrl(Base64ApiEndpoint $endpoint): string
    {
        $baseUrl = rtrim(config('services.base64_api.base_url'), '/');
        $path    = ltrim($endpoint->api_url ?? '', '/');
        abort_if(empty($path), 500, 'API path is not configured for this tool.');
        return $baseUrl . '/' . $path;
    }

    protected function elapsed(float $startTime): int
    {
        return (int) round((microtime(true) - $startTime) * 1000);
    }

    protected function sendWithFile($requestParams, array $validated, string $apiUrl)
    {
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

        return $httpRequest->post($apiUrl);
    }

    protected function sendJson($requestParams, array $validated, string $apiUrl)
    {
        $payload = [];
        foreach ($requestParams as $param) {
            if (isset($validated[$param->field_key])) {
                $payload[$param->field_key] = $validated[$param->field_key];
            }
        }
        return Http::asMultipart()->post($apiUrl, $payload);
    }

    protected function handleBinaryDownload(
        Request $request,
        Base64ApiEndpoint $endpoint,
        string $slug,
        float $startTime
    ): StreamedResponse|JsonResponse {
        $requestParams = $endpoint->requestParams()->get();

        $rules = [];
        foreach ($requestParams as $param) {
            $paramRules = [$param->is_required ? 'required' : 'nullable'];

            if ($param->field_type === 'file') {
                $paramRules[] = 'file';
                if ($slug === 'bulk-csv-to-zip') {
                    $paramRules[] = 'mimetypes:text/plain,text/csv,text/tsv,text/*';
                    $paramRules[] = 'max:20480';
                }
            } else {
                $paramRules[] = 'string';
            }
            $rules[$param->field_key] = $paramRules;
        }

        $data = $request->validate($rules);
        $url  = $this->buildUrl($endpoint);

        try {
            $hasFile = $requestParams->contains('field_type', 'file');
            $response = $hasFile
                ? $this->sendWithFile($requestParams, $data, $url)
                : Http::asMultipart()->post($url, $data);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        if (! $response->successful()) {
            return response()->json(
                ['error' => "Request failed with status {$response->status()}."],
                $response->status()
            );
        }

        $filename    = $data['filename'] ?? ($slug === 'bulk-csv-to-zip' ? 'converted-images.zip' : 'download.bin');
        $contentType = $response->header('Content-Type', 'application/octet-stream');
        $content     = $response->body();

        return new StreamedResponse(function () use ($content) {
            echo $content;
        }, 200, [
            'Content-Type'              => $contentType,
            'Content-Disposition'       => 'attachment; filename="' . $filename . '"',
            'Access-Control-Allow-Origin' => $request->header('Origin', '*'),
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }
}
