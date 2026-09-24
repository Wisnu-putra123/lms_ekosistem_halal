<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user = Auth::user();
        if ($user->role === 'admin') {
                    return view('admin.index');
                } elseif ($user->role === 'teacher') {
                    return view('teacher.index');
                } else {
                    return view('student.index');
                }
    }
}
