<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Major;
use App\Models\Street;
use App\Models\University;
use App\Models\UniversityMajor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ApplicationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_validation_error_when_student_grade_is_below_required_rate(): void
    {
        [$universityMajor] = $this->createPublishedMajorWithAdmissionRate(90);
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/apply/' . $universityMajor->university->public_id . '?major=' . $universityMajor->public_id)
            ->post('/apply', $this->payloadFor($universityMajor->public_id, 70));

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'graduation_grade' => 'معدل الطالب أقل من المعدل المطلوب للتقديم.',
        ]);
        $this->assertDatabaseCount((new Application())->getTable(), 0);
    }

    public function test_it_returns_validation_error_when_university_major_is_invalid(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/apply/invalid?major=invalid-major-id')
            ->post('/apply', $this->payloadFor('invalid-major-id', 95));

        $response->assertRedirect();
        $response->assertSessionHasErrors('university_major_id');
        $this->assertDatabaseCount((new Application())->getTable(), 0);
    }

    private function payloadFor(string $universityMajorPublicId, float $graduationGrade): array
    {
        return [
            'university_major_id' => $universityMajorPublicId,
            'F_name' => 'Ali',
            'S_name' => 'Ahmed',
            'Th_name' => 'Saleh',
            'Su_name' => 'Hassan',
            'phone_number' => '777777777',
            'graduation_date' => '2024-06-01',
            'graduation_grade' => $graduationGrade,
            'certificate_image' => UploadedFile::fake()->image('certificate.jpg'),
        ];
    }

    /**
     * @return array{0: UniversityMajor, 1: University, 2: Major}
     */
    private function createPublishedMajorWithAdmissionRate(float $admissionRate): array
    {
        Street::factory()->create();
        $university = University::factory()->create(['status' => 'approved']);
        $major = Major::factory()->create();
        $universityMajor = UniversityMajor::factory()->create([
            'university_id' => $university->id,
            'major_id' => $major->id,
            'published' => true,
            'admission_rate' => $admissionRate,
        ]);

        return [$universityMajor, $university, $major];
    }
}
