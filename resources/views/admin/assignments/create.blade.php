<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark {{ auth()->user()->role === 'lecturer' ? 'bg-primary' : 'bg-dark' }} sticky-top">
        <div class="container-fluid px-2 px-sm-3 px-lg-4">
            <a class="navbar-brand" href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.dashboard') : route('admin.dashboard') }}">
                <span class="d-none d-sm-inline">{{ auth()->user()->role === 'lecturer' ? 'CMS Lecturer' : 'CMS Admin' }}</span>
                <span class="d-sm-none">CMS</span>
            </a>
            <div class="navbar-nav ms-auto d-flex align-items-center flex-row">
                <span class="navbar-text me-2 d-none d-lg-inline">Welcome, {{ auth()->user()->name }}</span>
                <span class="navbar-text me-2 d-none d-md-inline d-lg-none text-truncate" style="max-width: 100px;">{{ auth()->user()->name }}</span>
                <span class="navbar-text me-2 d-md-none text-truncate" style="max-width: 80px; font-size: 0.875rem;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">
                        <span class="d-none d-sm-inline">Logout</span>
                        <span class="d-sm-none">Exit</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-3 px-lg-4 my-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.dashboard') : route('admin.dashboard') }}">
                        Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.index') : route('admin.assignments.index') }}">
                        Assignment Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Create Assignment</li>
            </ol>
        </nav>

        <!-- Assignment Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="d-flex align-items-center"><i class="fas fa-plus me-2" style="font-size: 1.5rem;"></i>Create Assignment</h2>
                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.index') : route('admin.assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Assignments
                    </a>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-edit me-2"></i>Assignment Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.store') : route('admin.assignments.store') }}">
                            @csrf

                            <!-- Course Selection -->
                            <div class="mb-3">
                                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                                <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                    <option value="">Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->code }} - {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Assignment Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Assignment Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title" value="{{ old('title') }}"
                                       placeholder="Enter assignment title" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Assignment Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="6"
                                          placeholder="Enter assignment description, instructions, and requirements" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Provide clear instructions and requirements for the assignment.</div>
                            </div>

                            <!-- Due Date and Time -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror"
                                           id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="max_score" class="form-label">Maximum Score <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_score') is-invalid @enderror"
                                           id="max_score" name="max_score" value="{{ old('max_score', 100) }}"
                                           min="1" max="1000" required>
                                    @error('max_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter the maximum possible score for this assignment.</div>
                                </div>
                            </div>

                            <!-- Assignment Preview -->
                            <div class="mb-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6><i class="fas fa-eye me-2"></i>Assignment Preview</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Course:</strong> <span id="preview-course">Not selected</span><br>
                                                <strong>Title:</strong> <span id="preview-title">Not entered</span><br>
                                                <strong>Due Date:</strong> <span id="preview-due-date">Not set</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Max Score:</strong> <span id="preview-max-score">100</span><br>
                                                <strong>Status:</strong> <span class="badge bg-success">Draft</span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <strong>Description:</strong>
                                            <div id="preview-description" class="mt-2 p-2 border rounded bg-white" style="white-space: pre-wrap;">
                                                No description entered
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.index') : route('admin.assignments.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Create Assignment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const courseSelect = document.getElementById('course_id');
            const titleInput = document.getElementById('title');
            const descriptionInput = document.getElementById('description');
            const dueDateInput = document.getElementById('due_date');
            const maxScoreInput = document.getElementById('max_score');

            // Update preview when inputs change
            courseSelect.addEventListener('change', updatePreview);
            titleInput.addEventListener('input', updatePreview);
            descriptionInput.addEventListener('input', updatePreview);
            dueDateInput.addEventListener('change', updatePreview);
            maxScoreInput.addEventListener('input', updatePreview);

            function updatePreview() {
                // Update course
                const selectedCourse = courseSelect.options[courseSelect.selectedIndex];
                document.getElementById('preview-course').textContent =
                    selectedCourse.value ? selectedCourse.textContent : 'Not selected';

                // Update title
                document.getElementById('preview-title').textContent =
                    titleInput.value || 'Not entered';

                // Update description
                document.getElementById('preview-description').textContent =
                    descriptionInput.value || 'No description entered';

                // Update due date
                if (dueDateInput.value) {
                    const date = new Date(dueDateInput.value);
                    document.getElementById('preview-due-date').textContent =
                        date.toLocaleDateString() + ' at ' + date.toLocaleTimeString();
                } else {
                    document.getElementById('preview-due-date').textContent = 'Not set';
                }

                // Update max score
                document.getElementById('preview-max-score').textContent =
                    maxScoreInput.value || '100';
            }

            // Set minimum date to current date/time
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dueDateInput.min = now.toISOString().slice(0, 16);

            // Initial preview update
            updatePreview();
        });
    </script>
</body>
</html>
