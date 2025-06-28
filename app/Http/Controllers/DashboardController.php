<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Enrollment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard();
            case 'lecturer':
                return $this->lecturerDashboard();
            case 'student':
                return $this->studentDashboard();
            default:
                return view('dashboard.general');
        }
    }

    public function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_lecturers' => User::where('role', 'lecturer')->count(),
            'total_courses' => Course::count(),
            'total_assignments' => Assignment::count(),
            'total_enrollments' => Enrollment::count(),
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_courses = Course::with('lecturer')->latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recent_users', 'recent_courses'));
    }

    public function lecturerDashboard()
    {
        $lecturer = auth()->user();

        $stats = [
            'my_courses' => $lecturer->courses()->count(),
            'total_students' => $lecturer->courses()->withCount('students')->get()->sum('students_count'),
            'total_assignments' => $lecturer->courses()->withCount('assignments')->get()->sum('assignments_count'),
        ];

        $my_courses = $lecturer->courses()->withCount(['students', 'assignments'])->latest()->get();

        return view('dashboard.lecturer', compact('stats', 'my_courses'));
    }

    public function studentDashboard()
    {
        $student = auth()->user();

        $enrolled_courses = $student->enrolledCourses()->withCount('assignments')->get();
        $recent_assignments = Assignment::whereIn('course_id', $enrolled_courses->pluck('id'))
            ->with('course')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'enrolled_courses' => $enrolled_courses->count(),
            'pending_assignments' => $recent_assignments->count(),
        ];

        return view('dashboard.student', compact('stats', 'enrolled_courses', 'recent_assignments'));
    }
}
