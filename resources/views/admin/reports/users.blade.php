<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports - Course Management System</title>
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
                <li class="breadcrumb-item active" aria-current="page">User Reports</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-users me-2"></i>User Reports</h1>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Reports
            </a>
        </div>

        <!-- User Statistics by Role -->
        <div class="row mb-4 g-3">
            @foreach($usersByRole as $roleData)
            <div class="col-lg-4 col-md-6">
                <div class="card text-center h-100 bg-{{ $roleData->role == 'admin' ? 'danger' : ($roleData->role == 'lecturer' ? 'warning' : 'primary') }} text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $roleData->count }}</h3>
                        <p class="card-text">{{ ucfirst($roleData->role) }}{{ $roleData->count > 1 ? 's' : '' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Active Users -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2"></i>User Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-12">
                                <h3 class="text-success">{{ $activeUsers }}</h3>
                                <p class="text-muted">Active Users (Last 30 Days)</p>
                                <small class="text-muted">Users who have logged in recently</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Registration Trend -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-area me-2"></i>Registration Trend</h5>
                    </div>
                    <div class="card-body">
                        @if($userRegistrations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Registrations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($userRegistrations->take(6) as $registration)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $registration->month)->format('M Y') }}</td>
                                            <td><span class="badge bg-primary">{{ $registration->count }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No registration data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Users by Department -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-building me-2"></i>Users by Department</h5>
                    </div>
                    <div class="card-body">
                        @if($usersByDepartment->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>User Count</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalDepartmentUsers = $usersByDepartment->sum('count'); @endphp
                                        @foreach($usersByDepartment as $deptData)
                                        <tr>
                                            <td>{{ $deptData->department ?? 'Unknown Department' }}</td>
                                            <td><span class="badge bg-primary">{{ $deptData->count }}</span></td>
                                            <td>
                                                @php $percentage = $totalDepartmentUsers > 0 ? round(($deptData->count / $totalDepartmentUsers) * 100, 1) : 0; @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
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
                                @php $totalDepartmentUsers = $usersByDepartment->sum('count'); @endphp
                                @foreach($usersByDepartment as $deptData)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="fw-bold">{{ $deptData->department ?? 'Unknown Department' }}</div>
                                            <span class="badge bg-primary">{{ $deptData->count }}</span>
                                        </div>
                                        @php $percentage = $totalDepartmentUsers > 0 ? round(($deptData->count / $totalDepartmentUsers) * 100, 1) : 0; @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No department user data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
