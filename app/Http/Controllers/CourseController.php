<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Department;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        $query = Course::with('lecturer');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        // Filter by semester
        if ($request->has('semester') && $request->semester) {
            $query->where('semester', $request->semester);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by lecturer
        if ($request->has('lecturer_id') && $request->lecturer_id) {
            $query->where('lecturer_id', $request->lecturer_id);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $departments = Department::active()
                                ->orderBy('name')
                                ->pluck('name', 'name');

        $lecturers = User::where('role', 'lecturer')
                        ->orderBy('name')
                        ->get();

        return view('admin.courses.index', compact('courses', 'departments', 'lecturers'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        return view('admin.courses.create', compact('lecturers', 'departments'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses',
            'description' => 'nullable|string|max:1000',
            'credits' => 'required|integer|min:1|max:10',
            'semester' => 'required|in:First,Second',
            'level' => 'required|integer|min:100|max:800',
            'department' => 'required|string|max:100',
            'lecturer_id' => 'required|exists:users,id',
        ]);

        // Verify the lecturer_id belongs to a lecturer
        $lecturer = User::where('id', $validated['lecturer_id'])
                       ->where('role', 'lecturer')
                       ->first();

        if (!$lecturer) {
            return back()->withErrors(['lecturer_id' => 'Selected user is not a lecturer.']);
        }

        Course::create($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        $course->load(['lecturer', 'students', 'assignments', 'materials']);

        // Get enrollment statistics
        $enrollmentStats = [
            'total_students' => $course->students()->count(),
            'total_assignments' => $course->assignments()->count(),
            'total_materials' => $course->materials()->count(),
        ];

        return view('admin.courses.show', compact('course', 'enrollmentStats'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'lecturers'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:20', Rule::unique('courses')->ignore($course->id)],
            'description' => 'nullable|string|max:1000',
            'credits' => 'required|integer|min:1|max:10',
            'semester' => 'required|in:First,Second',
            'level' => 'required|integer|min:100|max:800',
            'department' => 'required|string|max:100',
            'lecturer_id' => 'required|exists:users,id',
        ]);

        // Verify the lecturer_id belongs to a lecturer
        $lecturer = User::where('id', $validated['lecturer_id'])
                       ->where('role', 'lecturer')
                       ->first();

        if (!$lecturer) {
            return back()->withErrors(['lecturer_id' => 'Selected user is not a lecturer.']);
        }

        $course->update($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        // Check if course has enrolled students
        if ($course->students()->count() > 0) {
            return redirect()->route('admin.courses.index')
                            ->with('error', 'Cannot delete course with enrolled students!');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course deleted successfully!');
    }

    /**
     * Bulk delete courses
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);

        $courseIds = $request->course_ids;

        // Check for courses with enrolled students
        $coursesWithStudents = Course::whereIn('id', $courseIds)
                                   ->withCount('students')
                                   ->having('students_count', '>', 0)
                                   ->count();

        if ($coursesWithStudents > 0) {
            return redirect()->route('admin.courses.index')
                            ->with('error', 'Cannot delete courses that have enrolled students!');
        }

        Course::whereIn('id', $courseIds)->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', count($courseIds) . ' courses deleted successfully!');
    }

    /**
     * Lecturer-specific course management
     */
    public function lecturerIndex()
    {
        $lecturer = auth()->user();
        $courses = $lecturer->courses()
                           ->withCount(['students', 'assignments', 'materials'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        return view('lecturer.courses.index', compact('courses'));
    }

    /**
     * Show course for lecturer
     */
    public function lecturerShow(Course $course)
    {
        // Ensure lecturer can only view their own courses
        if ($course->lecturer_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this course.');
        }

        $course->load(['students', 'assignments', 'materials']);

        $enrollmentStats = [
            'total_students' => $course->students()->count(),
            'total_assignments' => $course->assignments()->count(),
            'total_materials' => $course->materials()->count(),
        ];

        return view('lecturer.courses.show', compact('course', 'enrollmentStats'));
    }
}
