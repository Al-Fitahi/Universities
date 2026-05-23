<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Student;
use App\Models\UniversityMajor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class University59ProjectTestSeeder extends Seeder
{
    public function run(): void
    {
        $universityId = 58;
        $studentsCount = 20;

        $universityMajorIds = UniversityMajor::query()
            ->where('university_id', $universityId)
            ->pluck('id');

        if ($universityMajorIds->isEmpty()) {
            $this->command?->warn("No university_majors found for university_id={$universityId}. Seeder stopped.");

            return;
        }

        $userId = User::query()->value('id');

        if (! $userId) {
            $userId = User::factory()->create()->id;
        }

        $faker = fake();

        for ($i = 0; $i < $studentsCount; $i++) {
            $student = Student::create([
                'F_name' => $faker->firstName(),
                'S_name' => $faker->lastName(),
                'Th_name' => $faker->firstName(),
                'Su_name' => $faker->lastName(),
                'phone_number' => $faker->numerify('0#########'),
                'graduation_date' => $faker->date(),
                'graduation_grade' => $faker->randomFloat(2, 0.60, 0.99),
                'certificate_image' => 'seeders/test-certificate.webp',
            ]);

            $applicationCode = $this->uniqueApplicationCode();

            Application::create([
                'student_id' => $student->id,
                'application_code' => $applicationCode,
                'university_major_id' => $universityMajorIds->random(),
                'user_id' => $userId,
                'status' => Application::STATUS_ACCEPTED,
                'is_active' => true,
            ]);
        }

        $this->command?->info("Seeded {$studentsCount} fake students + applications for university_id={$universityId}.");
    }

    private function uniqueApplicationCode(): string
    {
        do {
            $code = 'APP-TEST-' . Str::upper(Str::random(10));
        } while (Application::query()->where('application_code', $code)->exists());

        return $code;
    }
}
