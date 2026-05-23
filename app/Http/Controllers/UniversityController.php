<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UniversityController extends Controller
{
  /**
   * عرض قائمة الجامعات
   */
  public function index(Request $request): Response
  {
    $query = University::query()
      ->where('status', 'approved')
      ->with(['street:id,name', 'universityMajors:university_id,tuition_fee']);

    // Filtering
    if ($request->has('location')) {
      $query->where('location', 'like', '%' . $request->location . '%');
    }

    if ($request->has('type')) {
      $query->where('type', $request->type);
    }

    if ($request->has('search')) {
      $query->where(function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%')
          ->orWhere('description', 'like', '%' . $request->search . '%');
      });
    }

    $universities = $query->get()->map(function (University $university) {
      $minFee = $university->universityMajors
        ->pluck('tuition_fee')
        ->filter(fn($fee) => $fee !== null)
        ->map(fn($fee) => (float) $fee)
        ->min();

      $location = $university->location ?: (string) $university->address;
      $description = $university->description ?: '';

      return [
        'id' => $university->public_id,
        'name' => $university->name,
        'location' => $location,
        'rating' => (float) $university->starAvg(),
        'fees' => $minFee ?? 0,
        'image' => $this->fileUrl($university->image_background ?: $university->image_path ?: $university->avatar_url) ?? '',
        'logo' => $this->fileUrl($university->avatar_url ?: $university->image_path) ?? '',
        'description' => $description,
        'streetName' => $university->street?->name,
      ];
    })->values();

    return Inertia::render('Universities', [
      'universities' => $universities,
    ]);
  }

  /**
   * عرض تفاصيل جامعة معينة
   */
  public function show(Request $request, University $university): Response
  {
    $university->load([
      'street:id,name',
      'images' => function ($query) {
        $query->where('is_active', true)->orderBy('priority');
      },

      'universityMajors' => function ($query) {
        $query->where('published', true);
      },
      'universityMajors.major.college',

      //   'universityPosts' => function ($query) {
      //   $query->where('university_id',$university->id)->orderBy('created_at', 'desc');
      // },
    ]);

    $minFee = $university->universityMajors
      ->pluck('tuition_fee')
      ->filter(fn($fee) => $fee !== null)
      ->map(fn($fee) => (float) $fee)
      ->min();

    $location = $university->location ?: (string) $university->address;
    $description = $university->description ?: '';
    $averageRating = (float) $university->starAvg();
    $ratingCount = (int) $university->starCount();

    $userRating = null;
    $user = Auth::user();
    if ($user instanceof \Illuminate\Database\Eloquent\Model) {
      $userRating = $university->getRatedValue($user);
    }

    $uni = [
      'id' => $university->public_id,
      'name' => $university->name,
      'location' => $location,
      'rating' => $averageRating,
      'ratingCount' => $ratingCount,
      'userRating' => $userRating,
      'fees' => $minFee ?? 0,
      'image' => $this->fileUrl($university->image_background ?: $university->image_path ?: $university->avatar_url) ?? '',
      'logo' => $this->fileUrl($university->avatar_url ?: $university->image_path) ?? '',
      'description' => $description,
      'streetName' => $university->street?->name,
      'landmark' => null,
      'mapUrl' => null,
    ];

    $colleges = $university->universityMajors
      ->filter(fn($um) => $um->major && $um->major->college)
      ->groupBy(fn($um) => $um->major->college->id)
      ->map(function ($groupedMajors) {
        $college = $groupedMajors->first()->major->college;

        return [
          'id' => $college->public_id,
          'name' => $college->name,
          'image' => $this->fileUrl($college->image_path) ?? '',
          'majors' => $groupedMajors->map(function ($universityMajor) {
            $major = $universityMajor->major;
            $jobs = $major->designation_jobs;
            $career = is_string($jobs) ? json_decode($jobs, true) : $jobs;

            if (!is_array($career)) {
              $career = array_filter(array_map('trim', explode(',', (string) $jobs)));
            }

return [
    'id' => $universityMajor->public_id,   // ✅ كان: $major->public_id
    'name' => $major->name,
    'description' => $major->description,
    'years' => (int) ($universityMajor->study_years ?? $major->study_years ?? 0),
    'fees' => (float) ($universityMajor->tuition_fee ?? 0),
    'gpa' => (float) ($universityMajor->admission_rate ?? 0),
    'careerOpportunities' => array_values($career),
];
          })->values(),
        ];
      })
      ->values();

    $majors = $colleges
      ->flatMap(fn($college) => $college['majors'])
      ->values();

    $universityProjects = Project::query()
      ->where('is_active', true)
      ->whereHas('superUniverMajor.universityMajor', function ($query) use ($university) {
        $query->where('university_id', $university->id);
      })
      ->with([
        'projectType:id,name',
        'students:id,F_name,S_name,Th_name,Su_name,project_id',
        'superUniverMajor.supervisor:id,F_name,S_name,Th_name,Su_name',
        'superUniverMajor.universityMajor.university:id,public_id,name',
        'superUniverMajor.universityMajor.major:id,public_id,name,description,college_id',
        'superUniverMajor.universityMajor.major.college:id,public_id,name',
      ])
      ->latest()
      ->get()
      ->map(function (Project $project) {
        $universityMajor = $project->superUniverMajor?->universityMajor;
        $projectUniversity = $universityMajor?->university;
        $major = $universityMajor?->major;
        $college = $major?->college;
        $supervisor = $project->superUniverMajor?->supervisor;
        $overview = (string) ($project->project_overview ?? '');

        $memberNames = $project->students
          ->map(fn($student) => trim(implode(' ', array_filter([
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
          'universityId' => $projectUniversity?->public_id ?? '',
          'universityName' => $projectUniversity?->name ?? '',
          'collegeName' => $college?->name ?? '',
          'majorName' => $major?->name ?? '',
          'supervisorName' => $supervisorName,
          'memberNames' => $memberNames,
          'date' => optional($project->created_at)->toDateString() ?? '',
          'projectType' => $project->projectType?->name ?? '',
          'projectname' => $project->name,
          'description' => Str::limit($overview, 280),
          'pdfLink' => $this->fileUrl($project->pdf_path) ?? '',
        ];
      })
      ->values();

    return Inertia::render('UniversityDetails', [
      'uni' => $uni,
      'universityProjects' => $universityProjects,
      'colleges' => $colleges,
      'majors' => $majors,
    ]);
  }
  /**
   * Resolve storage or absolute URLs for media fields.
   */
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
}

