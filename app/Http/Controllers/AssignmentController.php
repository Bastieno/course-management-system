<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assignment::with(['course', 'submissions']);

        // If lecturer, only show assignments for their courses
        if (auth()->user()->role === 'lecturer') {
            $lecturerCourseIds = Course::where('lecturer_id', auth()->id())->pluck('id');
            $query->whereIn('course_id', $lecturerCourseIds);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'overdue':
                    $query->overdue();
                    break;
            }
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $assignments = $query->orderBy('due_date', 'desc')->paginate(15);

        // Get courses for filter dropdown (lecturer only sees their courses)
        if (auth()->user()->role === 'lecturer') {
            $courses = Course::where('lecturer_id', auth()->id())->orderBy('title')->get();
        } else {
            $courses = Course::orderBy('title')->get();
        }

        // Get statistics (lecturer only sees their stats)
        if (auth()->user()->role === 'lecturer') {
            $lecturerCourseIds = Course::where('lecturer_id', auth()->id())->pluck('id');
            $stats = [
                'total_assignments' => Assignment::whereIn('course_id', $lecturerCourseIds)->count(),
                'upcoming_assignments' => Assignment::whereIn('course_id', $lecturerCourseIds)->upcoming()->count(),
                'overdue_assignments' => Assignment::whereIn('course_id', $lecturerCourseIds)->overdue()->count(),
                'total_submissions' => Submission::whereHas('assignment', function($q) use ($lecturerCourseIds) {
                    $q->whereIn('course_id', $lecturerCourseIds);
                })->count(),
                'graded_submissions' => Submission::whereHas('assignment', function($q) use ($lecturerCourseIds) {
                    $q->whereIn('course_id', $lecturerCourseIds);
                })->graded()->count(),
            ];
        } else {
            $stats = [
                'total_assignments' => Assignment::count(),
                'upcoming_assignments' => Assignment::upcoming()->count(),
                'overdue_assignments' => Assignment::overdue()->count(),
                'total_submissions' => Submission::count(),
                'graded_submissions' => Submission::graded()->count(),
            ];
        }

        // Determine view based on user role
        $view = auth()->user()->role === 'lecturer' ? 'admin.assignments.index' : 'admin.assignments.index';

        return view($view, compact('assignments', 'courses', 'stats'));
    }

    public function create()
    {
        // If lecturer, only show their courses
        if (auth()->user()->role === 'lecturer') {
            $courses = Course::where('lecturer_id', auth()->id())->orderBy('title')->get();
        } else {
            $courses = Course::orderBy('title')->get();
        }

        return view('admin.assignments.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:now',
            'points' => 'required|integer|min:1|max:1000',
        ]);

        Assignment::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'points' => $request->points,
        ]);

        // Redirect based on user role
        $route = auth()->user()->role === 'lecturer' ? 'lecturer.assignments.index' : 'admin.assignments.index';

        return redirect()->route($route)
            ->with('success', 'Assignment created successfully.');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['course', 'submissions.student']);

        // Get submission statistics
        $submissionStats = [
            'total_submissions' => $assignment->submissions->count(),
            'graded_submissions' => $assignment->submissions->where('grade', '!=', null)->count(),
            'average_score' => $assignment->submissions->where('grade', '!=', null)->avg('grade'),
            'late_submissions' => $assignment->submissions->filter(function ($submission) use ($assignment) {
                return $submission->submitted_at > $assignment->due_date;
            })->count(),
        ];

        // Get grade distribution
        $gradeDistribution = $assignment->submissions
            ->where('grade', '!=', null)
            ->groupBy(function ($submission) {
                return $submission->getLetterGrade();
            })
            ->map(function ($group) {
                return $group->count();
            });

        return view('admin.assignments.show', compact('assignment', 'submissionStats', 'gradeDistribution'));
    }

    public function edit(Assignment $assignment)
    {
        // If lecturer, only show their courses
        if (auth()->user()->role === 'lecturer') {
            $courses = Course::where('lecturer_id', auth()->id())->orderBy('title')->get();
        } else {
            $courses = Course::orderBy('title')->get();
        }

        return view('admin.assignments.edit', compact('assignment', 'courses'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'points' => 'required|integer|min:1|max:1000',
        ]);

        $assignment->update([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'points' => $request->points,
        ]);

        // Redirect based on user role
        $route = auth()->user()->role === 'lecturer' ? 'lecturer.assignments.index' : 'admin.assignments.index';

        return redirect()->route($route)
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        // Redirect based on user role
        $route = auth()->user()->role === 'lecturer' ? 'lecturer.assignments.index' : 'admin.assignments.index';

        return redirect()->route($route)
            ->with('success', 'Assignment deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'exists:assignments,id',
        ]);

        Assignment::whereIn('id', $request->assignment_ids)->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', count($request->assignment_ids) . ' assignments deleted successfully.');
    }

    public function submissions(Request $request, Assignment $assignment)
    {
        $query = $assignment->submissions()->with('student');

        // Search by student name
        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'graded':
                    $query->whereNotNull('grade');
                    break;
                case 'pending':
                    $query->whereNull('grade');
                    break;
                case 'late':
                    $query->where('submitted_at', '>', $assignment->due_date);
                    break;
            }
        }

        // Sort submissions
        $sortBy = $request->get('sort', 'submitted_at');
        switch ($sortBy) {
            case 'student_name':
                $query->join('users', 'submissions.student_id', '=', 'users.id')
                      ->orderBy('users.name');
                break;
            case 'grade':
                $query->orderBy('grade', 'desc');
                break;
            default:
                $query->orderBy('submitted_at', 'desc');
                break;
        }

        $submissions = $query->paginate(15)->appends($request->query());

        return view('admin.assignments.submissions', compact('assignment', 'submissions'));
    }

    public function gradeSubmission(Request $request, Assignment $assignment, Submission $submission)
    {
        $request->validate([
            'grade' => 'required|integer|min:0|max:' . $assignment->points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
            'graded_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Submission graded successfully.');
    }

    public function analytics()
    {
        // Overall assignment statistics
        $overallStats = [
            'total_assignments' => Assignment::count(),
            'total_submissions' => Submission::count(),
            'average_submissions_per_assignment' => round(Submission::count() / max(Assignment::count(), 1), 2),
            'grading_completion_rate' => Assignment::count() > 0 ?
                round((Submission::graded()->count() / Submission::count()) * 100, 2) : 0,
        ];

        // Assignment creation trends (last 6 months)
        $assignmentTrends = Assignment::selectRaw(
            "strftime('%Y-%m', created_at) as month, COUNT(*) as count"
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Top courses by assignment count
        $topCoursesByAssignments = Course::withCount('assignments')
            ->orderBy('assignments_count', 'desc')
            ->limit(10)
            ->get();

        // Submission trends (last 6 months)
        $submissionTrends = Submission::selectRaw(
            "strftime('%Y-%m', submitted_at) as month, COUNT(*) as count"
        )
        ->where('submitted_at', '>=', Carbon::now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('admin.assignments.analytics', compact(
            'overallStats',
            'assignmentTrends',
            'topCoursesByAssignments',
            'submissionTrends'
        ));
    }
}
