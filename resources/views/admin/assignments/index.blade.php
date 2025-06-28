<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Management - Course Management System</title>
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
                <li class="breadcrumb-item active" aria-current="page">Assignment Management</li>
            </ol>
        </nav>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="d-flex align-items-center"><i class="fas fa-tasks me-2" style="font-size: 1.5rem;"></i>Assignment Management</h2>
            <div class="d-flex gap-2">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.assignments.analytics') }}" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-1"></i>Analytics
                    </a>
                @endif
                <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.create') : route('admin.assignments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Assignment
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $stats['total_assignments'] }}</h3>
                        <p class="card-text">Total Assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-info text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $stats['upcoming_assignments'] }}</h3>
                        <p class="card-text">Upcoming</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-warning text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $stats['overdue_assignments'] }}</h3>
                        <p class="card-text">Overdue</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-success text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $stats['graded_submissions'] }}/{{ $stats['total_submissions'] }}</h3>
                        <p class="card-text">Graded Submissions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-filter me-2"></i>Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.index') : route('admin.assignments.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search assignments...">
                        </div>
                        <div class="col-md-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list me-2"></i>Assignments</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="bulkDeleteBtn" style="display: none;">
                        <i class="fas fa-trash me-1"></i>Delete Selected
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($assignments->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Assignment</th>
                                    <th>Course</th>
                                    <th>Due Date</th>
                                    <th>Max Score</th>
                                    <th>Submissions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input assignment-checkbox"
                                               value="{{ $assignment->id }}">
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $assignment->title }}</div>
                                        <small class="text-muted">{{ Str::limit($assignment->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $assignment->course->code }}</span>
                                        <div class="small">{{ $assignment->course->title }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $assignment->due_date->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $assignment->due_date->format('h:i A') }}</small>
                                    </td>
                                    <td>{{ $assignment->max_score }}</td>
                                    <td>
                                        <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.submissions', $assignment) : route('admin.assignments.submissions', $assignment) }}" class="text-decoration-none">
                                            {{ $assignment->submissions->count() }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($assignment->isOverdue())
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.show', $assignment) : route('admin.assignments.show', $assignment) }}"
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.edit', $assignment) : route('admin.assignments.edit', $assignment) }}"
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.destroy', $assignment) : route('admin.assignments.destroy', $assignment) }}"
                                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
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
                    <div class="d-md-none">
                        @foreach($assignments as $assignment)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input assignment-checkbox"
                                               value="{{ $assignment->id }}" id="assignment{{ $assignment->id }}">
                                        <label class="form-check-label fw-bold" for="assignment{{ $assignment->id }}">
                                            {{ $assignment->title }}
                                        </label>
                                    </div>
                                    @if($assignment->isOverdue())
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-primary">{{ $assignment->course->code }}</span>
                                    <span class="ms-2">{{ $assignment->course->title }}</span>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted">Due Date:</small>
                                        <div>{{ $assignment->due_date->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Max Score:</small>
                                        <div>{{ $assignment->max_score }}</div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Submissions:</small>
                                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.submissions', $assignment) : route('admin.assignments.submissions', $assignment) }}" class="text-decoration-none">
                                        {{ $assignment->submissions->count() }}
                                    </a>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.show', $assignment) : route('admin.assignments.show', $assignment) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.edit', $assignment) : route('admin.assignments.edit', $assignment) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.destroy', $assignment) : route('admin.assignments.destroy', $assignment) }}"
                                          class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $assignments->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No assignments found</h5>
                        <p class="text-muted">Create your first assignment to get started.</p>
                        <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.create') : route('admin.assignments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Assignment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Bulk Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the selected assignments? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.assignments.bulk-delete') }}" id="bulkDeleteForm">
                        @csrf
                        <input type="hidden" name="assignment_ids" id="selectedAssignments">
                        <button type="submit" class="btn btn-danger">Delete Selected</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bulk selection functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.assignment-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkDeleteButton();
        });

        document.querySelectorAll('.assignment-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkDeleteButton);
        });

        function toggleBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.assignment-checkbox:checked');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            if (checkedBoxes.length > 0) {
                bulkDeleteBtn.style.display = 'block';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }

        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.assignment-checkbox:checked');
            const assignmentIds = Array.from(checkedBoxes).map(cb => cb.value);

            document.getElementById('selectedAssignments').value = JSON.stringify(assignmentIds);

            const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
            modal.show();
        });
    </script>
</body>
</html>
