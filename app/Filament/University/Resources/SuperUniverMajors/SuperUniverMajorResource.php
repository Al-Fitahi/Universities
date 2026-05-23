<?php

namespace App\Filament\University\Resources\SuperUniverMajors;

use App\Filament\University\Resources\SuperUniverMajors\Pages\CreateSuperUniverMajor;
use App\Filament\University\Resources\SuperUniverMajors\Pages\EditSuperUniverMajor;
use App\Filament\University\Resources\SuperUniverMajors\Pages\ListSuperUniverMajors;
use App\Filament\University\Resources\SuperUniverMajors\Pages\ViewSuperUniverMajor;
use App\Filament\University\Resources\SuperUniverMajors\Schemas\SuperUniverMajorForm;
use App\Filament\University\Resources\SuperUniverMajors\Schemas\SuperUniverMajorInfolist;
use App\Filament\University\Resources\SuperUniverMajors\Tables\SuperUniverMajorsTable;
use App\Models\SuperUniverMajor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SuperUniverMajorResource extends Resource
{
    protected static ?string $model = SuperUniverMajor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات الأكاديمية';
    protected static ?string $recordTitleAttribute = 'supervisor.F_name';

    protected static ?string $navigationLabel = 'المشرفون والتخصصات';

    protected static ?string $modelLabel = 'ربط مشرف بتخصص';

    protected static ?string $pluralModelLabel = 'ربط المشرفين بالتخصصات';

    public static function form(Schema $schema): Schema
    {
        return SuperUniverMajorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SuperUniverMajorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuperUniverMajorsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'supervisor',
                'universityMajor.major.college',
            ])
            ->whereHas('universityMajor', function ($query) {
                $query->where('university_id', Auth::guard('university')->id());
            });
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuperUniverMajors::route('/'),
            'create' => CreateSuperUniverMajor::route('/create'),
            'view' => ViewSuperUniverMajor::route('/{record}'),
            'edit' => EditSuperUniverMajor::route('/{record}/edit'),
        ];
    }
}
