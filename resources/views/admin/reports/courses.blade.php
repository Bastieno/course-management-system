<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Reports - Course Management System</title>
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
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Course Reports</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-book me-2"></i>Course Reports</h1>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Reports
            </a>
        </div>

        <!-- Course Statistics -->
        <div class="row mb-4 g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $courseStats['total_courses'] }}</h3>
                        <p class="card-text">Total Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-success text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $courseStats['courses_with_enrollments'] }}</h3>
                        <p class="card-text">Active Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-warning text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $courseStats['courses_without_enrollments'] }}</h3>
                        <p class="card-text">Inactive Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-info text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $courseStats['average_enrollments'] }}</h3>
                        <p class="card-text">Avg. Enrollments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Creation Trend and Top Courses -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Course Creation Trend</h5>
                    </div>
                    <div class="card-body">
                        @if($courseCreation->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>New Courses</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($courseCreation->take(6) as $creation)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $creation->month)->format('M Y') }}</td>
                                            <td><span class="badge bg-success">{{ $creation->count }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No course creation data available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Enrolled Courses -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-trophy me-2"></i>Top Enrolled Courses</h5>
                    </div>
                    <div class="card-body">
                        @if($topCourses->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($topCourses->take(8) as $course)
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
                            <p class="text-muted">No enrollment data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses by Department -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-building me-2"></i>Courses by Department</h5>
                    </div>
                    <div class="card-body">
                        @if($coursesByDepartment->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Course Count</th>
                                            <th>Distribution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalCourses = $coursesByDepartment->sum('count'); @endphp
                                        @foreach($coursesByDepartment as $deptData)
                                        <tr>
                                            <td>{{ $deptData->department ?? 'Unknown Department' }}</td>
                                            <td><span class="badge bg-success">{{ $deptData->count }}</span></td>
                                            <td>
                                                @php $percentage = $totalCourses > 0 ? round(($deptData->count / $totalCourses) * 100, 1) : 0; @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                                        {{ $percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none">
                                @php $totalCourses = $coursesByDepartment->sum('count'); @endphp
                                @foreach($coursesByDepartment as $deptData)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="fw-bold">{{ $deptData->department ?? 'Unknown Department' }}</div>
                                            <span class="badge bg-success">{{ $deptData->count }}</span>
                                        </div>
                                        @php $percentage = $totalCourses > 0 ? round(($deptData->count / $totalCourses) * 100, 1) : 0; @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No department course data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
