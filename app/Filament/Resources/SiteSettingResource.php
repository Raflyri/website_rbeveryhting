<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Filament\Resources\SiteSettingResource\RelationManagers;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Site Content';

    public static function canCreate(): bool
    {
        return false; // Only allow editing the existing settings, prevents creating multiple overlapping settings
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Header Menu')
                    ->description('Manage the links displayed in the top navigation bar.')
                    ->schema([
                        Forms\Components\Repeater::make('header_menu')
                            ->schema([
                                Forms\Components\TextInput::make('label')->required(),
                                Forms\Components\TextInput::make('url')->label('URL')->required(),
                            ])
                            ->columns(2)
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Footer Menu')
                    ->description('Manage the links displayed in the bottom footer.')
                    ->schema([
                        Forms\Components\Repeater::make('footer_menu')
                            ->schema([
                                Forms\Components\TextInput::make('label')->required(),
                                Forms\Components\TextInput::make('url')->label('URL')->required(),
                            ])
                            ->columns(2)
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
