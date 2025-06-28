<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="#">CMS Admin</a>
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
        <h1>Admin Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="row mb-4 g-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_users'] }}</h5>
                        <p class="card-text">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_students'] }}</h5>
                        <p class="card-text">Students</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_lecturers'] }}</h5>
                        <p class="card-text">Lecturers</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_courses'] }}</h5>
                        <p class="card-text">Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_assignments'] }}</h5>
                        <p class="card-text">Assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_enrollments'] }}</h5>
                        <p class="card-text">Enrollments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Manage Users</a>
                            <a href="{{ route('admin.courses.create') }}" class="btn btn-success">Add Course</a>
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-info">Manage Courses</a>
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-warning">Manage Departments</a>
                            <a href="{{ route('admin.assignments.index') }}" class="btn btn-outline-primary">Manage Assignments</a>
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-success">Manage Enrollments</a>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-dark">View Reports</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Users</h5>
                    </div>
                    <div class="card-body">
                        @if($recent_users->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td><span class="badge bg-secondary">{{ ucfirst($user->role) }}</span></td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none">
                                @foreach($recent_users as $user)
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'lecturer' ? 'warning' : 'primary') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </div>
                                        <div class="small text-muted mb-1">{{ $user->email }}</div>
                                        <div class="small text-muted">{{ $user->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No users found.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Courses -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Courses</h5>
                    </div>
                    <div class="card-body">
                        @if($recent_courses->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Code</th>
                                            <th>Lecturer</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_courses as $course)
                                        <tr>
                                            <td>{{ $course->title }}</td>
                                            <td>{{ $course->code }}</td>
                                            <td>{{ $course->lecturer->name ?? 'N/A' }}</td>
                                            <td>{{ $course->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none">
                                @foreach($recent_courses as $course)
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="fw-bold">{{ $course->title }}</div>
                                            <span class="badge bg-primary">{{ $course->code }}</span>
                                        </div>
                                        <div class="small text-muted mb-1">{{ $course->lecturer->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $course->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No courses found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
