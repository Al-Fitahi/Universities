<?php

use App\Http\Controllers\CollegeController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Rate\UniversityRatingController;
use App\Http\Controllers\UniversityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});





// Use controller for listing universities
Route::get('/universities', [UniversityController::class, 'index'])->name('universities');
Route::get('/universities/{university}', [UniversityController::class, 'show'])->name('universities.show');
Route::get('/universities/{university}/projects', [UniversityController::class, 'projects'])->name('universities.projects');
Route::get('/colleges', [CollegeController::class, 'index'])->name('colleges');
Route::get('/projects/suggestions', [ProjectController::class, 'suggestions'])->name('projects.suggestions');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
Route::get('/guidance', [GuidanceController::class, 'guidance'])->name('guidance');
Route::get('/compare', [GuidanceController::class, 'compare'])->name('compare');
Route::get('/about', fn () => Inertia::render('About'))->name('about');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');
Route::middleware(['auth:web'])->group(function () {
    Route::post('/universities/{university}/rate', [UniversityRatingController::class, 'store'])->name('universities.rate');
    Route::get('/apply/{university}', [ApplicationController::class, 'create'])->name('apply.create');
    Route::post('/apply', [ApplicationController::class, 'store'])->name('apply.store');
});





// (duplicate removed) controller index is defined above

require __DIR__.'/settings.php';

// Allow GET requests to Boost browser logs endpoint to avoid MethodNotAllowed
// The vendor package registers POST at the same path; this GET handler prevents accidental GET errors.
// Route::get('/_boost/browser-logs', function () {
//     return response()->json(['status' => 'ok']);
// });
