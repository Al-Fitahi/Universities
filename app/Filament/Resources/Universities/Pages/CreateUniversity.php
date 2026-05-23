<?php

namespace App\Filament\Resources\Universities\Pages;

use App\Filament\Resources\Universities\UniversityResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\University;
use App\Services\Image\ImageProcessor;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreateUniversity extends CreateRecord
{
    protected static string $resource = UniversityResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $processor = app(ImageProcessor::class);

        if (! empty($data['image_path']) && $data['image_path'] instanceof UploadedFile) {
            $paths = $processor->processAndStore($data['image_path'], 'universities');
            $data['image_path'] = $paths['path_main'] ?? null;
        }

        if (! empty($data['image_background']) && $data['image_background'] instanceof UploadedFile) {
            $paths = $processor->processAndStore($data['image_background'], 'universities');
            $data['image_background'] = $paths['path_main'] ?? null;
        }

        try {
            return University::create([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'password' => $data['password'] ?? null,
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'type' => $data['type'] ?? null,
                'location' => $data['location'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'image_background' => $data['image_background'] ?? null,
            ]);
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title('خطأ')->danger()->send();
            throw ValidationException::withMessages(['form' => 'خطأ أثناء حفظ الجامعة']);
        }
    }
}
