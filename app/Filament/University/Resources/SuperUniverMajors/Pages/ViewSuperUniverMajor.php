<?php

namespace App\Filament\University\Resources\SuperUniverMajors\Pages;

use App\Filament\University\Resources\SuperUniverMajors\SuperUniverMajorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSuperUniverMajor extends ViewRecord
{
    protected static string $resource = SuperUniverMajorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
