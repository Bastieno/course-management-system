<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EnrollmentController;

// Redirect root based on authentication status
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        // Redirect based on user role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'lecturer':
                return redirect()->route('lecturer.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // User Management Routes
        Route::resource('users', UserController::class, ['as' => 'admin']);
        Route::post('/users/bulk-delete', [UserController::class, 'bulkArchive'])->name('admin.users.bulk-archive');
        Route::post('/users/{user}/archive', [UserController::class, 'archive'])->name('admin.users.archive');
        Route::post('/users/{user}/unarchive', [UserController::class, 'unarchive'])->name('admin.users.unarchive');

        // Course Management Routes
        Route::resource('courses', CourseController::class, ['as' => 'admin']);
        Route::post('/courses/bulk-delete', [CourseController::class, 'bulkDelete'])->name('admin.courses.bulk-delete');

        // Department Management Routes
        Route::resource('departments', DepartmentController::class, ['as' => 'admin']);
        Route::post('/departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('admin.departments.bulk-delete');
        Route::patch('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('admin.departments.toggle-status');

        // Reports Routes
        Route::prefix('reports')->group(function () {
            Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('admin.reports.index');
            Route::get('/users', [App\Http\Controllers\ReportController::class, 'users'])->name('admin.reports.users');
            Route::get('/courses', [App\Http\Controllers\ReportController::class, 'courses'])->name('admin.reports.courses');
            Route::get('/enrollments', [App\Http\Controllers\ReportController::class, 'enrollments'])->name('admin.reports.enrollments');
            Route::get('/assignments', [App\Http\Controllers\ReportController::class, 'assignments'])->name('admin.reports.assignments');
        });

        // Assignment Management Routes
        Route::resource('assignments', App\Http\Controllers\AssignmentController::class, ['as' => 'admin']);
        Route::post('/assignments/bulk-delete', [App\Http\Controllers\AssignmentController::class, 'bulkDelete'])->name('admin.assignments.bulk-delete');
        Route::get('/assignments/{assignment}/submissions', [App\Http\Controllers\AssignmentController::class, 'submissions'])->name('admin.assignments.submissions');
        Route::post('/assignments/{assignment}/submissions/{submission}/grade', [App\Http\Controllers\AssignmentController::class, 'gradeSubmission'])->name('admin.assignments.grade-submission');
        Route::get('/assignment-analytics', [App\Http\Controllers\AssignmentController::class, 'analytics'])->name('admin.assignments.analytics');

        // Enrollment Management Routes
        Route::get('/enrollments', [EnrollmentController::class, 'adminIndex'])->name('admin.enrollments.index');
        Route::get('/courses/{course}/enrollments', [EnrollmentController::class, 'manageCourse'])->name('admin.enrollments.manage');
        Route::post('/courses/{course}/enrollments', [EnrollmentController::class, 'adminEnroll'])->name('admin.enrollments.enroll');
        Route::delete('/courses/{course}/enrollments/{student}', [EnrollmentController::class, 'adminUnenroll'])->name('admin.enrollments.unenroll');
        Route::post('/courses/{course}/enrollments/bulk', [EnrollmentController::class, 'bulkEnroll'])->name('admin.enrollments.bulk-enroll');
    });

    // Lecturer Routes
    Route::prefix('lecturer')->middleware('role:lecturer')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'lecturerDashboard'])->name('lecturer.dashboard');

        // Assignment Management for Lecturers
        Route::resource('assignments', App\Http\Controllers\AssignmentController::class, ['as' => 'lecturer']);
        Route::get('/assignments/{assignment}/submissions', [App\Http\Controllers\AssignmentController::class, 'submissions'])->name('lecturer.assignments.submissions');
        Route::post('/assignments/{assignment}/submissions/{submission}/grade', [App\Http\Controllers\AssignmentController::class, 'gradeSubmission'])->name('lecturer.assignments.grade-submission');
    });

    // Student Routes
    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('student.dashboard');

        // Course Enrollment Routes
        Route::get('/courses', [EnrollmentController::class, 'index'])->name('student.courses.index');
        Route::get('/courses/{course}', [EnrollmentController::class, 'show'])->name('student.courses.show');
        Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('student.courses.enroll');
        Route::delete('/courses/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('student.courses.unenroll');

        // Assignment Submission Routes
        Route::get('/assignments/{assignment}', [App\Http\Controllers\SubmissionController::class, 'show'])->name('student.assignments.show');
        Route::post('/assignments/{assignment}/submit', [App\Http\Controllers\SubmissionController::class, 'submit'])->name('student.assignments.submit');
        Route::put('/assignments/{assignment}/update', [App\Http\Controllers\SubmissionController::class, 'update'])->name('student.assignments.update');
        Route::delete('/assignments/{assignment}/delete', [App\Http\Controllers\SubmissionController::class, 'destroy'])->name('student.assignments.delete');
    });

    // General Dashboard (fallback)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
