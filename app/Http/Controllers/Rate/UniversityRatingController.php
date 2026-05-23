<?php

namespace App\Http\Controllers\Rate;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityRatingController extends Controller
{
    public function store(Request $request, University $university)
    {
      $request->validate([
        'rating' => ['required', 'numeric', 'min:1', 'max:5'],
      ]);

      $user = $request->user('web');
      if (!$user instanceof User) {
        return back()->with('error', 'Only regular users can rate universities.');
      }

      $university->addStar(
        (float) $request->input('rating'),
        $user,
        [
          'ip' => $request->ip(),
          'source' => 'web',
        ]
      );
      return back()->with('success', 'Thank you for rating this university!');
    }
}
