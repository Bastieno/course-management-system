<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }} - Course Management System</title>
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
                <li class="breadcrumb-item">
                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.index') : route('admin.assignments.index') }}">
                        Assignment Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $assignment->title }}</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="d-flex align-items-center"><i class="fas fa-eye me-2" style="font-size: 1.5rem;"></i>Assignment Details</h2>
            <div class="d-flex gap-2">
                <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.index') : route('admin.assignments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Assignments
                </a>
                <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.edit', $assignment) : route('admin.assignments.edit', $assignment) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit Assignment
                </a>
            </div>
        </div>

        <!-- Assignment Information -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Assignment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Title:</strong>
                                <p class="mb-0">{{ $assignment->title }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Course:</strong>
                                <p class="mb-0">
                                    <span class="badge bg-primary">{{ $assignment->course->code }}</span>
                                    {{ $assignment->course->title }}
                                </p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Due Date:</strong>
                                <p class="mb-0">
                                    {{ $assignment->due_date->format('M d, Y h:i A') }}
                                    @if($assignment->isOverdue())
                                        <span class="badge bg-danger ms-2">Overdue</span>
                                    @else
                                        <span class="badge bg-success ms-2">Active</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Maximum Score:</strong>
                                <p class="mb-0">{{ $assignment->points }} points</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <div class="mt-2 p-3 border rounded bg-light" style="white-space: pre-wrap;">{{ $assignment->description }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Created:</strong>
                                <p class="mb-0">{{ $assignment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Last Updated:</strong>
                                <p class="mb-0">{{ $assignment->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submissions -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-file-alt me-2"></i>Submissions</h5>
                        <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.submissions', $assignment) : route('admin.assignments.submissions', $assignment) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i>View All Submissions
                        </a>
                    </div>
                    <div class="card-body">
                        @if($assignment->submissions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Submitted</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignment->submissions->take(5) as $submission)
                                        <tr>
                                            <td>{{ $submission->student->name }}</td>
                                            <td>
                                                {{ $submission->submitted_at->format('M d, Y h:i A') }}
                                                @if($submission->submitted_at > $assignment->due_date)
                                                    <span class="badge bg-warning">Late</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission->grade !== null)
                                                    {{ $submission->grade }}/{{ $assignment->points }}
                                                @else
                                                    <span class="text-muted">Not graded</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission->grade !== null)
                                                    <span class="badge bg-success">Graded</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($assignment->submissions->count() > 5)
                                <div class="text-center mt-3">
                                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.submissions', $assignment) : route('admin.assignments.submissions', $assignment) }}" class="btn btn-outline-primary">
                                        View All {{ $assignment->submissions->count() }} Submissions
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No submissions yet</h6>
                                <p class="text-muted">Students haven't submitted their work for this assignment.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <h4 class="text-primary">{{ $submissionStats['total_submissions'] }}</h4>
                                    <small class="text-muted">Total Submissions</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-success">{{ $submissionStats['graded_submissions'] }}</h4>
                                <small class="text-muted">Graded</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <h4 class="text-warning">{{ $submissionStats['late_submissions'] }}</h4>
                                    <small class="text-muted">Late Submissions</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-info">
                                    @if($submissionStats['average_score'])
                                        {{ number_format($submissionStats['average_score'], 1) }}
                                    @else
                                        N/A
                                    @endif
                                </h4>
                                <small class="text-muted">Average Score</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($gradeDistribution->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-pie me-2"></i>Grade Distribution</h5>
                    </div>
                    <div class="card-body">
                        @foreach($gradeDistribution as $grade => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-{{ $grade == 'A' ? 'success' : ($grade == 'B' ? 'primary' : ($grade == 'C' ? 'warning' : ($grade == 'D' ? 'secondary' : 'danger'))) }}">
                                Grade {{ $grade }}
                            </span>
                            <span>{{ $count }} student{{ $count != 1 ? 's' : '' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
