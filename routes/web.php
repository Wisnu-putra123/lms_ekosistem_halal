<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\MeetingController;
use App\Http\Controllers\Teacher\MaterialController;
use App\Http\Controllers\Teacher\SubmissionGradingController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\Teacher\QuestionController;

use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\TaskSubmissionController as StudentTaskSubmissionController;
use App\Http\Controllers\Student\MaterialController as StudentMaterialController;
use App\Http\Controllers\Student\TaskSubmissionController;
use App\Http\Controllers\Student\StudentQuizController;

use Illuminate\Support\Facades\Route;


// Redirect root to home
Route::get('/', function () {
    return redirect()->route('home');
});

// Home Page
Route::get('/home', function () {
    return view('home');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    // User Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // User Registration
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Management Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Protected User Management (CRUD)
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');

        // Material
        Route::get('/materials/{material}', [StudentMaterialController::class, 'show'])->name('materials.show');

        // Rute Submission Student
        Route::get('/submissions/{assignment}', [TaskSubmissionController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{assignment}', [TaskSubmissionController::class, 'store'])->name('submissions.store');
        Route::put('/submissions/{assignment}/{submission}', [TaskSubmissionController::class, 'update'])->name('submissions.update');
        Route::delete('/submissions/{assignment}/{submission}', [TaskSubmissionController::class, 'destroy'])->name('submissions.destroy');

        // Student Quiz Pengerjaan
        Route::get('/quizzes/{assignment}', [StudentQuizController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{assignment}/start', [StudentQuizController::class, 'start'])->name('quizzes.start');
        Route::get('/quizzes/{assignment}/attempts/{attempt}', [StudentQuizController::class, 'attempt'])->name('quizzes.attempt');
        Route::post('/quizzes/{assignment}/attempts/{attempt}/save-answer', [StudentQuizController::class, 'saveAnswer'])->name('quizzes.saveAnswer');
        Route::post('/quizzes/{assignment}/attempts/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quizzes/{assignment}/attempts/{attempt}/result', [StudentQuizController::class, 'result'])->name('quizzes.result');

        // Tugas Upload (Submissions)
        // Route::get('/submissions/{assignment}', [StudentTaskSubmissionController::class, 'show'])->name('submissions.show');
        // Route::post('/submissions/{assignment}/submit', [StudentTaskSubmissionController::class, 'submit'])->name('submissions.submit');

        // // Kuis (Quizzes)
        // Route::get('/quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('quizzes.show');
        // Route::post('/quizzes/{quiz}/start', [StudentQuizController::class, 'start'])->name('quizzes.start');
        // Route::get('/quiz-attempts/{attempt}', [StudentQuizController::class, 'take'])->name('quizzes.take');
        // Route::post('/quiz-attempts/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
    });

    Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/courses', [TeacherCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [TeacherCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [TeacherCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}', [TeacherCourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit', [TeacherCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [TeacherCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [TeacherCourseController::class, 'destroy'])->name('courses.destroy');
        Route::post('/courses/{course}/regenerate-key', [TeacherCourseController::class, 'regenerateKey'])->name('courses.regenerateKey');

        // CRUD Pertemuan (Meetings)
        Route::post('/courses/{course}/meetings', [MeetingController::class, 'store'])->name('courses.meetings.store');
        Route::put('/meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
        Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy');
        
        // Manajement Materi
        Route::get('/meetings/{meeting}/materials/create', [MaterialController::class, 'create'])->name('meetings.materials.create');
        Route::post('/meetings/{meeting}/materials', [MaterialController::class, 'store'])->name('meetings.materials.store');
        Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
        Route::get('/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
        Route::put('/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

        // Management Assignment Submission
        Route::get('/meetings/{meeting}/assignments/create', [AssignmentController::class, 'create'])->name('meetings.assignments.create');
        Route::post('/meetings/{meeting}/assignments', [AssignmentController::class, 'store'])->name('meetings.assignments.store');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        // Penilaian Submission oleh Teacher
        Route::get('/submissions/{submission}/grade', [SubmissionGradingController::class, 'edit'])->name('submissions.grade');
        Route::put('/submissions/{submission}/grade', [SubmissionGradingController::class, 'update'])->name('submissions.update-grade');
        Route::delete('/submissions/{submission}', [SubmissionGradingController::class, 'destroy'])->name('submissions.destroy');

        // Quiz Management
        Route::get('/meetings/{meeting}/quizzes/create', [QuizController::class, 'create'])->name('meetings.quizzes.create');
        Route::post('/meetings/{meeting}/quizzes', [QuizController::class, 'store'])->name('meetings.quizzes.store');
        Route::get('/quizzes/{assignment}', [QuizController::class, 'show'])->name('quizzes.show');
        Route::get('/quizzes/{assignment}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/quizzes/{assignment}', [QuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{assignment}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

        // Bank Soal Management (Questions)
        Route::get('/quizzes/{assignment}/questions/create', [QuestionController::class, 'create'])->name('quizzes.questions.create');
        Route::post('/quizzes/{assignment}/questions', [QuestionController::class, 'store'])->name('quizzes.questions.store');
        Route::get('/quizzes/{assignment}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('quizzes.questions.edit');
        Route::put('/quizzes/{assignment}/questions/{question}', [QuestionController::class, 'update'])->name('quizzes.questions.update');
        Route::delete('/quizzes/{assignment}/questions/{question}', [QuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    });
});

