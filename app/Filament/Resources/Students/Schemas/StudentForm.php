<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('F_name')
                    ->required(),
                TextInput::make('S_name')
                    ->required(),
                TextInput::make('Th_name')
                    ->required(),
                TextInput::make('Su_name')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                DatePicker::make('graduation_date')
                    ->required(),
                TextInput::make('graduation_grade')
                    ->required()
                    ->numeric(),
                    FileUpload::make('certificate_image')
                        ->label('شهادة التخرج')
                        ->image()
                        ->maxSize(6144)
                        ->required(),
            ]);
    }
}
