<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
        // Paksa skema HTTPS saat diakses via Cloudflare Tunnel atau di environment production
        if (str_contains(request()->getHost(), 'trycloudflare.com') || app()->environment('production')) {
            URL::forceScheme('https');
        }

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
