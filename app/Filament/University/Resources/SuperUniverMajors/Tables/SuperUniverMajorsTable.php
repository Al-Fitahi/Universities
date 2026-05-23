<?php

namespace App\Filament\University\Resources\SuperUniverMajors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuperUniverMajorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
                ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('supervisor.F_name')
                    ->label('المشرف')
                    ->formatStateUsing(fn ($state, $record) => $record->supervisor?->full_name ?? $state)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('supervisor.phone_number')
                    ->label('الهاتف')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('universityMajor.major.college.name')
                    ->label('الكلية')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('universityMajor.major.name')
                    ->label('التخصص')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('أضيف في')
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
