<?php

namespace App\Filament\University\Resources\UniversityImages\Pages;

use App\Filament\University\Resources\UniversityImages\UniversityImageResource;
use App\Models\UniversityImage;
use App\Services\Image\ImageProcessor;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

class CreateUniversityImage extends CreateRecord
{
    protected static string $resource = UniversityImageResource::class;

    /**
     * Handle multiple uploads and create individual image records.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $files = $data['uploads'] ?? [];
        $universityId = Filament::auth()->id();

        if (empty($files)) {
            throw ValidationException::withMessages([
                'uploads' => 'يجب اختيار صورة واحدة على الأقل.',
            ]);
        }

        $currentCount = UniversityImage::where('university_id', $universityId)->count();
        $remaining = 10 - $currentCount;

        if (count($files) > $remaining) {
            Notification::make()
                ->title('لا يمكن رفع أكثر من 10 صور')
                ->body('المتاح حالياً: '.max($remaining, 0).'.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'uploads' => 'الحد الأقصى 10 صور. المتاح حالياً: '.max($remaining, 0).'.',
            ]);
        }

        $priorityStart = (int) UniversityImage::where('university_id', $universityId)->max('priority');
        $processor = app(ImageProcessor::class);
        $last = null;

        foreach ($files as $index => $file) {
            $paths = $processor->processAndStore($file, "universities/{$universityId}");

            $last = UniversityImage::create([
                'university_id' => $universityId,
                'path_main' => $paths['path_main'],
                'path_thumb' => $paths['path_thumb'],
                'priority' => $priorityStart + $index + 1,
                'is_active' => true,
            ]);
        }

        Notification::make()
            ->title('تم رفع الصور')
            ->body('تم حفظ الصور ومعالجتها بنجاح.')
            ->success()
            ->send();

        return $last ?? new UniversityImage();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
