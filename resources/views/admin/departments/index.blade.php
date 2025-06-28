<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management - Course Management System</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Department Management</h1>
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Department
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
                <form method="GET" action="{{ route('admin.departments.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Department name, code, or description">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Departments Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Departments ({{ $departments->total() }})</h5>
                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            </div>
            <div class="card-body">
                @if($departments->count() > 0)
                    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.departments.bulk-delete') }}">
                        @csrf

                        <!-- Desktop Table View -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Department</th>
                                        <th>Code</th>
                                        <th>Head</th>
                                        <th>Building</th>
                                        <th>Courses</th>
                                        <th>Users</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departments as $department)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                                   class="form-check-input department-checkbox">
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $department->name }}</strong>
                                                @if($department->description)
                                                    <br><small class="text-muted">{{ Str::limit($department->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $department->code }}</span>
                                        </td>
                                        <td>{{ $department->head_of_department ?? 'N/A' }}</td>
                                        <td>{{ $department->building ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $department->courses_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $department->users_count }}</span>
                                        </td>
                                        <td>
                                            @if($department->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.departments.show', $department) }}"
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.departments.edit', $department) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.departments.toggle-status', $department) }}"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                                            title="{{ $department->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $department->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.departments.destroy', $department) }}"
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?')">
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
                            @foreach($departments as $department)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                                   class="form-check-input department-checkbox" id="dept-{{ $department->id }}">
                                            <label class="form-check-label fw-bold" for="dept-{{ $department->id }}">
                                                {{ $department->name }}
                                            </label>
                                        </div>
                                        <span class="badge bg-primary">{{ $department->code }}</span>
                                    </div>

                                    @if($department->description)
                                        <p class="text-muted small mb-2">{{ Str::limit($department->description, 100) }}</p>
                                    @endif

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Head</small>
                                            <span class="fw-medium">{{ $department->head_of_department ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Building</small>
                                            <span class="fw-medium">{{ $department->building ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Courses</small>
                                            <span class="badge bg-info">{{ $department->courses_count }}</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Users</small>
                                            <span class="badge bg-secondary">{{ $department->users_count }}</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Status</small>
                                            @if($department->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.departments.show', $department) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('admin.departments.edit', $department) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.departments.toggle-status', $department) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-{{ $department->is_active ? 'pause' : 'play' }} me-1"></i>
                                                {{ $department->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.departments.destroy', $department) }}"
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?')">
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
                        {{ $departments->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5>No departments found</h5>
                        <p class="text-muted">Try adjusting your search criteria or add a new department.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.department-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkDeleteButton();
        });

        // Individual checkbox change
        document.querySelectorAll('.department-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkDeleteButton);
        });

        function toggleBulkDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.department-checkbox:checked');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            if (checkedBoxes.length > 0) {
                bulkDeleteBtn.style.display = 'block';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }

        // Bulk delete confirmation
        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.department-checkbox:checked');
            if (checkedBoxes.length > 0 && confirm(`Are you sure you want to delete ${checkedBoxes.length} selected departments?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    </script>
</body>
</html>
