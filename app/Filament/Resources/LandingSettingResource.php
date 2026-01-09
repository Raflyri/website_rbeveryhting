<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingSettingResource\Pages;
use App\Filament\Resources\LandingSettingResource\RelationManagers;
use App\Models\LandingSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;

class LandingSettingResource extends Resource
{
    protected static ?string $model = LandingSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    use Translatable;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hero_title')
                    ->label('Judul')
                    ->required(),

                Forms\Components\Textarea::make('vision_desc')
                    ->label('Deskripsi Visi')
                    ->rows(3)
                    ->helperText('Teks ini akan muncul di bagian bawah (Our Vision).')
                    ->required(),

                Forms\Components\FileUpload::make('hero_image')
                    ->label('Background Image (Jika Video Kosong)')
                    ->image()
                    ->disk('public')
                    ->directory('landing-assets'),

                Forms\Components\FileUpload::make('hero_video')
                    ->label('Background Video (MP4)')
                    ->disk('public')
                    ->acceptedFileTypes(['video/mp4'])
                    ->directory('landing-assets')
                    ->maxSize(50000),

                Forms\Components\Section::make('Status Website')
                    ->schema([
                        Forms\Components\Toggle::make('is_maintenance_mode')
                            ->label('Maintenance Mode (Coming Soon)')
                            ->helperText('Jika aktif, pengunjung akan melihat halaman Coming Soon. Jika mati, pengunjung melihat Website Utama.')
                            ->onColor('danger') // Merah artinya Maintenance/Berhenti
                            ->offColor('success') // Hijau artinya Live/Jalan
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListLandingSettings::route('/'),
            'create' => Pages\CreateLandingSetting::route('/create'),
            'edit' => Pages\EditLandingSetting::route('/{record}/edit'),
        ];
    }
}
