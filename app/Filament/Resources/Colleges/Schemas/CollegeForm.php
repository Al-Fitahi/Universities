<?php

namespace App\Filament\Resources\Colleges\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CollegeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')->required()->unique()->autocomplete()
                    ->required(),

                RichEditor::make('description')
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->image()
                    ->directory('colleges')
                    ->storeFiles(false)
                    ->maxSize(6144)
                    ->preserveFilenames()
                              ->downloadable()
          ->openable()
          ->dehydrated(fn($state) => filled($state)),



            ]);
    }
}
