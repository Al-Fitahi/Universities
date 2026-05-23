<?php

namespace App\Filament\University\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('academic_path')
                    ->label('الكلية / التخصص / المشرف')
                    ->state(function (Project $record): string {
                        $collegeName = $record->superUniverMajor?->universityMajor?->major?->college?->name ?? 'كلية غير معروفة';
                        $majorName = $record->superUniverMajor?->universityMajor?->major?->name ?? 'تخصص غير معروف';
                        $supervisorName = $record->superUniverMajor?->supervisor?->full_name ?? 'مشرف غير معروف';

                        return "{$collegeName} - {$majorName} - {$supervisorName}";
                    })
                    ->wrap()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('اسم المشروع')
                    ->searchable(),
                TextColumn::make('projectType.name')
                    ->label('نوع المشروع')
                    ->sortable(),
                TextColumn::make('students_count')
                    ->label('عدد الطلاب')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
