<?php

namespace App\Filament\University\Resources\Applications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('application_code')
                    ->required(),
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                TextInput::make('university_major_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'processing' => 'Processing',
            'accepted' => 'Accepted',
            'registered' => 'Registered',
            'rejected' => 'Rejected',
            'canceled' => 'Canceled',
        ])
                    ->default('processing')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
