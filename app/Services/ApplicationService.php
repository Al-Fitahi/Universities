<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Student;
use App\Models\UniversityMajor;
use App\Domain\ApplicationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    /**
     * إنشاء الطلب بعد تطبيق قواعد التحقق
     */
    public function createApplication(array $data): Application
    {
        return DB::transaction(function () use ($data) {

            // 1) إنشاء أو استرجاع الطالب
            $student = Student::findOrCreateByFullName($data);

            // 2) جلب university_major مباشرة بدون استعلامات وسيطة ✅
            $major = UniversityMajor::where('public_id', $data['university_major_id'])
                ->where('published', true)
                ->first();

            if (! $major) {
                throw ValidationException::withMessages([
                    'university_major_id' => 'التخصص المطلوب للتقديم غير متاح حالياً.',
                ]);
            }

            $studentId = $student->id;
            $majorId   = $major->id;

            // 3) القواعد
            ApplicationRules::ensureNoActiveOrBlocked($studentId);
            ApplicationRules::ensureNotRejectedRecently($studentId, $majorId);
            ApplicationRules::ensureMeetsAdmissionRate($student, $major);

            // 4) إنشاء الطلب
            return Application::create([
                'student_id'         => $studentId,
                'university_major_id' => $majorId,
                'user_id'            => Auth::id(),
                'status'             => 'processing',
                'is_active'          => true,
                'application_code'   => $this->generateUniqueCode(),
            ]);
        });
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Application::where('application_code', $code)->exists());

        return $code;
    }
}
