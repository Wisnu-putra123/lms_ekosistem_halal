<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan data $studentCourses ke file sidebar/layout
        View::composer(['layouts.app', 'layouts.sidebar'], function ($view) {
            if (Auth::check() && Auth::user()->role === 'student') {
                // Ambil daftar course yang sedang diikuti (active)
                $studentCourses = Enrollment::with('course')
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->get()
                    ->pluck('course');

                $view->with('studentCourses', $studentCourses);
            }
        });
    }
}
