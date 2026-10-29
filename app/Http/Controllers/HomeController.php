<?php

namespace App\Http\Controllers;

use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->where('status', 'published')
            ->latest()
            ->get();

        return view('home', compact('courses'));
    }
}