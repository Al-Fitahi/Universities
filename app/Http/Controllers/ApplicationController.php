<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Exceptions\ApplicationRuleException;
use App\Models\UniversityMajor;
use App\Services\ApplicationService;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    protected ApplicationService $service;

    public function __construct(ApplicationService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض صفحة التقديم لجامعة محددة
     */
    public function create(string $university): Response
    {
        $majors = UniversityMajor::query()
            ->whereHas('university', function ($q) use ($university) {
                $q->where('public_id', $university)->orWhere('id', $university);
            })
            ->where('published', true)
            ->with('major:id,public_id,name')
            ->get()
            ->map(function (UniversityMajor $universityMajor) {
                return [
                    'id'   => $universityMajor->public_id,          // ✅ university_major.public_id
                    'name' => $universityMajor->major?->name ?? '',
                    'gpa'  => (float) ($universityMajor->admission_rate ?? 0),
                ];
            })
            ->values();

        return Inertia::render('Apply', [
            'majors'               => $majors,
            'selectedUniversityId' => (string) $university,
        ]);
    }

    /**
     * إنشاء طلب جديد بعد تطبيق قواعد التحقق
     */
    public function store(ApplicationRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('certificate_image')) {
            $validated['certificate_image'] = $request
                ->file('certificate_image')
                ->store('certificates', 'public');
        }

        try {
            $this->service->createApplication($validated);
        } catch (ApplicationRuleException $exception) {
            throw ValidationException::withMessages([
                'graduation_grade' => $exception->getMessage(),
            ]);
        }

        return back()->with('success', 'تم إنشاء الطلب بنجاح');
    }
}
