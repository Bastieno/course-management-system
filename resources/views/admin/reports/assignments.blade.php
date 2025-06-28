<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Reports - Course Management System</title>
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
                <li class="breadcrumb-item active" aria-current="page">Assignment Reports</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tasks me-2"></i>Assignment Reports</h1>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Reports
            </a>
        </div>

        <!-- Assignment Statistics -->
        <div class="row mb-4 g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $assignmentStats['total_assignments'] }}</h3>
                        <p class="card-text">Total Assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-success text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $assignmentStats['assignments_this_month'] }}</h3>
                        <p class="card-text">This Month</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-info text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $assignmentStats['upcoming_assignments'] }}</h3>
                        <p class="card-text">Upcoming</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-warning text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $assignmentStats['overdue_assignments'] }}</h3>
                        <p class="card-text">Overdue</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Trends and Top Courses -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Assignment Creation Trends</h5>
                    </div>
                    <div class="card-body">
                        @if($assignmentTrends->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>New Assignments</th>
                                            <th>Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignmentTrends->take(6) as $index => $trend)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}</td>
                                            <td><span class="badge bg-primary">{{ $trend->count }}</span></td>
                                            <td>
                                                @if($index > 0)
                                                    @php
                                                        $prevCount = $assignmentTrends->get($index - 1)->count ?? 0;
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
                            <p class="text-muted">No assignment trend data available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assignment Status Overview -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie me-2"></i>Assignment Status Overview</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $totalAssignments = $assignmentStats['total_assignments'];
                            $upcoming = $assignmentStats['upcoming_assignments'];
                            $overdue = $assignmentStats['overdue_assignments'];
                            $completed = $totalAssignments - $upcoming - $overdue;
                        @endphp

                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-2">
                                    <h4 class="text-success">{{ $completed }}</h4>
                                    <small class="text-muted">Completed</small>
                                </div>
                                @if($totalAssignments > 0)
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ round(($completed / $totalAssignments) * 100, 1) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ round(($completed / $totalAssignments) * 100, 1) }}%</small>
                                @endif
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <h4 class="text-info">{{ $upcoming }}</h4>
                                    <small class="text-muted">Upcoming</small>
                                </div>
                                @if($totalAssignments > 0)
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-info" style="width: {{ round(($upcoming / $totalAssignments) * 100, 1) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ round(($upcoming / $totalAssignments) * 100, 1) }}%</small>
                                @endif
                            </div>
                            <div class="col-4">
                                <div class="mb-2">
                                    <h4 class="text-warning">{{ $overdue }}</h4>
                                    <small class="text-muted">Overdue</small>
                                </div>
                                @if($totalAssignments > 0)
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: {{ round(($overdue / $totalAssignments) * 100, 1) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ round(($overdue / $totalAssignments) * 100, 1) }}%</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments by Course -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list me-2"></i>Assignments by Course</h5>
                    </div>
                    <div class="card-body">
                        @if($assignmentsByCourse->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Code</th>
                                            <th>Assignments</th>
                                            <th>Activity Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $maxAssignments = $assignmentsByCourse->max('assignments_count'); @endphp
                                        @foreach($assignmentsByCourse as $course)
                                        <tr>
                                            <td>{{ $course->title }}</td>
                                            <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                            <td><span class="badge bg-warning">{{ $course->assignments_count }}</span></td>
                                            <td>
                                                @php $percentage = $maxAssignments > 0 ? round(($course->assignments_count / $maxAssignments) * 100, 1) : 0; @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%">
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
                                @php $maxAssignments = $assignmentsByCourse->max('assignments_count'); @endphp
                                @foreach($assignmentsByCourse as $course)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="fw-bold">{{ $course->title }}</div>
                                                <span class="badge bg-primary">{{ $course->code }}</span>
                                            </div>
                                            <span class="badge bg-warning">{{ $course->assignments_count }}</span>
                                        </div>
                                        @php $percentage = $maxAssignments > 0 ? round(($course->assignments_count / $maxAssignments) * 100, 1) : 0; @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No assignment data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
