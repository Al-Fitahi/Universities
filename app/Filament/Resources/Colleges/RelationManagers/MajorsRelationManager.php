<?php

namespace App\Filament\Resources\Colleges\RelationManagers;

use App\Filament\Resources\Majors\MajorResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;



class MajorsRelationManager extends RelationManager
{
    protected static string $relationship = 'majors';

    // protected static ?string $relatedResource = MajorResource::class;



    public function form(Schema $schema): Schema
{
    return $schema
        ->components([
                TextInput::make('name')->required()->unique()
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                Textarea::make('designation_jobs')
                    ->default(null),
                TextInput::make('study_years')
                    ->default(null),
        ]);
}

public function table(Table $table): Table
{
    return $table
        ->columns([
          TextColumn::make('public_id')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->html(),
                TextColumn::make('designation_jobs')
                    ->searchable(),
                TextColumn::make('study_years')
                    ->searchable(),
                TextColumn::make('college_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
                //
            ])

        ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
}
}
