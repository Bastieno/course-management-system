<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Display available courses for enrollment (Student view)
     */
    public function index()
    {
        $user = Auth::user();

        // Get courses the student is not enrolled in
        $enrolledCourseIds = $user->enrollments()->pluck('course_id');

        $availableCourses = Course::whereNotIn('id', $enrolledCourseIds)
            ->with(['lecturer', 'enrollments'])
            ->get();

        // Get enrolled courses
        $enrolledCourses = $user->enrolledCourses()
            ->withCount('assignments')
            ->get();

        return view('student.courses.index', compact('availableCourses', 'enrolledCourses'));
    }

    /**
     * Show course details for enrollment
     */
    public function show(Course $course)
    {
        $course->load(['lecturer', 'enrollments.student', 'assignments']);

        $isEnrolled = Enrollment::isEnrolled(Auth::id(), $course->id);
        $enrollmentCount = $course->enrollments()->count();

        return view('student.courses.show', compact('course', 'isEnrolled', 'enrollmentCount'));
    }

    /**
     * Enroll student in a course
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        // Check if already enrolled
        if (Enrollment::isEnrolled($user->id, $course->id)) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        // Create enrollment
        Enrollment::create([
            'student_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Successfully enrolled in ' . $course->title);
    }

    /**
     * Unenroll student from a course (with restrictions)
     */
    public function unenroll(Course $course)
    {
        $user = Auth::user();

        $enrollment = Enrollment::where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        // Check if unenrollment is allowed based on enrollment duration
        $enrollmentDays = $enrollment->enrolled_at->diffInDays(now());
        $maxUnenrollDays = 14; // Allow unenrollment within 14 days

        if ($enrollmentDays > $maxUnenrollDays) {
            return redirect()->back()->with('error',
                'You cannot unenroll from this course after ' . $maxUnenrollDays . ' days. Please contact your academic advisor for assistance.');
        }

        // Check if there are any submitted assignments
        $submissionCount = \App\Models\Submission::where('student_id', $user->id)
            ->whereHas('assignment', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })->count();

        if ($submissionCount > 0) {
            return redirect()->back()->with('error',
                'You cannot unenroll from this course because you have already submitted assignments. Please contact your academic advisor.');
        }

        $enrollment->delete();

        return redirect()->back()->with('success', 'Successfully unenrolled from ' . $course->title);
    }

    /**
     * Admin view of all enrollments
     */
    public function adminIndex()
    {
        $enrollments = Enrollment::with(['student', 'course'])
            ->orderBy('enrolled_at', 'desc')
            ->paginate(20);

        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Admin enrollment management for a specific course
     */
    public function manageCourse(Course $course)
    {
        $course->load(['enrollments.student', 'lecturer']);

        // Get students not enrolled in this course
        $enrolledStudentIds = $course->enrollments()->pluck('student_id');
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $enrolledStudentIds)
            ->active()
            ->get();

        return view('admin.enrollments.manage', compact('course', 'availableStudents'));
    }

    /**
     * Admin enroll student in course
     */
    public function adminEnroll(Request $request, Course $course)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($request->student_id);

        // Check if already enrolled
        if (Enrollment::isEnrolled($student->id, $course->id)) {
            return redirect()->back()->with('error', $student->name . ' is already enrolled in this course.');
        }

        // Create enrollment
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        return redirect()->back()->with('success', $student->name . ' has been enrolled in ' . $course->title);
    }

    /**
     * Admin unenroll student from course
     */
    public function adminUnenroll(Course $course, User $student)
    {
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', $student->name . ' is not enrolled in this course.');
        }

        $enrollment->delete();

        return redirect()->back()->with('success', $student->name . ' has been unenrolled from ' . $course->title);
    }

    /**
     * Bulk enrollment operations
     */
    public function bulkEnroll(Request $request, Course $course)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $enrolledCount = 0;
        $alreadyEnrolledCount = 0;

        foreach ($request->student_ids as $studentId) {
            if (!Enrollment::isEnrolled($studentId, $course->id)) {
                Enrollment::create([
                    'student_id' => $studentId,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                ]);
                $enrolledCount++;
            } else {
                $alreadyEnrolledCount++;
            }
        }

        $message = "Enrolled {$enrolledCount} students successfully.";
        if ($alreadyEnrolledCount > 0) {
            $message .= " {$alreadyEnrolledCount} students were already enrolled.";
        }

        return redirect()->back()->with('success', $message);
    }
}
