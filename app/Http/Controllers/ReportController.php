<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Department;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Get overview statistics
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_lecturers' => User::where('role', 'lecturer')->count(),
            'total_courses' => Course::count(),
            'total_departments' => Department::count(),
            'total_assignments' => Assignment::count(),
            'total_enrollments' => Enrollment::count(),
            'total_submissions' => Submission::count(),
        ];

        // Get monthly user registrations for the last 6 months
        $monthlyUsers = User::selectRaw(
            "strftime('%Y', created_at) as year, strftime('%m', created_at) as month, COUNT(*) as count"
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Get course enrollment statistics
        $courseEnrollments = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get();

        // Get department statistics
        $departmentStats = Department::withCount(['courses', 'users'])
            ->get();

        // Get recent activity (last 30 days)
        $recentActivity = [
            'new_users' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'new_courses' => Course::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'new_enrollments' => Enrollment::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'new_assignments' => Assignment::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
        ];

        return view('admin.reports.index', compact(
            'stats',
            'monthlyUsers',
            'courseEnrollments',
            'departmentStats',
            'recentActivity'
        ));
    }

    public function users()
    {
        // User statistics by role
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();

        // User registrations by month (last 12 months)
        $userRegistrations = User::selectRaw(
            "strftime('%Y-%m', created_at) as month, COUNT(*) as count"
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Active users (users who have logged in recently)
        $activeUsers = User::where('updated_at', '>=', Carbon::now()->subDays(30))->count();

        // Users by department
        $usersByDepartment = User::select('department', DB::raw('count(*) as count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->get();

        return view('admin.reports.users', compact(
            'usersByRole',
            'userRegistrations',
            'activeUsers',
            'usersByDepartment'
        ));
    }

    public function courses()
    {
        // Course statistics
        $courseStats = [
            'total_courses' => Course::count(),
            'courses_with_enrollments' => Course::has('enrollments')->count(),
            'courses_without_enrollments' => Course::doesntHave('enrollments')->count(),
            'average_enrollments' => round(Enrollment::count() / max(Course::count(), 1), 2),
        ];

        // Courses by department
        $coursesByDepartment = Course::select('department', DB::raw('count(*) as count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->get();

        // Top enrolled courses
        $topCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get();

        // Course creation over time
        $courseCreation = Course::selectRaw(
            "strftime('%Y-%m', created_at) as month, COUNT(*) as count"
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('admin.reports.courses', compact(
            'courseStats',
            'coursesByDepartment',
            'topCourses',
            'courseCreation'
        ));
    }

    public function enrollments()
    {
        // Enrollment statistics
        $enrollmentStats = [
            'total_enrollments' => Enrollment::count(),
            'enrollments_this_month' => Enrollment::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'enrollments_last_month' => Enrollment::whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])->count(),
        ];

        // Enrollment trends
        $enrollmentTrends = Enrollment::selectRaw(
            "strftime('%Y-%m', created_at) as month, COUNT(*) as count"
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Enrollments by course
        $enrollmentsByCourse = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(15)
            ->get();

        return view('admin.reports.enrollments', compact(
            'enrollmentStats',
            'enrollmentTrends',
            'enrollmentsByCourse'
        ));
    }

    public function assignments()
    {
        // Assignment statistics
        $assignmentStats = [
            'total_assignments' => Assignment::count(),
            'assignments_this_month' => Assignment::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'upcoming_assignments' => Assignment::where('due_date', '>', Carbon::now())->count(),
            'overdue_assignments' => Assignment::where('due_date', '<', Carbon::now())->count(),
        ];

        // Assignments by course
        $assignmentsByCourse = Course::withCount('assignments')
            ->orderBy('assignments_count', 'desc')
            ->limit(10)
            ->get();

        // Assignment creation trends
        $assignmentTrends = Assignment::selectRaw(
            "strftime('%Y-%m', created_at) as month, COUNT(*) as count"
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('admin.reports.assignments', compact(
            'assignmentStats',
            'assignmentsByCourse',
            'assignmentTrends'
        ));
    }
}
