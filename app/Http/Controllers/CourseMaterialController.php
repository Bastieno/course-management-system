<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseMaterialController extends Controller
{
    /**
     * Display course materials for a specific course
     */
    public function index(Course $course)
    {
        // Check if user has access to this course
        if (!$this->hasAccessToCourse($course)) {
            return redirect()->back()->with('error', 'You do not have access to this course.');
        }

        $materials = $course->materials()->orderBy('created_at', 'desc')->get();

        return view('course-materials.index', compact('course', 'materials'));
    }

    /**
     * Show the form for creating a new course material
     */
    public function create(Course $course)
    {
        // Only lecturers and admins can create materials
        if (!Auth::user()->isAdmin() && !Auth::user()->isLecturer()) {
            return redirect()->back()->with('error', 'You do not have permission to add materials.');
        }

        // Check if lecturer owns this course
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only add materials to your own courses.');
        }

        return view('course-materials.create', compact('course'));
    }

    /**
     * Store a newly created course material
     */
    public function store(Request $request, Course $course)
    {
        // Only lecturers and admins can create materials
        if (!Auth::user()->isAdmin() && !Auth::user()->isLecturer()) {
            return redirect()->back()->with('error', 'You do not have permission to add materials.');
        }

        // Check if lecturer owns this course
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only add materials to your own courses.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video,document,link,other',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,txt,zip,rar,mp4,avi,mov,wmv', // 50MB max
            'link_url' => 'nullable|url|max:500',
        ]);

        $materialData = [
            'course_id' => $course->id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('course-materials', $fileName, 'public');
            $materialData['file_path'] = $filePath;
        }

        // Handle link type
        if ($request->type === 'link' && $request->link_url) {
            $materialData['file_path'] = $request->link_url;
        }

        CourseMaterial::create($materialData);

        return redirect()->route($this->getRoutePrefix() . '.courses.materials.index', $course)
            ->with('success', 'Course material added successfully!');
    }

    /**
     * Display the specified course material
     */
    public function show(Course $course, CourseMaterial $material)
    {
        // Check if user has access to this course
        if (!$this->hasAccessToCourse($course)) {
            return redirect()->back()->with('error', 'You do not have access to this course.');
        }

        // Check if material belongs to this course
        if ($material->course_id !== $course->id) {
            return redirect()->back()->with('error', 'Material not found in this course.');
        }

        return view('course-materials.show', compact('course', 'material'));
    }

    /**
     * Show the form for editing the specified course material
     */
    public function edit(Course $course, CourseMaterial $material)
    {
        // Only lecturers and admins can edit materials
        if (!Auth::user()->isAdmin() && !Auth::user()->isLecturer()) {
            return redirect()->back()->with('error', 'You do not have permission to edit materials.');
        }

        // Check if lecturer owns this course
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit materials in your own courses.');
        }

        // Check if material belongs to this course
        if ($material->course_id !== $course->id) {
            return redirect()->back()->with('error', 'Material not found in this course.');
        }

        return view('course-materials.edit', compact('course', 'material'));
    }

    /**
     * Update the specified course material
     */
    public function update(Request $request, Course $course, CourseMaterial $material)
    {
        // Only lecturers and admins can update materials
        if (!Auth::user()->isAdmin() && !Auth::user()->isLecturer()) {
            return redirect()->back()->with('error', 'You do not have permission to update materials.');
        }

        // Check if lecturer owns this course
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only update materials in your own courses.');
        }

        // Check if material belongs to this course
        if ($material->course_id !== $course->id) {
            return redirect()->back()->with('error', 'Material not found in this course.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video,document,link,other',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,txt,zip,rar,mp4,avi,mov,wmv', // 50MB max
            'link_url' => 'nullable|url|max:500',
        ]);

        $updateData = [
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists and it's not a link
            if ($material->file_path && $material->type !== 'link') {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('course-materials', $fileName, 'public');
            $updateData['file_path'] = $filePath;
        }

        // Handle link type
        if ($request->type === 'link' && $request->link_url) {
            // Delete old file if changing from file to link
            if ($material->file_path && $material->type !== 'link') {
                Storage::disk('public')->delete($material->file_path);
            }
            $updateData['file_path'] = $request->link_url;
        }

        $material->update($updateData);

        return redirect()->route($this->getRoutePrefix() . '.courses.materials.index', $course)
            ->with('success', 'Course material updated successfully!');
    }

    /**
     * Remove the specified course material
     */
    public function destroy(Course $course, CourseMaterial $material)
    {
        // Only lecturers and admins can delete materials
        if (!Auth::user()->isAdmin() && !Auth::user()->isLecturer()) {
            return redirect()->back()->with('error', 'You do not have permission to delete materials.');
        }

        // Check if lecturer owns this course
        if (Auth::user()->isLecturer() && $course->lecturer_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete materials from your own courses.');
        }

        // Check if material belongs to this course
        if ($material->course_id !== $course->id) {
            return redirect()->back()->with('error', 'Material not found in this course.');
        }

        // Delete the file if it exists and it's not a link
        if ($material->type !== 'link') {
            $material->deleteFile();
        }

        $material->delete();

        return redirect()->route($this->getRoutePrefix() . '.courses.materials.index', $course)
            ->with('success', 'Course material deleted successfully!');
    }

    /**
     * Download the specified course material
     */
    public function download(Course $course, CourseMaterial $material)
    {
        // Check if user has access to this course
        if (!$this->hasAccessToCourse($course)) {
            return redirect()->back()->with('error', 'You do not have access to this course.');
        }

        // Check if material belongs to this course
        if ($material->course_id !== $course->id) {
            return redirect()->back()->with('error', 'Material not found in this course.');
        }

        // Handle link type
        if ($material->type === 'link') {
            return redirect($material->file_path);
        }

        // Check if file exists
        if (!$material->fileExists()) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($material->file_path, $material->title);
    }

    /**
     * Get the route prefix based on user role
     */
    private function getRoutePrefix()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return 'admin';
        } elseif ($user->isLecturer()) {
            return 'lecturer';
        } elseif ($user->isStudent()) {
            return 'student';
        }

        return 'admin'; // fallback
    }

    /**
     * Check if user has access to the course
     */
    private function hasAccessToCourse(Course $course)
    {
        $user = Auth::user();

        // Admin has access to all courses
        if ($user->isAdmin()) {
            return true;
        }

        // Lecturer has access to their own courses
        if ($user->isLecturer() && $course->lecturer_id === $user->id) {
            return true;
        }

        // Student has access to enrolled courses
        if ($user->isStudent()) {
            return $user->enrolledCourses()->where('course_id', $course->id)->exists();
        }

        return false;
    }
}
