<?php

namespace App\Filament\Resources\Majors\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Laravel\Prompts\TextareaPrompt;
use Psy\Readline\Hoa\Autocompleter;
use Symfony\Component\Console\Descriptor\TextDescriptor;

class MajorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')->required()->unique()->autocomplete()
                    ->required(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                Textarea::make('designation_jobs')
                    ->default(null),
                TextInput::make('study_years')
                    ->default(null),
                Select::make('college_id')
                    ->relationship('college', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
            ]);
    }
}
