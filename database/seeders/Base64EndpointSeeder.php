<?php

namespace Database\Seeders;

use App\Models\Base64ApiEndpoint;
use App\Models\Base64ApiParam;
use Illuminate\Database\Seeder;

class Base64EndpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $endpoints = [
            [
                'name' => 'Text to Base64',
                'slug' => 'text-encode',
                'description' => 'Convert plain text into Base64 encoded string.',
                'api_url' => '/text/encode',
                'http_method' => 'POST',
                'icon' => 'type',
                'category' => 'text',
                'sort_order' => 1,
                'request_params' => [
                    ['field_key' => 'text', 'field_label' => 'Input text', 'field_type' => 'textarea', 'placeholder' => 'Halo, ini kalimat untuk di encode!', 'helper_text' => 'Your text will be sent securely to the Base64 API and encoded server-side.', 'is_required' => true, 'sort_order' => 1],
                ],
                'response_params' => [
                    ['field_key' => 'status', 'field_label' => 'Status', 'field_type' => 'string', 'sort_order' => 1],
                    ['field_key' => 'encoded', 'field_label' => 'Encoded Result', 'field_type' => 'code', 'helper_text' => 'The Base64-encoded version of your input text.', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Base64 to Text',
                'slug' => 'text-decode',
                'description' => 'Decode Base64 string back to plain text.',
                'api_url' => '/text/decode',
                'http_method' => 'POST',
                'icon' => 'align-left',
                'category' => 'text',
                'sort_order' => 2,
                'request_params' => [
                    ['field_key' => 'b64_string', 'field_label' => 'Base64 string', 'field_type' => 'textarea', 'placeholder' => 'SGFsbyBSYWZseSwgaW5pIHRlcyBlbmNvZGUh', 'helper_text' => 'The string should be a valid Base64 value; the API will return the decoded text.', 'is_required' => true, 'sort_order' => 1],
                ],
                'response_params' => [
                    ['field_key' => 'status', 'field_label' => 'Status', 'field_type' => 'string', 'sort_order' => 1],
                    ['field_key' => 'decoded', 'field_label' => 'Decoded Text', 'field_type' => 'code', 'helper_text' => 'The plain text decoded from the Base64 string.', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'URL Safe Base64',
                'slug' => 'url-safe',
                'description' => 'Encode or decode Base64 strings for URL usage.',
                'api_url' => '/text/url-safe',
                'http_method' => 'POST',
                'icon' => 'link',
                'category' => 'text',
                'sort_order' => 3,
                'request_params' => [
                    ['field_key' => 'text', 'field_label' => 'Input text', 'field_type' => 'textarea', 'placeholder' => 'Subject? = coding & fun!', 'helper_text' => 'The API will encode this text using its URL-safe Base64 strategy.', 'is_required' => true, 'sort_order' => 1],
                    ['field_key' => 'action', 'field_label' => 'Action', 'field_type' => 'select', 'is_required' => true, 'default_value' => 'encode', 'options' => ['encode' => 'Encode', 'decode' => 'Decode'], 'sort_order' => 2],
                ],
                'response_params' => [
                    ['field_key' => 'status', 'field_label' => 'Status', 'field_type' => 'string', 'sort_order' => 1],
                    ['field_key' => 'result', 'field_label' => 'Result', 'field_type' => 'code', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Image to Base64',
                'slug' => 'image-encode',
                'description' => 'Convert image files to Base64 string.',
                'api_url' => '/image/encode',
                'http_method' => 'POST',
                'icon' => 'image',
                'category' => 'image',
                'sort_order' => 4,
                'request_params' => [
                    ['field_key' => 'file', 'field_label' => 'Choose image', 'field_type' => 'file', 'helper_text' => 'Upload an image file (PNG, JPG, GIF, etc.) to convert to Base64.', 'is_required' => true, 'sort_order' => 1],
                ],
                'response_params' => [
                    ['field_key' => 'status', 'field_label' => 'Status', 'field_type' => 'string', 'sort_order' => 1],
                    ['field_key' => 'filename', 'field_label' => 'Filename', 'field_type' => 'string', 'sort_order' => 2],
                    ['field_key' => 'mime_type', 'field_label' => 'MIME Type', 'field_type' => 'string', 'sort_order' => 3],
                    ['field_key' => 'size', 'field_label' => 'Size', 'field_type' => 'string', 'sort_order' => 4],
                    ['field_key' => 'base64', 'field_label' => 'Base64 Data', 'field_type' => 'code', 'sort_order' => 5],
                ],
            ],
            [
                'name' => 'Base64 to Image',
                'slug' => 'image-decode',
                'description' => 'Convert Base64 string back to image file.',
                'api_url' => '/image/decode',
                'http_method' => 'POST',
                'icon' => 'image',
                'category' => 'image',
                'sort_order' => 5,
                'request_params' => [
                    ['field_key' => 'b64_string', 'field_label' => 'Base64 string', 'field_type' => 'textarea', 'placeholder' => 'Paste the Base64 content of an image', 'is_required' => true, 'sort_order' => 1],
                    ['field_key' => 'filename', 'field_label' => 'Output filename', 'field_type' => 'text', 'placeholder' => 'example.png', 'helper_text' => 'This filename will be suggested when the browser downloads the decoded image.', 'is_required' => true, 'sort_order' => 2],
                ],
                'response_params' => [],
            ],
            [
                'name' => 'File to Base64',
                'slug' => 'file-encode',
                'description' => 'Convert any file to Base64 string.',
                'api_url' => '/file/encode',
                'http_method' => 'POST',
                'icon' => 'file',
                'category' => 'file',
                'sort_order' => 6,
                'request_params' => [
                    ['field_key' => 'file', 'field_label' => 'Choose file', 'field_type' => 'file', 'helper_text' => 'The file is streamed to the Base64 API over HTTPS; large files may take longer to process.', 'is_required' => true, 'sort_order' => 1],
                ],
                'response_params' => [
                    ['field_key' => 'status', 'field_label' => 'Status', 'field_type' => 'string', 'sort_order' => 1],
                    ['field_key' => 'filename', 'field_label' => 'Filename', 'field_type' => 'string', 'sort_order' => 2],
                    ['field_key' => 'mime_type', 'field_label' => 'MIME Type', 'field_type' => 'string', 'sort_order' => 3],
                    ['field_key' => 'size', 'field_label' => 'File Size', 'field_type' => 'string', 'sort_order' => 4],
                    ['field_key' => 'base64', 'field_label' => 'Base64 Data', 'field_type' => 'code', 'sort_order' => 5],
                ],
            ],
            [
                'name' => 'Base64 to File',
                'slug' => 'file-decode',
                'description' => 'Convert Base64 string back to original file.',
                'api_url' => '/file/decode',
                'http_method' => 'POST',
                'icon' => 'file',
                'category' => 'file',
                'sort_order' => 7,
                'request_params' => [
                    ['field_key' => 'b64_string', 'field_label' => 'Base64 string', 'field_type' => 'textarea', 'placeholder' => 'Paste the Base64 content you want to decode', 'is_required' => true, 'sort_order' => 1],
                    ['field_key' => 'filename', 'field_label' => 'Output filename', 'field_type' => 'text', 'placeholder' => 'example.png', 'helper_text' => 'This filename will be suggested when the browser downloads the decoded file.', 'is_required' => true, 'sort_order' => 2],
                ],
                'response_params' => [],
            ],
            [
                'name' => 'File Snippet',
                'slug' => 'file-snippet',
                'description' => 'Get a ready-to-use HTML snippet with Base64 data URI.',
                'api_url' => '/file/snippet',
                'http_method' => 'POST',
                'icon' => 'scissors',
                'category' => 'file',
                'sort_order' => 8,
                'request_params' => [
                    ['field_key' => 'file', 'field_label' => 'Choose file', 'field_type' => 'file', 'helper_text' => 'Ideal for inlining images into HTML emails or small UI elements.', 'is_required' => true, 'sort_order' => 1],
                ],
                'response_params' => [
                    ['field_key' => 'status', 'field_label' => 'Status', 'field_type' => 'string', 'sort_order' => 1],
                    ['field_key' => 'snippet', 'field_label' => 'HTML Snippet', 'field_type' => 'code', 'helper_text' => 'Copy this snippet and paste it directly into your HTML.', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Bulk CSV to Zip',
                'slug' => 'bulk-csv-to-zip',
                'description' => 'Convert CSV of Base64 strings to a Zip of images.',
                'api_url' => '/bulk/csv-to-zip',
                'http_method' => 'POST',
                'icon' => 'archive',
                'category' => 'utility',
                'sort_order' => 9,
                'request_params' => [
                    ['field_key' => 'file', 'field_label' => 'CSV file', 'field_type' => 'file', 'helper_text' => 'Recommended format: id,image where image is a Base64 string.', 'is_required' => true, 'sort_order' => 1],
                ],
                'response_params' => [],
            ],
            [
                'name' => 'System Health Check',
                'slug' => 'health-check',
                'description' => 'Check the status of the Base64 API service.',
                'api_url' => '/health',
                'http_method' => 'GET',
                'icon' => 'activity',
                'category' => 'utility',
                'sort_order' => 10,
                'request_params' => [],
                'response_params' => [
                    ['field_key' => 'status', 'field_label' => 'Status Code', 'field_type' => 'string', 'sort_order' => 1],
                    ['field_key' => 'body', 'field_label' => 'Response Body', 'field_type' => 'json', 'sort_order' => 2],
                ],
            ],
        ];

        foreach ($endpoints as $endpointData) {
            $requestParams = $endpointData['request_params'] ?? [];
            $responseParams = $endpointData['response_params'] ?? [];
            unset($endpointData['request_params'], $endpointData['response_params']);

            $endpoint = Base64ApiEndpoint::updateOrCreate(
                ['slug' => $endpointData['slug']],
                array_merge($endpointData, ['is_active' => true])
            );

            // Seed request params
            foreach ($requestParams as $param) {
                Base64ApiParam::updateOrCreate(
                    [
                        'endpoint_id' => $endpoint->id,
                        'direction' => 'request',
                        'field_key' => $param['field_key'],
                    ],
                    array_merge($param, [
                        'endpoint_id' => $endpoint->id,
                        'direction' => 'request',
                    ])
                );
            }

            // Seed response params
            foreach ($responseParams as $param) {
                Base64ApiParam::updateOrCreate(
                    [
                        'endpoint_id' => $endpoint->id,
                        'direction' => 'response',
                        'field_key' => $param['field_key'],
                    ],
                    array_merge($param, [
                        'endpoint_id' => $endpoint->id,
                        'direction' => 'response',
                    ])
                );
            }
        }
    }
}
