<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">CMS Admin</a>
            <div class="navbar-nav ms-auto d-flex align-items-center flex-row">
                <span class="navbar-text me-2 d-none d-md-inline">Welcome, {{ auth()->user()->name }}</span>
                <span class="navbar-text me-2 d-md-none text-truncate" style="max-width: 120px;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-3 px-lg-4 my-4">
        <!-- Mobile Layout: Stacked -->
        <div class="d-block d-md-none mb-4">
            <h1 class="mb-0">Course Management</h1>
        </div>

        <!-- Desktop Layout: Side by Side -->
        <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
            <h1>Course Management</h1>
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Course
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.courses.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Course title, code, or description">
                    </div>
                    <div class="col-md-2">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-select" id="semester" name="semester">
                            <option value="">All Semesters</option>
                            <option value="First" {{ request('semester') == 'First' ? 'selected' : '' }}>First</option>
                            <option value="Second" {{ request('semester') == 'Second' ? 'selected' : '' }}>Second</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select" id="level" name="level">
                            <option value="">All Levels</option>
                            <option value="100" {{ request('level') == '100' ? 'selected' : '' }}>100 Level</option>
                            <option value="200" {{ request('level') == '200' ? 'selected' : '' }}>200 Level</option>
                            <option value="300" {{ request('level') == '300' ? 'selected' : '' }}>300 Level</option>
                            <option value="400" {{ request('level') == '400' ? 'selected' : '' }}>400 Level</option>
                            <option value="500" {{ request('level') == '500' ? 'selected' : '' }}>500 Level</option>
                            <option value="600" {{ request('level') == '600' ? 'selected' : '' }}>600 Level</option>
                            <option value="700" {{ request('level') == '700' ? 'selected' : '' }}>700 Level</option>
                            <option value="800" {{ request('level') == '800' ? 'selected' : '' }}>800 Level</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="lecturer_id" class="form-label">Lecturer</label>
                        <select class="form-select" id="lecturer_id" name="lecturer_id">
                            <option value="">All Lecturers</option>
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ request('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Courses ({{ $courses->total() }})</h5>
                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            </div>
            <div class="card-body">
                @if($courses->count() > 0)
                    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.courses.bulk-delete') }}">
                        @csrf

                        <!-- Desktop Table View -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Course</th>
                                        <th>Code</th>
                                        <th>Lecturer</th>
                                        <th>Department</th>
                                        <th>Level</th>
                                        <th>Credits</th>
                                        <th>Students</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                                   class="form-check-input course-checkbox">
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $course->title }}</strong>
                                                @if($course->description)
                                                    <br><small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $course->code }}</span>
                                        </td>
                                        <td>{{ $course->lecturer->name ?? 'N/A' }}</td>
                                        <td>{{ $course->department }}</td>
                                        <td>{{ $course->level }} Level</td>
                                        <td>{{ $course->credits }} Credits</td>
                                        <td>
                                            <span class="badge bg-info">{{ $course->students()->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.courses.show', $course) }}"
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.courses.edit', $course) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-lg-none">
                            @foreach($courses as $course)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                                   class="form-check-input course-checkbox" id="course-{{ $course->id }}">
                                            <label class="form-check-label fw-bold" for="course-{{ $course->id }}">
                                                {{ $course->title }}
                                            </label>
                                        </div>
                                        <span class="badge bg-primary">{{ $course->code }}</span>
                                    </div>

                                    @if($course->description)
                                        <p class="text-muted small mb-2">{{ Str::limit($course->description, 100) }}</p>
                                    @endif

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Lecturer</small>
                                            <span class="fw-medium">{{ $course->lecturer->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Department</small>
                                            <span class="fw-medium">{{ $course->department }}</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Level</small>
                                            <span class="fw-medium">{{ $course->level }} Level</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Credits</small>
                                            <span class="badge bg-secondary">{{ $course->credits }}</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Students</small>
                                            <span class="badge bg-info">{{ $course->students()->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.courses.show', $course) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $courses->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <h5>No courses found</h5>
                        <p class="text-muted">Try adjusting your search criteria or add a new course.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.course-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkDeleteButton();
        });

        // Individual checkbox change
        document.querySelectorAll('.course-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkDeleteButton);
        });

        function toggleBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            if (checkedBoxes.length > 0) {
                bulkDeleteBtn.style.display = 'block';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }

        // Bulk delete confirmation
        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
            if (checkedBoxes.length > 0 && confirm(`Are you sure you want to delete ${checkedBoxes.length} selected courses?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    </script>
</body>
</html>
