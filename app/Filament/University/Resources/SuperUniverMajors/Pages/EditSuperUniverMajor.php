<?php

namespace App\Filament\University\Resources\SuperUniverMajors\Pages;

use App\Filament\University\Resources\SuperUniverMajors\SuperUniverMajorResource;
use App\Models\SuperUniverMajor;
use App\Models\UniversityMajor;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EditSuperUniverMajor extends EditRecord
{
    protected static string $resource = SuperUniverMajorResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['college_id'] = $record->universityMajor?->major?->college_id;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
            ->whereKeyNot($this->getRecord()->id)
            ->exists();

        if ($alreadyLinked) {
            throw ValidationException::withMessages([
                'university_major_id' => 'هذا المشرف مرتبط مسبقًا بنفس التخصص.',
            ]);
        }

        unset($data['college_id']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
