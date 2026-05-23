<?php

namespace App\Filament\Resources\Colleges\Pages;

use App\Filament\Resources\Colleges\CollegeResource;
use App\Models\College;
use App\Services\Image\ImageProcessor;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateCollege extends CreateRecord
{
    protected static string $resource = CollegeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // If an uploaded file is present, process it with our ImageProcessor
        $processor = app(ImageProcessor::class);

        if (! empty($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
            $paths = $processor->processAndStore($data['image_path'], 'colleges');
            $data['image_path'] = $paths['path_main'];
        }

        try {
            return College::create([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
            ]);
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('حدث خطأ')
                ->body('حدث خطأ أثناء حفظ الكلية.')
                ->danger()
                ->send();

            throw ValidationException::withMessages(['form' => 'خطأ في حفظ السجل.']);
        }
    }
}
