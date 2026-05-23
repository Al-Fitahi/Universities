<?php

namespace App\Filament\University\Resources\Projects\Pages;

use App\Filament\University\Resources\Projects\ProjectResource;
use App\Models\Application;
use App\Models\Project;
use App\Models\Student;
use App\Models\SuperUniverMajor;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    /** @var array<int> */
    protected array $selectedStudentIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
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
            ->whereNull('project_id')
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

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $project = Project::create($data);

            $updatedStudents = Student::query()
                ->whereIn('id', $this->selectedStudentIds)
                ->whereNull('project_id')
                ->update(['project_id' => $project->id]);

            if ($updatedStudents !== count($this->selectedStudentIds)) {
                throw ValidationException::withMessages([
                    'student_ids' => 'تعذر ربط بعض الطلاب، حاول التحديث ثم الإعادة.',
                ]);
            }

            return $project;
        });
    }
}
