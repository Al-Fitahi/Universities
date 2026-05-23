<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages\CreateBanner;
use App\Filament\Resources\BannerResource\Pages\EditBanner;
use App\Filament\Resources\BannerResource\Pages\ListBanners;
use App\Filament\Resources\BannerResource\Pages\ViewBanner;
use App\Filament\Resources\BannerResource\RelationManagers\BannerImagesRelationManager;
use App\Models\Banner;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static UnitEnum|string|null $navigationGroup = 'المحتوى';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('عنوان البانر')
                ->required(),
            TextInput::make('location')
                ->label('موقع الظهور')
                ->helperText('مثال: الصفحة الرئيسية، صفحة التسجيل')
                ->required(),
            Toggle::make('is_active')
                ->label('مفعل')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),
                TextColumn::make('location')
                    ->label('الموقع')
                    ->searchable(),
                TextColumn::make('images_count')
                    ->label('عدد الصور')
                    ->state(fn (Banner $record) => $record->images()->count())
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('مفعل')
                    ->sortable(),
            ])
            ->defaultSort('title')
            ->recordUrl(null)
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BannerImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'view' => ViewBanner::route('/{record}'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }
}
