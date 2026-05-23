<?php

namespace App\Filament\Resources\Universities\Pages;

use App\Filament\Resources\Universities\UniversityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Services\Image\ImageProcessor;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class EditUniversity extends EditRecord
{
    protected static string $resource = UniversityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $processor = app(ImageProcessor::class);

        if (! empty($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
            $processor->deleteStored($record->image_path);
            $paths = $processor->processAndStore($data['image_path'], 'universities');
            $data['image_path'] = $paths['path_main'] ?? $record->image_path;
        }

        if (! empty($data['image_background']) && $data['image_background'] instanceof UploadedFile) {
            $processor->deleteStored($record->image_background);
            $paths = $processor->processAndStore($data['image_background'], 'universities');
            $data['image_background'] = $paths['path_main'] ?? $record->image_background;
        }

        try {
            $record->update($data);
            return $record;
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title('خطأ')->danger()->send();
            throw ValidationException::withMessages(['form' => 'خطأ أثناء تحديث الجامعة']);
        }
    }
}
