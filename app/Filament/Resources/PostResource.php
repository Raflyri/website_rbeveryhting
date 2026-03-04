<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model          = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Site Content';
    protected static ?int    $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Grid::make(3)->schema([

                /*
                |------------------------------------------------------------------
                | LEFT — Metadata (2/3 width)
                |------------------------------------------------------------------
                */
                Forms\Components\Group::make()->columnSpan(2)->schema([

                    Forms\Components\Section::make('Post Details')->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old ?? '')) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Post::class, 'slug', ignoreRecord: true),

                        Forms\Components\Select::make('type')
                            ->options([
                                'news'    => '📰 News',
                                'article' => '📄 Article',
                                'blog'    => '✍️ Blog',
                            ])
                            ->required()
                            ->default('article'),

                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->hint('Auto-generated from first block if empty')
                            ->columnSpanFull(),

                    ])->columns(2),

                    /*
                    |--------------------------------------------------------------
                    | BLOCK BUILDER
                    |--------------------------------------------------------------
                    */
                    Forms\Components\Section::make('Content Blocks')
                        ->description('Build your article with flexible blocks. Click "Add block" below.')
                        ->schema([
                            Forms\Components\Builder::make('blocks')
                                ->label('')
                                ->blocks([

                                    // ── Text Block ──
                                    Forms\Components\Builder\Block::make('text')
                                        ->label('📝 Text / Heading')
                                        ->icon('heroicon-o-document-text')
                                        ->schema([
                                            Forms\Components\TextInput::make('heading')
                                                ->label('Heading (optional)')
                                                ->placeholder('Section heading…'),
                                            Forms\Components\Select::make('heading_level')
                                                ->label('Heading level')
                                                ->options(['h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4'])
                                                ->default('h2')
                                                ->visible(fn(Get $get) => filled($get('heading'))),
                                            Forms\Components\RichEditor::make('paragraph')
                                                ->label('Paragraph')
                                                ->toolbarButtons([
                                                    'bold',
                                                    'italic',
                                                    'underline',
                                                    'strike',
                                                    'link',
                                                    'bulletList',
                                                    'orderedList',
                                                    'h2',
                                                    'h3',
                                                ])
                                                ->columnSpanFull(),
                                        ]),

                                    // ── Image Block ──
                                    Forms\Components\Builder\Block::make('image')
                                        ->label('🖼 Image')
                                        ->icon('heroicon-o-photo')
                                        ->schema([
                                            Forms\Components\FileUpload::make('src')
                                                ->label('Image file')
                                                ->image()
                                                ->directory('posts/images')
                                                ->required(),
                                            Forms\Components\TextInput::make('caption')
                                                ->label('Caption (optional)'),
                                            Forms\Components\Select::make('alignment')
                                                ->label('Alignment')
                                                ->options(['left' => 'Left', 'center' => 'Center', 'right' => 'Right'])
                                                ->default('center'),
                                        ])->columns(2),

                                    // ── Code Block ──
                                    Forms\Components\Builder\Block::make('code')
                                        ->label('💻 Code Snippet')
                                        ->icon('heroicon-o-code-bracket')
                                        ->schema([
                                            Forms\Components\Select::make('language')
                                                ->label('Language')
                                                ->searchable()
                                                ->options([
                                                    'php'        => 'PHP',
                                                    'javascript' => 'JavaScript',
                                                    'typescript' => 'TypeScript',
                                                    'python'     => 'Python',
                                                    'bash'       => 'Bash / Shell',
                                                    'sql'        => 'SQL',
                                                    'json'       => 'JSON',
                                                    'html'       => 'HTML',
                                                    'css'        => 'CSS',
                                                    'yaml'       => 'YAML',
                                                    'plaintext'  => 'Plain Text',
                                                ])
                                                ->default('php'),
                                            Forms\Components\Textarea::make('code')
                                                ->label('Code')
                                                ->rows(12)
                                                ->required()
                                                ->columnSpanFull()
                                                ->extraAttributes(['style' => 'font-family:monospace']),
                                        ])->columns(2),

                                    // ── Quote Block ──
                                    Forms\Components\Builder\Block::make('quote')
                                        ->label('💬 Quote / Callout')
                                        ->icon('heroicon-o-chat-bubble-left-right')
                                        ->schema([
                                            Forms\Components\Textarea::make('text')
                                                ->label('Quote text')
                                                ->required()
                                                ->rows(3)
                                                ->columnSpanFull(),
                                            Forms\Components\TextInput::make('attribution')
                                                ->label('Attribution (optional)')
                                                ->placeholder('— Author Name'),
                                        ]),
                                ])
                                ->collapsible()
                                ->reorderableWithButtons()
                                ->addActionLabel('+ Add Block')
                                ->columnSpanFull(),
                        ]),

                ]),

                /*
                |------------------------------------------------------------------
                | RIGHT — Publishing sidebar (1/3 width)
                |------------------------------------------------------------------
                */
                Forms\Components\Group::make()->columnSpan(1)->schema([

                    Forms\Components\Section::make('⏱ Time-Travel Publishing')
                        ->description('Control exactly when this post appears publicly.')
                        ->schema([

                            Forms\Components\Placeholder::make('status_hint')
                                ->label('Current Status')
                                ->content(
                                    fn($record) => $record
                                        ? match ($record->computedStatus()) {
                                            'draft'     => '✏️  Draft — visible only to you.',
                                            'scheduled' => '⏰  Scheduled — will go live on ' . $record->published_at?->format('d M Y H:i'),
                                            'published' => '✅  Live' . ($record->published_at?->diffInHours(now()) > 48 ? ' (Backdated to ' . $record->published_at?->format('d M Y') . ')' : '') . '.',
                                        }
                                        : '✏️  Draft (new post)'
                                ),

                            Forms\Components\Toggle::make('is_published')
                                ->label('Enable publishing')
                                ->helperText('Turn ON to publish or schedule. Leave OFF to keep as Draft.')
                                ->live()
                                ->default(false),

                            Forms\Components\DateTimePicker::make('published_at')
                                ->label('Publish Date & Time')
                                ->helperText('Past date → Backdated (published immediately). Future date → Scheduled (auto-publishes). Blank → publishes now when toggled ON.')
                                ->nullable()
                                ->seconds(false)
                                ->native(false),

                            Forms\Components\Toggle::make('is_featured')
                                ->label('📌 Featured (Hero Post)')
                                ->helperText('Pin as the big hero on the /insights page.')
                                ->default(false),

                            Forms\Components\TextInput::make('reading_time_minutes')
                                ->label('Reading time (min)')
                                ->numeric()
                                ->default(5)
                                ->minValue(1),
                        ]),

                    Forms\Components\Section::make('Thumbnail')->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('')
                            ->image()
                            ->directory('posts/thumbnails')
                            ->imagePreviewHeight('160'),
                    ]),

                    Forms\Components\Section::make('Author')->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->default('RBeverything Team'),
                        Forms\Components\FileUpload::make('author_avatar')
                            ->label('Avatar')
                            ->image()
                            ->directory('posts/avatars')
                            ->imagePreviewHeight('80')
                            ->avatar(),
                        Forms\Components\Textarea::make('author_bio')
                            ->label('Bio')
                            ->rows(3),
                    ]),
                ]),

            ]), // end Grid

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->width(60)
                    ->height(40)
                    ->extraImgAttributes(['class' => 'rounded object-cover']),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'news',
                        'info'    => 'article',
                        'warning' => 'blog',
                    ]),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('⭐')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star'),

                // ── Computed status badge (Time-Travel) ──
                Tables\Columns\TextColumn::make('computed_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn(Post $record): string => $record->statusLabel())
                    ->color(fn(Post $record): string => $record->statusColor()),

                Tables\Columns\TextColumn::make('reading_time_minutes')
                    ->label('Read')
                    ->formatStateUsing(fn($state) => $state . ' min')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn(Post $record): ?string => match ($record->computedStatus()) {
                        'scheduled' => '⏰ ' . $record->published_at?->diffForHumans(),
                        'published' => $record->published_at?->diffForHumans(),
                        default     => null,
                    }),

            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'news'    => 'News',
                        'article' => 'Article',
                        'blog'    => 'Blog',
                    ]),
                Tables\Filters\Filter::make('draft')
                    ->label('Draft only')
                    ->query(fn($query) => $query->where('is_published', false)),
                Tables\Filters\Filter::make('scheduled')
                    ->label('Scheduled only')
                    ->query(fn($query) => $query->where('is_published', true)->where('published_at', '>', now())),
                Tables\Filters\Filter::make('live')
                    ->label('Live only')
                    ->query(fn($query) => $query->where('is_published', true)->where(fn($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Post $record) => route('insights.show', $record->slug))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
