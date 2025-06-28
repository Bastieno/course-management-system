<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid px-2 px-sm-3 px-lg-4">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <span class="d-none d-sm-inline">CMS Admin</span>
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
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="d-flex align-items-center"><i class="fas fa-chart-bar me-2" style="font-size: 1.5rem;"></i>System Reports</h2>
        </div>

        <!-- Quick Report Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Report Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.reports.users') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <span>User Reports</span>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.reports.courses') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                    <i class="fas fa-book fa-2x mb-2"></i>
                                    <span>Course Reports</span>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.reports.enrollments') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                    <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                    <span>Enrollment Reports</span>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.reports.assignments') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                    <i class="fas fa-tasks fa-2x mb-2"></i>
                                    <span>Assignment Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="row mb-4 g-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h4 class="card-title">{{ $stats['total_users'] }}</h4>
                        <p class="card-text">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100 bg-success text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h4 class="card-title">{{ $stats['total_students'] }}</h4>
                        <p class="card-text">Students</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100 bg-info text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h4 class="card-title">{{ $stats['total_lecturers'] }}</h4>
                        <p class="card-text">Lecturers</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100 bg-warning text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h4 class="card-title">{{ $stats['total_courses'] }}</h4>
                        <p class="card-text">Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100 bg-secondary text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h4 class="card-title">{{ $stats['total_assignments'] }}</h4>
                        <p class="card-text">Assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card text-center h-100 bg-dark text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h4 class="card-title">{{ $stats['total_enrollments'] }}</h4>
                        <p class="card-text">Enrollments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Activity (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ $recentActivity['new_users'] }}</h4>
                                    <small class="text-muted">New Users</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-success">{{ $recentActivity['new_courses'] }}</h4>
                                    <small class="text-muted">New Courses</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-info">{{ $recentActivity['new_enrollments'] }}</h4>
                                    <small class="text-muted">New Enrollments</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-warning">{{ $recentActivity['new_assignments'] }}</h4>
                                    <small class="text-muted">New Assignments</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Enrolled Courses -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Top Enrolled Courses</h5>
                    </div>
                    <div class="card-body">
                        @if($courseEnrollments->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($courseEnrollments->take(5) as $course)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="fw-bold">{{ $course->title }}</div>
                                        <small class="text-muted">{{ $course->code }}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $course->enrollments_count }}</span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No course enrollment data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Statistics -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Department Overview</h5>
                    </div>
                    <div class="card-body">
                        @if($departmentStats->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Courses</th>
                                            <th>Users</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departmentStats as $department)
                                        <tr>
                                            <td>{{ $department->name }}</td>
                                            <td><span class="badge bg-success">{{ $department->courses_count }}</span></td>
                                            <td><span class="badge bg-primary">{{ $department->users_count }}</span></td>
                                            <td>
                                                <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">
                                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none">
                                @foreach($departmentStats as $department)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="fw-bold">{{ $department->name }}</div>
                                            <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">
                                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Courses:</small>
                                                <div><span class="badge bg-success">{{ $department->courses_count }}</span></div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Users:</small>
                                                <div><span class="badge bg-primary">{{ $department->users_count }}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No department data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
