<?php

namespace App\Filament\Resources\Base64ApiEndpointResource\Pages;

use App\Filament\Resources\Base64ApiEndpointResource;
use App\Models\Base64ApiEndpoint;
use App\Models\Base64ApiParam;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListBase64ApiEndpoints extends ListRecords
{
    protected static string $resource = Base64ApiEndpointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([

                // ── Option 1: Add Manually ──
                Actions\Action::make('addManually')
                    ->label('Add Manually')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->url(Base64ApiEndpointResource::getUrl('create')),

                // ── Option 2: Import JSON ──
                Actions\Action::make('importJson')
                    ->label('Import JSON')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->modalHeading('Import API Collection')
                    ->modalDescription('Paste a Postman Collection JSON or upload a .json file. All endpoints in the collection will be created automatically.')
                    ->modalSubmitActionLabel('Import')
                    ->modalWidth('xl')
                    ->form([
                        Forms\Components\Tabs::make('importMethod')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Paste JSON')
                                    ->icon('heroicon-o-clipboard-document')
                                    ->schema([
                                        Forms\Components\Textarea::make('json_text')
                                            ->label('Collection JSON')
                                            ->placeholder('{ "info": { ... }, "item": [ ... ] }')
                                            ->rows(14)
                                            ->helperText('Paste the full Postman Collection v2.1 JSON here.'),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Upload File')
                                    ->icon('heroicon-o-arrow-up-tray')
                                    ->schema([
                                        Forms\Components\FileUpload::make('json_file')
                                            ->label('JSON File')
                                            ->acceptedFileTypes(['application/json'])
                                            ->maxSize(2048)
                                            ->helperText('Upload a .json Postman Collection file (max 2 MB).'),
                                    ]),
                            ]),
                    ])
                    ->action(function (array $data) {
                        $json = null;

                        // Prefer pasted text, fall back to uploaded file
                        if (! empty($data['json_text'])) {
                            $json = $data['json_text'];
                        } elseif (! empty($data['json_file'])) {
                            $filePath = storage_path('app/public/' . $data['json_file']);
                            if (file_exists($filePath)) {
                                $json = file_get_contents($filePath);
                                @unlink($filePath); // clean up
                            }
                        }

                        if (empty($json)) {
                            Notification::make()
                                ->title('No JSON provided')
                                ->body('Please paste JSON or upload a file.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $collection = json_decode($json, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            Notification::make()
                                ->title('Invalid JSON')
                                ->body('Could not parse the JSON: ' . json_last_error_msg())
                                ->danger()
                                ->send();
                            return;
                        }

                        $items = $collection['item'] ?? [];

                        if (empty($items)) {
                            Notification::make()
                                ->title('No endpoints found')
                                ->body('The JSON does not contain an "item" array with API endpoints.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $created = 0;
                        $skipped = 0;
                        $maxSort = Base64ApiEndpoint::max('sort_order') ?? 0;

                        foreach ($items as $item) {
                            $request = $item['request'] ?? [];
                            $name = $item['name'] ?? 'Untitled Endpoint';
                            $method = strtoupper($request['method'] ?? 'POST');

                            // Build path from URL parts
                            $url = $request['url'] ?? [];
                            $pathParts = $url['path'] ?? [];
                            $apiPath = '/' . implode('/', $pathParts);

                            // Generate slug from name
                            $slug = Str::slug($name);

                            // Skip if slug already exists
                            if (Base64ApiEndpoint::where('slug', $slug)->exists()) {
                                $skipped++;
                                continue;
                            }

                            // Guess category from path
                            $category = 'utility';
                            if (str_contains($apiPath, '/text')) {
                                $category = 'text';
                            } elseif (str_contains($apiPath, '/image')) {
                                $category = 'image';
                            } elseif (str_contains($apiPath, '/file')) {
                                $category = 'file';
                            }

                            $maxSort++;

                            $endpoint = Base64ApiEndpoint::create([
                                'name' => preg_replace('/^\d+\.\s*/', '', $name), // strip leading "1. "
                                'slug' => $slug,
                                'description' => $item['description'] ?? ($request['description'] ?? null),
                                'api_url' => $apiPath,
                                'http_method' => $method,
                                'icon' => null,
                                'category' => $category,
                                'is_active' => true,
                                'sort_order' => $maxSort,
                            ]);

                            // Create request params from formdata body
                            $body = $request['body'] ?? [];
                            $formData = $body['formdata'] ?? [];
                            $sortOrder = 0;

                            foreach ($formData as $field) {
                                $sortOrder++;
                                $fieldKey = $field['key'] ?? 'field_' . $sortOrder;
                                $fieldType = ($field['type'] ?? 'text') === 'file' ? 'file' : 'textarea';

                                // Use 'text' input for short fields
                                if ($fieldType !== 'file' && strlen($field['value'] ?? '') < 60) {
                                    $fieldType = 'text';
                                }

                                Base64ApiParam::create([
                                    'endpoint_id' => $endpoint->id,
                                    'direction' => 'request',
                                    'field_key' => $fieldKey,
                                    'field_label' => Str::headline($fieldKey),
                                    'field_type' => $fieldType,
                                    'placeholder' => $field['value'] ?? null,
                                    'is_required' => true,
                                    'sort_order' => $sortOrder,
                                ]);
                            }

                            // Add a default 'status' response param
                            Base64ApiParam::create([
                                'endpoint_id' => $endpoint->id,
                                'direction' => 'response',
                                'field_key' => 'status',
                                'field_label' => 'Status',
                                'field_type' => 'string',
                                'sort_order' => 1,
                            ]);

                            $created++;
                        }

                        Notification::make()
                            ->title('Import complete')
                            ->body("Created {$created} endpoint(s)" . ($skipped > 0 ? ", skipped {$skipped} duplicate(s)" : '') . '.')
                            ->success()
                            ->send();
                    }),
            ])
                ->label('New Endpoint')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->button(),
        ];
    }
}
