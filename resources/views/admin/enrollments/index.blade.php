<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-arrow-left me-2"></i>CMS Admin
            </a>
            <div class="navbar-nav ms-auto d-flex align-items-center flex-row">
                <span class="navbar-text me-2 d-none d-md-inline">Welcome, {{ auth()->user()->name }}</span>
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
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <h1 class="mb-0">Enrollment Management</h1>
        </div>

        <!-- Desktop Layout: Side by Side -->
        <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
            <h1>Enrollment Management</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4>{{ $enrollments->total() }}</h4>
                        <p class="mb-0">Total Enrollments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <h4>{{ $enrollments->groupBy('student_id')->count() }}</h4>
                        <p class="mb-0">Active Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <i class="fas fa-book fa-2x mb-2"></i>
                        <h4>{{ $enrollments->groupBy('course_id')->count() }}</h4>
                        <p class="mb-0">Courses with Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <i class="fas fa-calendar fa-2x mb-2"></i>
                        <h4>{{ $enrollments->where('enrolled_at', '>=', now()->startOfMonth())->count() }}</h4>
                        <p class="mb-0">This Month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollments Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Enrollments</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($enrollments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Department</th>
                                    <th>Enrolled Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                     style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $enrollment->student->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $enrollment->student->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $enrollment->course->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $enrollment->course->code }}</small>
                                        </td>
                                        <td>{{ $enrollment->course->department }}</td>
                                        <td>
                                            {{ $enrollment->enrolled_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $enrollment->enrolled_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.enrollments.manage', $enrollment->course) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-cog me-1"></i>Manage
                                                </a>
                                                <form method="POST" action="{{ route('admin.enrollments.unenroll', [$enrollment->course, $enrollment->student]) }}"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to unenroll this student?')">
                                                        <i class="fas fa-times me-1"></i>Unenroll
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $enrollments->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Enrollments Found</h4>
                        <p class="text-muted">No students have enrolled in any courses yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Enrollments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="course" class="form-label">Course</label>
                            <select class="form-select" name="course" id="course">
                                <option value="">All Courses</option>
                                @foreach(\App\Models\Course::all() as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }} ({{ $course->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" name="department" id="department">
                                <option value="">All Departments</option>
                                @foreach(\App\Models\Course::distinct()->pluck('department') as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date_from" class="form-label">Enrolled From</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="mb-3">
                            <label for="date_to" class="form-label">Enrolled To</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
