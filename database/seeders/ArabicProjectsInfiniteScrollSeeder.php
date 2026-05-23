<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\SuperUniverMajor;
use App\Models\UniversityMajor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ArabicProjectsInfiniteScrollSeeder extends Seeder
{
    private const TARGET_PROJECTS = 120;
    private const CERTIFICATE_MARKER = 'seeders/certificates/arabic-infinite-scroll.webp';

    public function run(): void
    {
        $projectTypes = $this->ensureProjectTypes();
        if ($projectTypes->isEmpty()) {
            $this->command?->warn('No project types available; seeder stopped.');

            return;
        }

        $superUniverMajors = $this->ensureSuperUniverMajors();
        if ($superUniverMajors->isEmpty()) {
            $this->command?->warn('No super-univer-major mappings available; seeder stopped.');

            return;
        }

        for ($i = 1; $i <= self::TARGET_PROJECTS; $i++) {
            $project = Project::query()->updateOrCreate(
                ['name' => $this->projectName($i)],
                [
                    'super_univer_major_id' => $superUniverMajors->random()->id,
                    'project_type_id' => $projectTypes->random()->id,
                    'project_overview' => $this->projectOverview($i),
                    'pdf_path' => sprintf('seeders/projects/arabic-project-%03d.pdf', $i),
                    'is_active' => true,
                ]
            );

            $studentCount = 2 + ($i % 3);
            $this->syncProjectStudents($project, $i, $studentCount);
        }

        $this->command?->info('Seeded/updated 120 Arabic projects for infinite scroll testing.');
    }

    private function ensureProjectTypes(): Collection
    {
        $existing = ProjectType::query()->get();
        if ($existing->isNotEmpty()) {
            return $existing;
        }

        $arabicTypes = [
            'مشروع بحثي',
            'مشروع تطبيقي',
            'مشروع هندسي',
            'مشروع ابتكاري',
            'مشروع تحليلي',
        ];

        foreach ($arabicTypes as $name) {
            ProjectType::query()->firstOrCreate(['name' => $name]);
        }

        return ProjectType::query()->get();
    }

    private function ensureSuperUniverMajors(): Collection
    {
        $existing = SuperUniverMajor::query()->get();
        if ($existing->isNotEmpty()) {
            return $existing;
        }

        $universityMajorIds = UniversityMajor::query()->pluck('id')->values();
        if ($universityMajorIds->isEmpty()) {
            return collect();
        }

        $firstNames = ['أحمد', 'محمد', 'علي', 'خالد', 'عبدالله', 'ياسر', 'صالح', 'مروان', 'سالم', 'هيثم'];
        $secondNames = ['حسن', 'ناصر', 'قاسم', 'عمر', 'إبراهيم', 'طه', 'عادل', 'حمود', 'مصطفى', 'نبيل'];
        $thirdNames = ['عبدالرحمن', 'عبدالكريم', 'عبدالسلام', 'عبدالوهاب', 'عبداللطيف', 'عبدالرزاق', 'عبدالقادر', 'عبدالغني', 'عبدالحميد', 'عبدالملك'];
        $familyNames = ['الشميري', 'الحداد', 'الأنسي', 'العواضي', 'الزبيري', 'الراعي', 'الحكيمي', 'المتوكل', 'السامعي', 'الحميري'];

        $needed = min(10, $universityMajorIds->count());

        for ($i = 0; $i < $needed; $i++) {
            $supervisor = Supervisor::query()->firstOrCreate(
                [
                    'F_name' => $firstNames[$i % count($firstNames)],
                    'S_name' => $secondNames[$i % count($secondNames)],
                    'Th_name' => $thirdNames[$i % count($thirdNames)],
                    'Su_name' => $familyNames[$i % count($familyNames)],
                ],
                [
                    'phone_number' => $this->tenDigitPhone(300000000 + $i),
                    'is_active' => true,
                ]
            );

            SuperUniverMajor::query()->firstOrCreate([
                'university_major_id' => $universityMajorIds[$i],
                'supervisor_id' => $supervisor->id,
            ]);
        }

        return SuperUniverMajor::query()->get();
    }

    private function syncProjectStudents(Project $project, int $projectNumber, int $count): void
    {
        $expectedKeys = [];

        for ($slot = 1; $slot <= $count; $slot++) {
            $data = $this->studentData($projectNumber, $slot, $project->id);
            $expectedKeys[] = $this->studentKey($data);

            Student::query()->updateOrCreate(
                [
                    'F_name' => $data['F_name'],
                    'S_name' => $data['S_name'],
                    'Th_name' => $data['Th_name'],
                    'Su_name' => $data['Su_name'],
                    'certificate_image' => $data['certificate_image'],
                ],
                [
                    'phone_number' => $data['phone_number'],
                    'graduation_date' => $data['graduation_date'],
                    'graduation_grade' => $data['graduation_grade'],
                    'project_id' => $data['project_id'],
                ]
            );
        }

        $seeded = Student::query()
            ->where('project_id', $project->id)
            ->where('certificate_image', self::CERTIFICATE_MARKER)
            ->get();

        foreach ($seeded as $student) {
            if (!in_array($this->studentKey($student->toArray()), $expectedKeys, true)) {
                $student->delete();
            }
        }
    }

    private function studentData(int $projectNumber, int $slot, int $projectId): array
    {
        $firstNames = ['سارة', 'أحمد', 'ريم', 'حسن', 'ليان', 'مازن', 'شهد', 'أنس', 'هبة', 'عمر', 'مها', 'زيد'];
        $secondNames = ['عبدالله', 'محمد', 'علي', 'إبراهيم', 'سالم', 'ناصر', 'خالد', 'يحيى', 'فارس', 'هاشم'];
        $thirdNames = ['صالح', 'قاسم', 'عبدالرحمن', 'حسين', 'طه', 'عادل', 'مصطفى', 'نبيل', 'رائد', 'سعيد'];
        $familyNames = ['الشميري', 'الأنسي', 'الحداد', 'العواضي', 'الزبيري', 'المتوكل', 'الراعي', 'الحكيمي', 'الحميري', 'السامعي'];

        $seed = ($projectNumber * 11) + $slot;
        $grade = round(min(0.99, 0.70 + (($seed % 30) / 100)), 2);

        return [
            'F_name' => $firstNames[$seed % count($firstNames)],
            'S_name' => $secondNames[$seed % count($secondNames)],
            'Th_name' => $thirdNames[$seed % count($thirdNames)],
            'Su_name' => $familyNames[$seed % count($familyNames)] . sprintf('-%03d-%d', $projectNumber, $slot),
            'phone_number' => $this->tenDigitPhone(700000000 + $seed),
            'graduation_date' => now()
                ->subYears(1 + ($seed % 3))
                ->subDays($seed % 365)
                ->toDateString(),
            'graduation_grade' => $grade,
            'certificate_image' => self::CERTIFICATE_MARKER,
            'project_id' => $projectId,
        ];
    }

    private function projectName(int $index): string
    {
        $domains = ['الذكاء الاصطناعي', 'تحليل البيانات', 'أمن المعلومات', 'نظم المعلومات', 'هندسة البرمجيات', 'الشبكات', 'الحوسبة السحابية', 'إنترنت الأشياء'];
        $kinds = ['نظام', 'منصة', 'تطبيق', 'حل', 'نموذج'];

        return sprintf(
            'مشروع %s في %s رقم %03d',
            $kinds[$index % count($kinds)],
            $domains[($index * 3) % count($domains)],
            $index
        );
    }

    private function projectOverview(int $index): string
    {
        return "هذا المشروع رقم {$index} يهدف إلى تقديم حل عملي قابل للتنفيذ ضمن بيئة جامعية، مع توثيق منهجية العمل وقياس النتائج بشكل واضح.";
    }

    private function tenDigitPhone(int $number): string
    {
        return substr(str_pad((string) $number, 10, '0', STR_PAD_LEFT), -10);
    }

    private function studentKey(array $student): string
    {
        return implode('|', [
            $student['F_name'] ?? '',
            $student['S_name'] ?? '',
            $student['Th_name'] ?? '',
            $student['Su_name'] ?? '',
            $student['certificate_image'] ?? '',
        ]);
    }
}
