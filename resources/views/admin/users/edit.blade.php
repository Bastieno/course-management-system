<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">CMS Admin</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit User: {{ $user->name }}</h5>
                        <div>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info btn-sm me-2">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <!-- Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password (Optional for editing) -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password">
                                    <div class="form-text">Leave blank to keep current password</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>

                            <!-- Role and Department -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role') is-invalid @enderror"
                                            id="role" name="role" required onchange="toggleStudentFields()">
                                        <option value="">Select Role</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="lecturer" {{ old('role', $user->role) == 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department') is-invalid @enderror"
                                            id="department" name="department" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $id => $name)
                                            <option value="{{ $name }}" {{ old('department', $user->department) == $name ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                           placeholder="e.g., +234 123 456 7890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6" id="student-id-field">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <input type="text" class="form-control @error('student_id') is-invalid @enderror"
                                           id="student_id" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                                           placeholder="e.g., STU/2024/001">
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Student Level (only for students) -->
                            <div class="row mb-3" id="level-field">
                                <div class="col-md-6">
                                    <label for="level" class="form-label">Level</label>
                                    <select class="form-select @error('level') is-invalid @enderror"
                                            id="level" name="level">
                                        <option value="">Select Level</option>
                                        <option value="100" {{ old('level', $user->level) == '100' ? 'selected' : '' }}>100 Level</option>
                                        <option value="200" {{ old('level', $user->level) == '200' ? 'selected' : '' }}>200 Level</option>
                                        <option value="300" {{ old('level', $user->level) == '300' ? 'selected' : '' }}>300 Level</option>
                                        <option value="400" {{ old('level', $user->level) == '400' ? 'selected' : '' }}>400 Level</option>
                                        <option value="500" {{ old('level', $user->level) == '500' ? 'selected' : '' }}>500 Level</option>
                                        <option value="600" {{ old('level', $user->level) == '600' ? 'selected' : '' }}>600 Level</option>
                                        <option value="700" {{ old('level', $user->level) == '700' ? 'selected' : '' }}>700 Level</option>
                                        <option value="800" {{ old('level', $user->level) == '800' ? 'selected' : '' }}>800 Level</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Account Created</label>
                                    <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y \a\t g:i A') }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Updated</label>
                                    <input type="text" class="form-control" value="{{ $user->updated_at->format('M d, Y \a\t g:i A') }}" readonly>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update User
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Additional Actions -->
                @if($user->id !== auth()->id())
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Once you delete this user, there is no going back. Please be certain.</p>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleStudentFields() {
            const role = document.getElementById('role').value;
            const studentIdField = document.getElementById('student-id-field');
            const levelField = document.getElementById('level-field');

            if (role === 'student') {
                studentIdField.style.display = 'block';
                levelField.style.display = 'block';
            } else {
                studentIdField.style.display = 'none';
                levelField.style.display = 'none';
                // Clear values when hidden (but only if not already a student)
                if ('{{ $user->role }}' !== 'student') {
                    document.getElementById('student_id').value = '';
                    document.getElementById('level').value = '';
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleStudentFields();
        });
    </script>
</body>
</html>
