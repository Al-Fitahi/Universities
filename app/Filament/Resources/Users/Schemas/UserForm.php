<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
        TextInput::make('name')
                    ->required()
                    ->unique(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(),
                DateTimePicker::make('email_verified_at'),
        TextInput::make('password')
          ->password()
          ->dehydrateStateUsing(fn(?string $state) => $state ? Hash::make($state) : null)
          ->dehydrated(fn(?string $state) => filled($state))
          ->required(fn(string $operation) => $operation === 'create')
          ->maxLength(255)
          ->confirmed()
          ->hidden(fn($record, string $operation) => $operation === 'edit')
          ->revealable(),

        TextInput::make('password_confirmation')
          ->password()
          ->required(fn(string $operation) => $operation === 'create')
          ->same('password')
          ->hidden(fn($record, string $operation) => $operation === 'edit')
          ->revealable(),
            ]);
    }
}
