<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Base64ApiEndpointResource\Pages;
use App\Models\Base64ApiEndpoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class Base64ApiEndpointResource extends Resource
{
    protected static ?string $model = Base64ApiEndpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationGroup = 'Tools & Utilities';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Row 1: Endpoint Details + API Config ──
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Endpoint Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\Select::make('category')
                                    ->options([
                                        'text' => 'Text',
                                        'image' => 'Image',
                                        'file' => 'File',
                                        'utility' => 'Utility',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('icon')
                                    ->label('Icon Name (Feather Icons)')
                                    ->placeholder('e.g., activity, cpu, database')
                                    ->maxLength(50),

                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('API Configuration')
                            ->schema([
                                Forms\Components\TextInput::make('api_url')
                                    ->label('API Endpoint URL')
                                    ->placeholder('/api/v1/...')
                                    ->helperText('Relative or absolute URL for the API endpoint.'),

                                Forms\Components\Select::make('http_method')
                                    ->options([
                                        'GET' => 'GET',
                                        'POST' => 'POST',
                                        'PUT' => 'PUT',
                                        'DELETE' => 'DELETE',
                                    ])
                                    ->default('POST')
                                    ->required(),

                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Toggle to show/hide this endpoint on the frontend.'),
                            ]),
                    ])->columnSpan(['lg' => 1]),

                // ── Row 2: Request Parameters ──
                Forms\Components\Section::make('Request Parameters')
                    ->description('Define the form fields users fill in before submitting. Changes here update the frontend form instantly.')
                    ->schema([
                        Forms\Components\Repeater::make('requestParams')
                            ->relationship('requestParams')
                            ->label('')
                            ->schema([
                                Forms\Components\Hidden::make('direction')->default('request'),

                                Forms\Components\TextInput::make('field_key')
                                    ->label('Key')
                                    ->placeholder('e.g. text, b64_string, file')
                                    ->required()
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('field_label')
                                    ->label('Label')
                                    ->placeholder('e.g. Input text')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('field_type')
                                    ->label('Input Type')
                                    ->options([
                                        'text' => 'Text (single line)',
                                        'textarea' => 'Textarea (multi-line)',
                                        'file' => 'File Upload',
                                        'hidden' => 'Hidden',
                                        'select' => 'Select / Dropdown',
                                    ])
                                    ->default('text')
                                    ->required()
                                    ->live(),

                                Forms\Components\TextInput::make('placeholder')
                                    ->maxLength(255)
                                    ->visible(fn(Forms\Get $get) => in_array($get('field_type'), ['text', 'textarea'])),

                                Forms\Components\TextInput::make('helper_text')
                                    ->label('Helper Text')
                                    ->maxLength(500),

                                Forms\Components\Toggle::make('is_required')
                                    ->label('Required')
                                    ->default(true),

                                Forms\Components\TextInput::make('default_value')
                                    ->label('Default / Example Value')
                                    ->maxLength(500)
                                    ->visible(fn(Forms\Get $get) => $get('field_type') !== 'file'),

                                Forms\Components\KeyValue::make('options')
                                    ->label('Dropdown Options (value → label)')
                                    ->visible(fn(Forms\Get $get) => $get('field_type') === 'select')
                                    ->helperText('e.g. encode → Encode, decode → Decode'),

                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Order'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => ($state['field_key'] ?? '') . ' (' . ($state['field_type'] ?? 'text') . ')')
                            ->mutateRelationshipDataBeforeCreateUsing(fn(array $data): array => array_merge($data, ['direction' => 'request']))
                            ->mutateRelationshipDataBeforeSaveUsing(fn(array $data): array => array_merge($data, ['direction' => 'request'])),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),

                // ── Row 3: Response Fields ──
                Forms\Components\Section::make('Response Fields')
                    ->description('Define how the API response is displayed. Each key maps to a field in the JSON response.')
                    ->schema([
                        Forms\Components\Repeater::make('responseParams')
                            ->relationship('responseParams')
                            ->label('')
                            ->schema([
                                Forms\Components\Hidden::make('direction')->default('response'),

                                Forms\Components\TextInput::make('field_key')
                                    ->label('JSON Key')
                                    ->placeholder('e.g. encoded, status, base64')
                                    ->required()
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('field_label')
                                    ->label('Display Label')
                                    ->placeholder('e.g. Encoded Result')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('field_type')
                                    ->label('Render Type')
                                    ->options([
                                        'string' => 'Plain Text',
                                        'json' => 'JSON Block',
                                        'code' => 'Code Block (with copy)',
                                        'image_preview' => 'Image Preview (base64)',
                                        'download_link' => 'Download Link',
                                    ])
                                    ->default('string')
                                    ->required(),

                                Forms\Components\TextInput::make('helper_text')
                                    ->label('Helper Text')
                                    ->maxLength(500),

                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Order'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => ($state['field_key'] ?? '') . ' → ' . ($state['field_type'] ?? 'string'))
                            ->mutateRelationshipDataBeforeCreateUsing(fn(array $data): array => array_merge($data, ['direction' => 'response']))
                            ->mutateRelationshipDataBeforeSaveUsing(fn(array $data): array => array_merge($data, ['direction' => 'response'])),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Base64ApiEndpoint $record) => $record->slug),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'text' => 'info',
                        'image' => 'success',
                        'file' => 'warning',
                        'utility' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('api_url')
                    ->label('API URL')
                    ->limit(30)
                    ->color(fn($state) => empty($state) ? 'danger' : 'success')
                    ->tooltip(fn(Base64ApiEndpoint $record) => $record->api_url ?? 'Not Configured'),

                Tables\Columns\TextColumn::make('params_count')
                    ->label('Params')
                    ->counts('params')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextInputColumn::make('sort_order')
                    ->sortable()
                    ->rules(['numeric', 'min:0']),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'text' => 'Text',
                        'image' => 'Image',
                        'file' => 'File',
                        'utility' => 'Utility',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBase64ApiEndpoints::route('/'),
            'create' => Pages\CreateBase64ApiEndpoint::route('/create'),
            'edit' => Pages\EditBase64ApiEndpoint::route('/{record}/edit'),
        ];
    }
}
