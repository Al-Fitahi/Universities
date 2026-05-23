<?php

namespace App\Filament\University\Resources\UniversityImages\Pages;

use App\Filament\University\Resources\UniversityImages\UniversityImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniversityImages extends ListRecords
{
    protected static string $resource = UniversityImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إضافة صور'),
        ];
    }
}
