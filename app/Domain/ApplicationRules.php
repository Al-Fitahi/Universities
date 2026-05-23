<?php

namespace App\Domain;

use App\Models\Application;
use App\Models\Student;
use App\Models\UniversityMajor;
use Carbon\Carbon;
use App\Exceptions\ApplicationRuleException;

/**
 * Class ApplicationRules
 *
 * يحتوي قواعد التحقق قبل إنشاء الطلب
 */
class ApplicationRules
{
    /**
     * منع إنشاء طلب جديد إذا كان هناك طلب سابق غير مرفوض أو غير ملغي
     */
    public static function ensureNoActiveOrBlocked(int $studentId): void
    {
        $exists = Application::where('student_id', $studentId)
            ->where(function ($q) {
                $q->where('is_active', true)
                  ->orWhereNotIn('status', ['rejected', 'canceled']);
            })
            ->exists();

        if ($exists) {
            throw new ApplicationRuleException(
                "لا يمكن إنشاء طلب جديد قبل الانتهاء من الطلب السابق."
            );
        }
    }

    /**
     * منع إعادة التقديم لنفس التخصص بعد رفض أقل من 9 أشهر
     */
    public static function ensureNotRejectedRecently(int $studentId, int $majorId): void
    {
        $prev = Application::where('student_id', $studentId)
            ->where('university_major_id', $majorId)
            ->where('status', 'rejected')
            ->latest()
            ->first();

        if ($prev) {
            $months = Carbon::parse($prev->created_at)->diffInMonths(now());

            if ($months < 9) {
                throw new ApplicationRuleException(
                    "يجب مرور 9 أشهر على آخر رفض لنفس التخصص."
                );
            }
        }
    }

    /**
     * التأكد أن معدل الطالب يحقق معدل القبول للتخصص
     */
    public static function ensureMeetsAdmissionRate(Student $student, UniversityMajor $major): void
    {
        if ($student->graduation_grade < $major->admission_rate) {
            throw new ApplicationRuleException(
                "معدل الطالب أقل من المعدل المطلوب للتقديم."
            );
        }
    }
}
