<?php

namespace App\Filament\Resources\Streets;

use App\Filament\Resources\Streets\Pages\CreateStreet;
use App\Filament\Resources\Streets\Pages\EditStreet;
use App\Filament\Resources\Streets\Pages\ListStreets;
use App\Filament\Resources\Streets\Pages\ViewStreet;
use App\Filament\Resources\Streets\Schemas\StreetForm;
use App\Filament\Resources\Streets\Schemas\StreetInfolist;
use App\Filament\Resources\Streets\Tables\StreetsTable;
use App\Models\Street;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StreetResource extends Resource
{
    protected static ?string $model = Street::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StreetForm::configure($schema);
    }

      public static function getNavigationBadge(): ?string
    {
    return static::getModel()::count();
    }

    public static function infolist(Schema $schema): Schema
    {
        return StreetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StreetsTable::configure($table);
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
            'index' => ListStreets::route('/'),
            'create' => CreateStreet::route('/create'),
            'view' => ViewStreet::route('/{record}'),
            'edit' => EditStreet::route('/{record}/edit'),
        ];
    }
}
