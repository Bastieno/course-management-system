<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Reports - Course Management System</title>
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
                <li class="breadcrumb-item active" aria-current="page">Enrollment Reports</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-user-graduate me-2"></i>Enrollment Reports</h1>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Reports
            </a>
        </div>

        <!-- Enrollment Statistics -->
        <div class="row mb-4 g-3">
            <div class="col-lg-4 col-md-6">
                <div class="card text-center h-100 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $enrollmentStats['total_enrollments'] }}</h3>
                        <p class="card-text">Total Enrollments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card text-center h-100 bg-success text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $enrollmentStats['enrollments_this_month'] }}</h3>
                        <p class="card-text">This Month</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card text-center h-100 bg-info text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $enrollmentStats['enrollments_last_month'] }}</h3>
                        <p class="card-text">Last Month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Trends and Top Courses -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Enrollment Trends</h5>
                    </div>
                    <div class="card-body">
                        @if($enrollmentTrends->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Enrollments</th>
                                            <th>Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($enrollmentTrends->take(6) as $index => $trend)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}</td>
                                            <td><span class="badge bg-info">{{ $trend->count }}</span></td>
                                            <td>
                                                @if($index > 0)
                                                    @php
                                                        $prevCount = $enrollmentTrends->get($index - 1)->count ?? 0;
                                                        $change = $trend->count - $prevCount;
                                                    @endphp
                                                    @if($change > 0)
                                                        <i class="fas fa-arrow-up text-success"></i>
                                                        <small class="text-success">+{{ $change }}</small>
                                                    @elseif($change < 0)
                                                        <i class="fas fa-arrow-down text-danger"></i>
                                                        <small class="text-danger">{{ $change }}</small>
                                                    @else
                                                        <i class="fas fa-minus text-muted"></i>
                                                        <small class="text-muted">0</small>
                                                    @endif
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No enrollment trend data available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Growth -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Monthly Growth</h5>
                    </div>
                    <div class="card-body">
                        @if($enrollmentStats['enrollments_this_month'] > 0 || $enrollmentStats['enrollments_last_month'] > 0)
                            @php
                                $thisMonth = $enrollmentStats['enrollments_this_month'];
                                $lastMonth = $enrollmentStats['enrollments_last_month'];
                                $growth = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
                            @endphp
                            <div class="text-center">
                                <h2 class="text-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                    {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                                </h2>
                                <p class="text-muted">Growth from last month</p>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h5 class="text-info">{{ $thisMonth }}</h5>
                                            <small class="text-muted">This Month</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h5 class="text-secondary">{{ $lastMonth }}</h5>
                                            <small class="text-muted">Last Month</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">No enrollment data for comparison.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollments by Course -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list me-2"></i>Enrollments by Course</h5>
                    </div>
                    <div class="card-body">
                        @if($enrollmentsByCourse->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Code</th>
                                            <th>Enrollments</th>
                                            <th>Popularity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $maxEnrollments = $enrollmentsByCourse->max('enrollments_count'); @endphp
                                        @foreach($enrollmentsByCourse as $course)
                                        <tr>
                                            <td>{{ $course->title }}</td>
                                            <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                            <td><span class="badge bg-success">{{ $course->enrollments_count }}</span></td>
                                            <td>
                                                @php $percentage = $maxEnrollments > 0 ? round(($course->enrollments_count / $maxEnrollments) * 100, 1) : 0; @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%">
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
                                @php $maxEnrollments = $enrollmentsByCourse->max('enrollments_count'); @endphp
                                @foreach($enrollmentsByCourse as $course)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="fw-bold">{{ $course->title }}</div>
                                                <span class="badge bg-primary">{{ $course->code }}</span>
                                            </div>
                                            <span class="badge bg-success">{{ $course->enrollments_count }}</span>
                                        </div>
                                        @php $percentage = $maxEnrollments > 0 ? round(($course->enrollments_count / $maxEnrollments) * 100, 1) : 0; @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
