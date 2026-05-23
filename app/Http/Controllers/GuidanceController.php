<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Major;
use App\Models\University;
use App\Models\UniversityMajor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GuidanceController extends Controller
{
    public function guidance(): Response
    {
        [$majors, $majorStats, $publishedMajorIds] = $this->buildMajorData();

        $colleges = College::query()
            ->whereIn('id', $majors->pluck('collegeId')->unique()->values())
            ->get()
            ->map(function (College $college) {
                return [
                    'id' => (string) $college->id,
                    'name' => $college->name,
                    'image' => $this->fileUrl($college->image_path) ?? '',
                ];
            })
            ->values();

        $universities = University::query()
            ->where('status', 'approved')
            ->with([
                'street:id,name',
                'universityMajors' => function ($query) use ($publishedMajorIds) {
                    $query->where('published', true)
                        ->whereIn('major_id', $publishedMajorIds)
                        ->select('id', 'university_id', 'major_id', 'tuition_fee', 'study_years', 'admission_rate');
                },
            ])
            ->get()
            ->map(function (University $university) {
                return [
                    'id' => $university->public_id,
                    'name' => $university->name,
                    'location' => $university->location ?: (string) $university->address,
                    'rating' => (float) $university->starAvg(),
                    'image' => $this->fileUrl($university->image_background ?: $university->image_path ?: $university->avatar_url) ?? '',
                    'logo' => $this->fileUrl($university->avatar_url ?: $university->image_path) ?? '',
                    'streetName' => $university->street?->name,
                    'majors' => $university->universityMajors
                        ->pluck('major_id')
                        ->map(fn ($id) => (string) $id)
                        ->values(),
                    'major_offerings' => $university->universityMajors
                        ->map(function ($offering) {
                            return [
                                'major_id' => (string) $offering->major_id,
                                'fees' => (float) ($offering->tuition_fee ?? 0),
                                'study_years' => (int) ($offering->study_years ?? 0),
                                'admission_rate' => $offering->admission_rate !== null ? (float) $offering->admission_rate : null,
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();

        return Inertia::render('Guidance', [
            'universities' => $universities,
            'colleges' => $colleges,
            'majors' => $majors->values(),
        ]);
    }

    public function compare(): Response
    {
        [$majors] = $this->buildMajorData();

        return Inertia::render('Compare', [
            'majors' => $majors->values(),
        ]);
    }

    /**
     * Build majors and stats from published university offerings.
     *
     * @return array{0:\Illuminate\Support\Collection,1:\Illuminate\Support\Collection,2:array<int,int>}
     */
    private function buildMajorData(): array
    {
        $publishedUniversityMajors = UniversityMajor::query()
            ->where('published', true)
            ->whereHas('university', function ($query) {
                $query->where('status', 'approved');
            })
            ->get(['major_id', 'tuition_fee', 'admission_rate', 'study_years']);

        $publishedMajorIds = $publishedUniversityMajors
            ->pluck('major_id')
            ->unique()
            ->values()
            ->all();

        $majorStats = $publishedUniversityMajors
            ->groupBy('major_id')
            ->map(function ($items) {
                $fees = $items->pluck('tuition_fee')
                    ->filter(fn ($fee) => $fee !== null)
                    ->map(fn ($fee) => (float) $fee);

                $gpas = $items->pluck('admission_rate')
                    ->filter(fn ($rate) => $rate !== null)
                    ->map(fn ($rate) => (float) $rate);

                $years = $items->pluck('study_years')
                    ->filter(fn ($year) => $year !== null)
                    ->map(fn ($year) => (int) $year);

                return [
                    'fees' => $fees->min() ?? 0,
                    'gpa' => $gpas->min() ?? 0,
                    'years' => $years->max() ?? 0,
                ];
            });

        $majors = Major::query()
            ->whereIn('id', $publishedMajorIds)
            ->get()
            ->map(function (Major $major) use ($majorStats) {
                $jobs = $major->designation_jobs;
                $career = is_string($jobs) ? json_decode($jobs, true) : $jobs;
                if (!is_array($career)) {
                    $career = array_filter(array_map('trim', explode(',', (string) $jobs)));
                }

                $stats = $majorStats->get($major->id, [
                    'fees' => 0,
                    'gpa' => 0,
                    'years' => 0,
                ]);

                return [
                    'id' => (string) $major->id,
                    'publicId' => $major->public_id,
                    'name' => $major->name,
                    'collegeId' => (string) $major->college_id,
                    'description' => $major->description ?? '',
                    'gpa' => (float) $stats['gpa'],
                    'fees' => (float) $stats['fees'],
                    'years' => (int) ($stats['years'] ?: ($major->study_years ?? 0)),
                    'careerOpportunities' => array_values($career),
                ];
            });

        return [$majors, $majorStats, $publishedMajorIds];
    }

    private function fileUrl(?string $path): ?string
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
