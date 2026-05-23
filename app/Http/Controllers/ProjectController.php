<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function suggestions(Request $request)
    {
        $suggestions = Project::query()
            ->where('is_active', true)
            ->with([
                'superUniverMajor.universityMajor.university:id,public_id,name',
                'projectType:id,public_id,name',
                'students:id,F_name,S_name,Th_name,Su_name,project_id',
                'superUniverMajor.supervisor:id,F_name,S_name,Th_name,Su_name',
                'superUniverMajor.universityMajor.major:id,public_id,name,description,college_id',
                'superUniverMajor.universityMajor.major.college:id,public_id,name',
            ])
            ->latest()
            ->get()
            ->map(function (Project $project) {
                $university = $project->superUniverMajor?->universityMajor?->university;

                return [
                    'value' => $project->name,
                    'label' => $project->name,
                    'meta' => $university?->name ?? '',
                    'project' => $this->projectSummary($project),
                ];
            })
            ->unique('value')
            ->values();

        return response()->json($suggestions);
    }

    public function index(Request $request): Response
    {
        $typeFilter = trim((string) $request->query('type', ''));
        $searchFilter = trim((string) $request->query('search', ''));
        $perPage = min(max((int) $request->query('per_page', 12), 6), 24);

        $projectTypes = ProjectType::query()
            ->select(['public_id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn (ProjectType $type) => [
                'id' => $type->public_id,
                'name' => $type->name,
            ])
            ->values();

        $paginator = Project::query()
            ->where('is_active', true)
            ->when($typeFilter !== '', function ($query) use ($typeFilter) {
                $query->whereHas('projectType', function ($projectTypeQuery) use ($typeFilter) {
                    $projectTypeQuery->where('public_id', $typeFilter);
                });
            })
            ->when($searchFilter !== '', function ($query) use ($searchFilter) {
                $query->where('name', 'like', '%'.$searchFilter.'%');
            })
            ->with([
                'projectType:id,public_id,name',
                'students:id,F_name,S_name,Th_name,Su_name,project_id',
                'superUniverMajor.supervisor:id,F_name,S_name,Th_name,Su_name',
                'superUniverMajor.universityMajor.university:id,public_id,name',
                'superUniverMajor.universityMajor.major:id,public_id,name,description,college_id',
                'superUniverMajor.universityMajor.major.college:id,public_id,name',
            ])
            ->latest()
            ->simplePaginate($perPage)
            ->withQueryString();

        $graduationProjects = collect($paginator->items())
            ->map(fn (Project $project) => $this->projectSummary($project))
            ->values();

        return Inertia::render('Projects', [
            'graduationProjects' => $graduationProjects,
            'projectTypes' => $projectTypes,
            'filters' => [
                'type' => $typeFilter,
                'search' => $searchFilter,
            ],
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'hasMorePages' => $paginator->hasMorePages(),
                'nextPage' => $paginator->currentPage() + 1,
                'perPage' => $paginator->perPage(),
            ],
        ]);
    }

    protected function fileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::url($path);
    }

    protected function projectSummary(Project $project): array
    {
        $universityMajor = $project->superUniverMajor?->universityMajor;
        $university = $universityMajor?->university;
        $major = $universityMajor?->major;
        $college = $major?->college;
        $supervisor = $project->superUniverMajor?->supervisor;
        $overview = (string) ($project->project_overview ?? '');

        $memberNames = $project->students
            ->map(fn ($student) => trim(implode(' ', array_filter([
                $student->F_name,
                $student->S_name,
                $student->Th_name,
                $student->Su_name,
            ]))))
            ->filter()
            ->values()
            ->toArray();

        $supervisorName = trim(implode(' ', array_filter([
            $supervisor?->F_name,
            $supervisor?->S_name,
            $supervisor?->Th_name,
            $supervisor?->Su_name,
        ])));

        return [
            'id' => $project->public_id,
            'name' => $project->name,
            'universityId' => $university?->public_id ?? '',
            'universityName' => $university?->name ?? '',
            'collegeName' => $college?->name ?? '',
            'majorName' => $major?->name ?? '',
            'supervisorName' => $supervisorName,
            'memberNames' => $memberNames,
            'date' => optional($project->created_at)->toDateString() ?? '',
            'projectType' => $project->projectType?->name ?? '',
            'description' => Str::limit($overview, 280),
            'pdfLink' => $this->fileUrl($project->pdf_path) ?? '',
        ];
    }
}

