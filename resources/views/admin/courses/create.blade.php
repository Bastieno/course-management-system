<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course - Course Management System</title>
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

    <div class="container my-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Course</h5>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Courses
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.courses.store') }}">
                            @csrf

                            <!-- Course Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}"
                                           placeholder="e.g., Introduction to Computer Science" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code') }}"
                                           placeholder="e.g., CSC101" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Description -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="description" class="form-label">Course Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Brief description of the course content and objectives">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Details -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="credits" class="form-label">Credits <span class="text-danger">*</span></label>
                                    <select class="form-select @error('credits') is-invalid @enderror"
                                            id="credits" name="credits" required>
                                        <option value="">Select Credits</option>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('credits') == $i ? 'selected' : '' }}>
                                                {{ $i }} Credit{{ $i > 1 ? 's' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('credits')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select class="form-select @error('semester') is-invalid @enderror"
                                            id="semester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="First" {{ old('semester') == 'First' ? 'selected' : '' }}>First Semester</option>
                                        <option value="Second" {{ old('semester') == 'Second' ? 'selected' : '' }}>Second Semester</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                                    <select class="form-select @error('level') is-invalid @enderror"
                                            id="level" name="level" required>
                                        <option value="">Select Level</option>
                                        <option value="100" {{ old('level') == '100' ? 'selected' : '' }}>100 Level</option>
                                        <option value="200" {{ old('level') == '200' ? 'selected' : '' }}>200 Level</option>
                                        <option value="300" {{ old('level') == '300' ? 'selected' : '' }}>300 Level</option>
                                        <option value="400" {{ old('level') == '400' ? 'selected' : '' }}>400 Level</option>
                                        <option value="500" {{ old('level') == '500' ? 'selected' : '' }}>500 Level</option>
                                        <option value="600" {{ old('level') == '600' ? 'selected' : '' }}>600 Level</option>
                                        <option value="700" {{ old('level') == '700' ? 'selected' : '' }}>700 Level</option>
                                        <option value="800" {{ old('level') == '800' ? 'selected' : '' }}>800 Level</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department') is-invalid @enderror"
                                            id="department" name="department" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->name }}" {{ old('department') == $department->name ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($departments->count() == 0)
                                        <div class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            No departments found. Please create departments first.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Lecturer Assignment -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="lecturer_id" class="form-label">Assign Lecturer <span class="text-danger">*</span></label>
                                    <select class="form-select @error('lecturer_id') is-invalid @enderror"
                                            id="lecturer_id" name="lecturer_id" required>
                                        <option value="">Select Lecturer</option>
                                        @foreach($lecturers as $lecturer)
                                            <option value="{{ $lecturer->id }}" {{ old('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                                                {{ $lecturer->name }} ({{ $lecturer->department }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lecturer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($lecturers->count() == 0)
                                        <div class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            No lecturers found. Please create lecturer accounts first.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary" {{ $lecturers->count() == 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-save"></i> Create Course
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card my-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Course Creation Guidelines</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Course Code:</strong> Should be unique and follow your institution's naming convention (e.g., CSC101, MTH201)</li>
                            <li><strong>Credits:</strong> Typically ranges from 1-6 credits for most courses</li>
                            <li><strong>Level:</strong> Corresponds to the academic year (100-400 for undergraduate, 500+ for postgraduate)</li>
                            <li><strong>Lecturer:</strong> Only users with "Lecturer" role can be assigned to courses</li>
                            <li><strong>Department:</strong> Should match the department offering the course</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
