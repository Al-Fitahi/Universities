<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\Major;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Street;
use App\Models\SuperUniverMajor;
use App\Models\Supervisor;
use App\Models\University;
use App\Models\UniversityMajor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversityProjectsFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_treats_all_filter_values_as_no_filter(): void
    {
        $university = $this->createUniversity();

        [$firstProject] = $this->createProjectGraph($university, 'Engineering College', 'Software Engineering', 'Software Project');
        [$secondProject] = $this->createProjectGraph($university, 'Business College', 'Accounting', 'Accounting Project');

        $baseline = $this->getJson("/universities/{$university->public_id}/projects");
        $baseline->assertOk();

        $withAll = $this->getJson(
            "/universities/{$university->public_id}/projects?college=all&major=all&date=all&type=all"
        );
        $withAll->assertOk();

        $baselineIds = collect($baseline->json('universityProjects'))->pluck('id')->sort()->values()->all();
        $allIds = collect($withAll->json('universityProjects'))->pluck('id')->sort()->values()->all();

        $this->assertSame([(string) $firstProject->public_id, (string) $secondProject->public_id], $baselineIds);
        $this->assertSame($baselineIds, $allIds);
    }

    public function test_it_filters_projects_by_college_and_major_public_id(): void
    {
        $university = $this->createUniversity();

        [$softwareProject, $softwareCollege, $softwareMajor] = $this->createProjectGraph(
            $university,
            'Engineering College',
            'Software Engineering',
            'Software Project'
        );

        [$accountingProject, $accountingCollege, $accountingMajor] = $this->createProjectGraph(
            $university,
            'Business College',
            'Accounting',
            'Accounting Project'
        );

        $collegeResponse = $this->getJson(
            "/universities/{$university->public_id}/projects?college={$softwareCollege->public_id}"
        );
        $collegeResponse->assertOk();
        $collegeResponse->assertJsonCount(1, 'universityProjects');
        $this->assertSame((string) $softwareProject->public_id, data_get($collegeResponse->json(), 'universityProjects.0.id'));

        $majorResponse = $this->getJson(
            "/universities/{$university->public_id}/projects?major={$softwareMajor->public_id}"
        );
        $majorResponse->assertOk();
        $majorResponse->assertJsonCount(1, 'universityProjects');
        $this->assertSame((string) $softwareProject->public_id, data_get($majorResponse->json(), 'universityProjects.0.id'));

        $combinedResponse = $this->getJson(
            "/universities/{$university->public_id}/projects?college={$accountingCollege->public_id}&major={$accountingMajor->public_id}"
        );
        $combinedResponse->assertOk();
        $combinedResponse->assertJsonCount(1, 'universityProjects');
        $this->assertSame((string) $accountingProject->public_id, data_get($combinedResponse->json(), 'universityProjects.0.id'));
    }

    private function createUniversity(): University
    {
        Street::factory()->create();

        return University::factory()->create([
            'status' => 'approved',
        ]);
    }

    /**
     * @return array{0: Project, 1: College, 2: Major}
     */
    private function createProjectGraph(University $university, string $collegeName, string $majorName, string $projectName): array
    {
        $college = College::factory()->create([
            'name' => $collegeName,
        ]);

        $major = Major::factory()->create([
            'name' => $majorName,
            'college_id' => $college->id,
        ]);

        $universityMajor = UniversityMajor::factory()->create([
            'major_id' => $major->id,
            'university_id' => $university->id,
            'published' => true,
        ]);

        $supervisor = Supervisor::query()->create([
            'F_name' => 'Ali',
            'S_name' => 'Ahmad',
            'Th_name' => 'Hassan',
            'Su_name' => 'Saleh',
            'phone_number' => fake()->numerify('##########'),
            'is_active' => true,
        ]);

        $superUniverMajor = SuperUniverMajor::query()->create([
            'university_major_id' => $universityMajor->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $projectType = ProjectType::factory()->create([
            'name' => $projectName . ' Type',
        ]);

        $project = Project::query()->create([
            'super_univer_major_id' => $superUniverMajor->id,
            'name' => $projectName,
            'project_type_id' => $projectType->id,
            'project_overview' => $projectName . ' overview',
            'pdf_path' => 'projects/' . strtolower(str_replace(' ', '-', $projectName)) . '.pdf',
            'is_active' => true,
        ]);

        return [$project, $college, $major];
    }
}
