<?php

namespace App\Filament\Resources\Colleges\Pages;

use App\Filament\Resources\Colleges\CollegeResource;
use App\Services\Image\ImageProcessor;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;

class EditCollege extends EditRecord
{
    protected static string $resource = CollegeResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $processor = app(ImageProcessor::class);

        // If user uploaded a replacement file via the same field
        if (! empty($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
            // delete old images if any
            $processor->deleteStored($record->image_path);

            $paths = $processor->processAndStore($data['image_path'], 'colleges');
            $data['image_path'] = $paths['path_main'];
        }

        unset($data['uploads']);

        $record->update([
            'name' => $data['name'] ?? $record->name,
            'description' => $data['description'] ?? $record->description,
            'image_path' => $data['image_path'] ?? $record->image_path,
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
