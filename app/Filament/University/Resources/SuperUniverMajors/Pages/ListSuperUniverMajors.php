<?php

namespace App\Filament\University\Resources\SuperUniverMajors\Pages;

use App\Filament\University\Resources\SuperUniverMajors\SuperUniverMajorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuperUniverMajors extends ListRecords
{
    protected static string $resource = SuperUniverMajorResource::class;

      protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
