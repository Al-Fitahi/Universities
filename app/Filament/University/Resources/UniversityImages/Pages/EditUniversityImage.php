<?php

namespace App\Filament\University\Resources\UniversityImages\Pages;

use App\Filament\University\Resources\UniversityImages\UniversityImageResource;
use App\Models\UniversityImage;
use App\Services\Image\ImageProcessor;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUniversityImage extends EditRecord
{
    protected static string $resource = UniversityImageResource::class;

    /**
     * Replace image if a new file is provided and update priority/toggle.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $replacement = $data['replacement'] ?? null;
        $processor = app(ImageProcessor::class);

        if ($replacement) {
            $processor->deleteStored($record->path_main);
            $processor->deleteStored($record->path_thumb);

            $paths = $processor->processAndStore($replacement, "universities/{$record->university_id}");
            $data['path_main'] = $paths['path_main'];
            $data['path_thumb'] = $paths['path_thumb'];
        }

        unset($data['replacement'], $data['uploads']);

        $record->update([
            'path_main' => $data['path_main'] ?? $record->path_main,
            'path_thumb' => $data['path_thumb'] ?? $record->path_thumb,
            'priority' => $data['priority'] ?? $record->priority,
            'is_active' => $data['is_active'] ?? $record->is_active,
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('تم الحفظ')
            ->body('تم تحديث الصورة بنجاح.')
            ->success();
    }
}
