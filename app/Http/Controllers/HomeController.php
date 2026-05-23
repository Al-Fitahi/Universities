<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\University;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
   /**
    * عرض الصفحة الرئيسية
    */
   public function __invoke(): Response
   {
           $universities = University::query()
               ->where('status', 'approved')
               ->with(['universityMajors' => function ($query) {
                   $query->select('university_id', 'tuition_fee');
               }])
               ->limit(6)
               ->get()
               ->map(function (University $university) {
                   $minFee = $university->universityMajors
                       ->pluck('tuition_fee')
                       ->filter(fn ($fee) => $fee !== null)
                       ->map(fn ($fee) => (float) $fee)
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
                   ];
               })
               ->values();

           $graduationProjects = Project::query()
               ->where('is_active', true)
               ->with([
                   'projectType:id,name',
                   'superUniverMajor.universityMajor.university:id,public_id,name,location,address',
                   'superUniverMajor.universityMajor.major:id,name,description,college_id',
                   'superUniverMajor.universityMajor.major.college:id,name',
               ])
               ->latest()
               ->limit(6)
               ->get()
               ->map(function (Project $project) {
                   $universityMajor = $project->superUniverMajor?->universityMajor;
                   $university = $universityMajor?->university;
                   $major = $universityMajor?->major;
                   $college = $major?->college;
                   $projectType = $project->projectType;
                   $overview = (string) ($project->project_overview ?? '');

                   return [
                       'id' => $project->public_id,
                       'name' => $project->name,
                       'universityId' => $university?->public_id ?? '',
                       'universityName' => $university?->name ?? '',
                       'collegeName' => $college?->name ?? '',
                       'majorName' => $major?->name ?? '',
                       'date' => optional($project->created_at)->toDateString() ?? '',
                       'projectType' => $projectType?->name ?? '',
                       'description' => Str::limit($overview, 280),
                   ];
               })
               ->values();

       return Inertia::render('Home', [
               'universities' => $universities,
               'graduationProjects' => $graduationProjects,
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

