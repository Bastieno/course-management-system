<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Course Management System</title>
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
            <h2 class="d-flex align-items-center mb-0"><i class="fas fa-users me-2" style="font-size: 1.5rem;"></i>User Management</h2>
        </div>

        <!-- Desktop Layout: Side by Side -->
        <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
            <h2 class="d-flex align-items-center"><i class="fas fa-users me-2" style="font-size: 1.5rem;"></i>User Management</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
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
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Name, email, or student ID">
                    </div>
                    <div class="col-md-2">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="lecturer" {{ request('role') == 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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

        <!-- Users Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Users ({{ $users->total() }})</h5>
                <button type="button" class="btn btn-warning btn-sm" id="bulkArchiveBtn" style="display: none;">
                    <i class="fas fa-archive"></i> Archive Selected
                </button>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <form id="bulkArchiveForm" method="POST" action="{{ route('admin.users.bulk-archive') }}">
                        @csrf

                        <!-- Desktop Table View -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Student ID</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr class="{{ $user->isArchived() ? 'table-secondary' : '' }}">
                                        <td>
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                   class="form-check-input user-checkbox"
                                                   {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                {{ $user->name }}
                                                @if($user->id == auth()->id())
                                                    <span class="badge bg-info ms-2">You</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'lecturer' ? 'warning' : 'primary') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->department ?? 'N/A' }}</td>
                                        <td>{{ $user->student_id ?? 'N/A' }}</td>
                                        <td>
                                            @if($user->isArchived())
                                                <span class="badge bg-secondary">Archived</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user->created_at->format('M d, Y') }}
                                            @if($user->isArchived())
                                                <br><small class="text-muted">Archived: {{ $user->archived_at->format('M d, Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id != auth()->id())
                                                    @if($user->isArchived())
                                                        <form method="POST" action="{{ route('admin.users.unarchive', $user) }}"
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Unarchive">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                              class="d-inline" onsubmit="return confirm('WARNING: This will permanently delete this user and cannot be undone. Are you sure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Permanently Delete">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.users.archive', $user) }}"
                                                              class="d-inline" onsubmit="return confirm('Are you sure you want to archive this user?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive">
                                                                <i class="fas fa-archive"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-lg-none">
                            @foreach($users as $user)
                            <div class="card mb-3 {{ $user->isArchived() ? 'border-secondary' : '' }}">
                                <div class="card-body {{ $user->isArchived() ? 'bg-light' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            @if($user->id != auth()->id())
                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                       class="form-check-input user-checkbox me-2">
                                            @endif
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                @if($user->id == auth()->id())
                                                    <span class="badge bg-info">You</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'lecturer' ? 'warning' : 'primary') }} mb-1">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                            @if($user->isArchived())
                                                <span class="badge bg-secondary">Archived</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Email</small>
                                        <span class="fw-medium">{{ $user->email }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">Department</small>
                                        <span class="fw-medium">{{ $user->department ?? 'N/A' }}</span>
                                    </div>

                                    @if($user->role === 'student' && $user->student_id)
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Student ID</small>
                                            <span class="fw-medium">{{ $user->student_id }}</span>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Created</small>
                                        <span class="fw-medium">{{ $user->created_at->format('M d, Y') }}</span>
                                        @if($user->isArchived())
                                            <br><small class="text-muted">Archived: {{ $user->archived_at->format('M d, Y') }}</small>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        @if($user->id != auth()->id())
                                            @if($user->isArchived())
                                                <form method="POST" action="{{ route('admin.users.unarchive', $user) }}"
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-undo me-1"></i>Unarchive
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                      class="d-inline" onsubmit="return confirm('WARNING: This will permanently delete this user and cannot be undone. Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt me-1"></i>Delete
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.archive', $user) }}"
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to archive this user?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-archive me-1"></i>Archive
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5>No users found</h5>
                        <p class="text-muted">Try adjusting your search criteria or add a new user.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkArchiveButton();
        });

        // Individual checkbox change
        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkArchiveButton);
        });

        function toggleBulkArchiveButton() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const bulkArchiveBtn = document.getElementById('bulkArchiveBtn');

            if (checkedBoxes.length > 0) {
                bulkArchiveBtn.style.display = 'block';
            } else {
                bulkArchiveBtn.style.display = 'none';
            }
        }

        // Bulk archive confirmation
        document.getElementById('bulkArchiveBtn').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            if (checkedBoxes.length > 0 && confirm(`Are you sure you want to archive ${checkedBoxes.length} selected users?`)) {
                document.getElementById('bulkArchiveForm').submit();
            }
        });
    </script>

    <style>
        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        /* Enhanced badge styling */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
        }

        /* Role-specific badge colors */
        .badge.bg-danger {
            background-color: #dc3545 !important;
            color: white;
        }

        .badge.bg-warning {
            background-color: #fd7e14 !important;
            color: white;
        }

        .badge.bg-primary {
            background-color: #0d6efd !important;
            color: white;
        }

        .badge.bg-success {
            background-color: #198754 !important;
            color: white;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
            color: white;
        }

        .badge.bg-info {
            background-color: #0dcaf0 !important;
            color: #000;
        }

        /* Card enhancements */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Badge container for better alignment */
        .badge-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }
    </style>
</body>
</html>
