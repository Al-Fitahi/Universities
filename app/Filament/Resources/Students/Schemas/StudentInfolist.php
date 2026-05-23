<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('public_id'),
                TextEntry::make('F_name'),
                TextEntry::make('S_name'),
                TextEntry::make('Th_name'),
                TextEntry::make('Su_name'),
                TextEntry::make('phone_number'),
                TextEntry::make('graduation_date')
                    ->date(),
                TextEntry::make('graduation_grade')
                    ->numeric(),
                ImageEntry::make('certificate_image'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
