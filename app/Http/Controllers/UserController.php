<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by archive status (default to active users only)
        $status = $request->get('status', 'active');
        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'archived') {
            $query->archived();
        }
        // 'all' shows both active and archived

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get departments from the departments table
        $departments = Department::active()
                                ->orderBy('name')
                                ->pluck('name');

        return view('admin.users.index', compact('users', 'departments'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $departments = Department::active()
                                ->orderBy('name')
                                ->pluck('name', 'id');
        
        return view('admin.users.create', compact('departments'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,lecturer,student',
            'phone' => 'nullable|string|max:20',
            'student_id' => 'nullable|string|max:50|unique:users',
            'department' => 'required|string|max:100',
            'level' => 'nullable|integer|min:100|max:800',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $departments = Department::active()
                                ->orderBy('name')
                                ->pluck('name', 'id');
        
        return view('admin.users.edit', compact('user', 'departments'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,lecturer,student',
            'phone' => 'nullable|string|max:20',
            'student_id' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'department' => 'required|string|max:100',
            'level' => 'nullable|integer|min:100|max:800',
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully!');
    }

    /**
     * Archive the specified user
     */
    public function archive(User $user)
    {
        // Prevent admin from archiving themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'You cannot archive your own account!');
        }

        $user->archive();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User archived successfully!');
    }

    /**
     * Unarchive the specified user
     */
    public function unarchive(User $user)
    {
        $user->unarchive();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User unarchived successfully!');
    }

    /**
     * Bulk archive users
     */
    public function bulkArchive(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;

        // Remove current admin from archive list
        $userIds = array_filter($userIds, function($id) {
            return $id != auth()->id();
        });

        User::whereIn('id', $userIds)->update(['archived_at' => now()]);

        return redirect()->route('admin.users.index')
                        ->with('success', count($userIds) . ' users archived successfully!');
    }

    /**
     * Permanently delete the specified user (Admin only)
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'You cannot delete your own account!');
        }

        // Only allow deletion of archived users
        if (!$user->isArchived()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Please archive the user first before permanent deletion!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User permanently deleted!');
    }
}
