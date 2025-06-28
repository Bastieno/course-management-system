<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Show assignment details and submission form
     */
    public function show(Assignment $assignment)
    {
        // Check if student is enrolled in the course
        $isEnrolled = Auth::user()->enrolledCourses()->where('course_id', $assignment->course_id)->exists();

        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to view assignments.');
        }

        // Get existing submission if any
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        $assignment->load('course');

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    /**
     * Submit assignment
     */
    public function submit(Request $request, Assignment $assignment)
    {
        // Check if student is enrolled in the course
        $isEnrolled = Auth::user()->enrolledCourses()->where('course_id', $assignment->course_id)->exists();

        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to submit assignments.');
        }

        // Check if assignment is still open
        if ($assignment->due_date->isPast()) {
            return redirect()->back()->with('error', 'This assignment is past due and no longer accepts submissions.');
        }

        $request->validate([
            'content' => 'required|string|max:10000',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,zip,rar', // 10MB max
        ]);

        // Check if student already submitted
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        if ($existingSubmission) {
            return redirect()->back()->with('error', 'You have already submitted this assignment.');
        }

        $submissionData = [
            'assignment_id' => $assignment->id,
            'student_id' => Auth::id(),
            'content' => $request->content,
            'submitted_at' => now(),
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $fileName, 'public');
            $submissionData['file_path'] = $filePath;
            $submissionData['file_name'] = $file->getClientOriginalName();
        }

        Submission::create($submissionData);

        return redirect()->back()->with('success', 'Assignment submitted successfully!');
    }

    /**
     * Update submission (if allowed)
     */
    public function update(Request $request, Assignment $assignment)
    {
        // Check if student is enrolled in the course
        $isEnrolled = Auth::user()->enrolledCourses()->where('course_id', $assignment->course_id)->exists();

        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to update assignments.');
        }

        // Check if assignment is still open
        if ($assignment->due_date->isPast()) {
            return redirect()->back()->with('error', 'This assignment is past due and no longer accepts updates.');
        }

        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        if (!$submission) {
            return redirect()->back()->with('error', 'No submission found to update.');
        }

        // Don't allow updates if already graded
        if ($submission->grade !== null) {
            return redirect()->back()->with('error', 'Cannot update a graded submission.');
        }

        $request->validate([
            'content' => 'required|string|max:10000',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,zip,rar', // 10MB max
        ]);

        $updateData = [
            'content' => $request->content,
            'submitted_at' => now(), // Update submission time
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $fileName, 'public');
            $updateData['file_path'] = $filePath;
            $updateData['file_name'] = $file->getClientOriginalName();
        }

        $submission->update($updateData);

        return redirect()->back()->with('success', 'Assignment updated successfully!');
    }

    /**
     * Delete submission (if allowed)
     */
    public function destroy(Assignment $assignment)
    {
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        if (!$submission) {
            return redirect()->back()->with('error', 'No submission found to delete.');
        }

        // Don't allow deletion if already graded
        if ($submission->grade !== null) {
            return redirect()->back()->with('error', 'Cannot delete a graded submission.');
        }

        // Check if assignment is still open
        if ($assignment->due_date->isPast()) {
            return redirect()->back()->with('error', 'This assignment is past due and submissions cannot be deleted.');
        }

        // Delete file if exists
        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();

        return redirect()->back()->with('success', 'Submission deleted successfully!');
    }
}
