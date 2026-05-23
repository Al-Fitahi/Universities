<?php

namespace App\Filament\University\Resources\Projects;

use App\Filament\University\Resources\Projects\Pages\CreateProject;
use App\Filament\University\Resources\Projects\Pages\EditProject;
use App\Filament\University\Resources\Projects\Pages\ListProjects;
use App\Filament\University\Resources\Projects\Pages\ViewProject;
use App\Filament\University\Resources\Projects\Schemas\ProjectForm;
use App\Filament\University\Resources\Projects\Schemas\ProjectInfolist;
use App\Filament\University\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $universityId = Auth::guard('university')->id();

        return parent::getEloquentQuery()
            ->select('projects.*')
            ->with([
                'projectType:id,name',
                'superUniverMajor:id,university_major_id,supervisor_id',
                'superUniverMajor.supervisor:id,F_name,S_name,Th_name,Su_name',
                'superUniverMajor.universityMajor:id,major_id,university_id',
                'superUniverMajor.universityMajor.major:id,name,college_id',
                'superUniverMajor.universityMajor.major.college:id,name',
                'students:id,F_name,S_name,Th_name,Su_name,project_id',
            ])
            ->withCount('students')
            ->whereHas('superUniverMajor.universityMajor', function (Builder $query) use ($universityId): void {
                $query->where('university_id', $universityId);
            });
    }


    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
