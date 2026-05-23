<?php

namespace App\Filament\University\Resources\Projects\Pages;

use App\Filament\University\Resources\Projects\ProjectResource;
use App\Models\Application;
use App\Models\Student;
use App\Models\SuperUniverMajor;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    /** @var array<int> */
    protected array $selectedStudentIds = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['student_ids'] = $this->getRecord()
            ->students()
            ->pluck('id')
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $superUniverMajor = SuperUniverMajor::query()
            ->whereKey($data['super_univer_major_id'] ?? null)
            ->whereHas('universityMajor', function (Builder $query): void {
                $query->where('university_id', Auth::guard('university')->id());
            })
            ->first();

        if (! $superUniverMajor) {
            throw ValidationException::withMessages([
                'super_univer_major_id' => 'الاختيار المحدد لا يتبع جامعتك.',
            ]);
        }

        $this->selectedStudentIds = collect($data['student_ids'] ?? [])
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($this->selectedStudentIds)) {
            throw ValidationException::withMessages([
                'student_ids' => 'اختر طالبًا واحدًا على الأقل.',
            ]);
        }

        $eligibleStudentsCount = Student::query()
            ->whereIn('id', $this->selectedStudentIds)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('project_id')
                    ->orWhere('project_id', $this->getRecord()->id);
            })
            ->whereHas('applications', function (Builder $query) use ($superUniverMajor): void {
                $query
                    ->where('university_major_id', $superUniverMajor->university_major_id)
                    ->where('status', Application::STATUS_ACCEPTED)
                    ->where('is_active', true);
            })
            ->count();

        if ($eligibleStudentsCount !== count($this->selectedStudentIds)) {
            throw ValidationException::withMessages([
                'student_ids' => 'بعض الطلاب غير مؤهلين أو مرتبطين بالفعل بمشروع آخر.',
            ]);
        }

        unset($data['student_ids']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $record->update($data);

            $currentStudentIds = Student::query()
                ->where('project_id', $record->id)
                ->pluck('id');

            $targetStudentIds = collect($this->selectedStudentIds);

            $studentsToDetach = $currentStudentIds
                ->diff($targetStudentIds)
                ->values();

            $studentsToAttach = $targetStudentIds
                ->diff($currentStudentIds)
                ->values();

            if ($studentsToDetach->isNotEmpty()) {
                Student::query()
                    ->where('project_id', $record->id)
                    ->whereIn('id', $studentsToDetach->all())
                    ->update(['project_id' => null]);
            }

            if ($studentsToAttach->isNotEmpty()) {
                $updatedStudents = Student::query()
                    ->whereIn('id', $studentsToAttach->all())
                    ->where(function (Builder $query) use ($record): void {
                        $query
                            ->whereNull('project_id')
                            ->orWhere('project_id', $record->id);
                    })
                    ->update(['project_id' => $record->id]);

                if ($updatedStudents !== $studentsToAttach->count()) {
                    throw ValidationException::withMessages([
                        'student_ids' => 'تعذر ربط بعض الطلاب، حاول التحديث ثم الإعادة.',
                    ]);
                }
            }

            return $record;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
