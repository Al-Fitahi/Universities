<?php

namespace App\Filament\Resources\Majors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class MajorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('public_id'),
                TextEntry::make('name'),
                TextEntry::make('description')
                ->html(),
                TextEntry::make('designation_jobs'),
                TextEntry::make('study_years'),
                TextEntry::make('college_id')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
