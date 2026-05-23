<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUniversityStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('university')->user();

if ($user instanceof \App\Models\University && $user->status !== 'approved') {
    Auth::guard('university')->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/university/login')
        ->withErrors(['email' => 'حسابك قيد المراجعة أو مرفوض من الإدارة.']);
}

        return $next($request);
    }

  }
