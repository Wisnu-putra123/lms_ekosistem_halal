<?php

namespace App\Http\Middleware;

use App\Models\Assignment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAssignmentOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil parameter assignment dari route (bisa berupa Model Binding atau ID)
        $assignment = $request->route('assignment');

        if (!$assignment instanceof Assignment) {
            $assignment = Assignment::find($assignment);
        }

        // Jika data assignment ditemukan dan memiliki tenggat waktu
        if ($assignment && $assignment->available_until) {
            if (now()->greaterThan($assignment->available_until)) {
                return redirect()->back()->with('error', 'Tenggat waktu pengerjaan tugas/kuis ini telah berakhir.');
            }
        }

        return $next($request);
    }
}