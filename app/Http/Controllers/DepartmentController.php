<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index(Request $request)
    {
        $query = Department::withCount(['courses', 'users']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $departments = $query->orderBy('name')->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:10|unique:departments',
            'description' => 'nullable|string|max:1000',
            'head_of_department' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified department
     */
    public function show(Department $department)
    {
        $department->load(['courses', 'users']);

        $stats = [
            'total_courses' => $department->courses()->count(),
            'total_users' => $department->users()->count(),
            'total_students' => $department->users()->where('role', 'student')->count(),
            'total_lecturers' => $department->users()->where('role', 'lecturer')->count(),
        ];

        return view('admin.departments.show', compact('department', 'stats'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'code' => ['required', 'string', 'max:10', Rule::unique('departments')->ignore($department->id)],
            'description' => 'nullable|string|max:1000',
            'head_of_department' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department)
    {
        // Check if department has courses or users
        if ($department->courses()->count() > 0) {
            return redirect()->route('admin.departments.index')
                            ->with('error', 'Cannot delete department with existing courses!');
        }

        if ($department->users()->count() > 0) {
            return redirect()->route('admin.departments.index')
                            ->with('error', 'Cannot delete department with existing users!');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department deleted successfully!');
    }

    /**
     * Toggle department status
     */
    public function toggleStatus(Department $department)
    {
        $department->update(['is_active' => !$department->is_active]);

        $status = $department->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.departments.index')
                        ->with('success', "Department {$status} successfully!");
    }

    /**
     * Bulk delete departments
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'department_ids' => 'required|array',
            'department_ids.*' => 'exists:departments,id'
        ]);

        $departmentIds = $request->department_ids;

        // Check for departments with courses or users
        $departmentsWithData = Department::whereIn('id', $departmentIds)
                                       ->where(function($query) {
                                           $query->has('courses')
                                                 ->orHas('users');
                                       })
                                       ->count();

        if ($departmentsWithData > 0) {
            return redirect()->route('admin.departments.index')
                            ->with('error', 'Cannot delete departments that have courses or users!');
        }

        Department::whereIn('id', $departmentIds)->delete();

        return redirect()->route('admin.departments.index')
                        ->with('success', count($departmentIds) . ' departments deleted successfully!');
    }
}
