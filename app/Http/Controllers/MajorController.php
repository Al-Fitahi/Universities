<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MajorController extends Controller
{
   /**
    * عرض قائمة التخصصات
    */
   public function index(Request $request): Response
   {
       $query = Major::with('college');

       // Filtering
       if ($request->has('search')) {
           $query->where('name', 'like', '%' . $request->search . '%');
       }

       if ($request->has('college_id')) {
           $query->where('college_id', $request->college_id);
       }

       $majors = $query->get()->map(function ($major) {
           return [
               'public_id' => $major->public_id,
               'name' => $major->name,
               'description' => $major->description,
               'designation_jobs' => $major->designation_jobs,
               'study_years' => $major->study_years,
               'college' => [
                   'public_id' => $major->college->public_id,
                   'name' => $major->college->name,
                   'description' => $major->college->description,
                   'image_path' => $major->college->image_path,
               ],
           ];
       });

       return Inertia::render('Majors', [
           'majorsData' => $majors,
       ]);
   }
}
