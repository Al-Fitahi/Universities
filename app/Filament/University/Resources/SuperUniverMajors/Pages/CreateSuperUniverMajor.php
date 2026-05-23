<?php

namespace App\Filament\University\Resources\SuperUniverMajors\Pages;

use App\Filament\University\Resources\SuperUniverMajors\SuperUniverMajorResource;
use App\Models\SuperUniverMajor;
use App\Models\UniversityMajor;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateSuperUniverMajor extends CreateRecord
{
    protected static string $resource = SuperUniverMajorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $universityId = Auth::guard('university')->id();

        $isMajorAllowed = UniversityMajor::query()
            ->whereKey($data['university_major_id'] ?? null)
            ->where('university_id', $universityId)
            ->exists();

        if (! $isMajorAllowed) {
            throw ValidationException::withMessages([
                'university_major_id' => 'التخصص المحدد لا يتبع جامعتك.',
            ]);
        }

        $alreadyLinked = SuperUniverMajor::query()
            ->where('university_major_id', $data['university_major_id'])
            ->where('supervisor_id', $data['supervisor_id'])
            ->exists();

        if ($alreadyLinked) {
            throw ValidationException::withMessages([
                'university_major_id' => 'هذا المشرف مرتبط مسبقًا بنفس التخصص.',
            ]);
        }

        unset($data['college_id']);

        return $data;
    }
}
