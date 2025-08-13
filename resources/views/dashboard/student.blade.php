<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="#">CMS Student</a>
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
        <h1>Student Dashboard</h1>

        <!-- Student Info Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>{{ auth()->user()->name }}</h5>
                                <p class="text-muted mb-1">Student ID: {{ auth()->user()->student_id ?? 'Not assigned' }}</p>
                                <p class="text-muted mb-1">Department: {{ auth()->user()->department ?? 'Not specified' }}</p>
                                <p class="text-muted">Level: {{ auth()->user()->level ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">{{ $stats['enrolled_courses'] }}</h5>
                        <p class="card-text">Enrolled Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">{{ $stats['pending_assignments'] }}</h5>
                        <p class="card-text">Recent Assignments</p>
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
                        <a href="{{ route('student.courses.index') }}" class="btn btn-primary me-2">Browse Courses</a>
                        <button class="btn btn-success me-2">Submit Assignment</button>
                        <button class="btn btn-info me-2">View Grades</button>
                        <button class="btn btn-warning">Send Message</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Enrolled Courses -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>My Courses</h5>
                    </div>
                    <div class="card-body">
                        @if($enrolled_courses->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($enrolled_courses as $course)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $course->title }}</h6>
                                        <p class="mb-1 text-muted">{{ $course->code }} - {{ $course->department }}</p>
                                        <small>{{ $course->assignments_count }} assignments</small>
                                    </div>
                                    <a href="{{ route('student.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">You are not enrolled in any courses yet.</p>
                                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">Browse Available Courses</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Assignments -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Assignments</h5>
                    </div>
                    <div class="card-body">
                        @if($recent_assignments->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recent_assignments as $assignment)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $assignment->title }}</h6>
                                        <small>{{ $assignment->due_date->format('M d, Y') }}</small>
                                    </div>
                                    <p class="mb-1">{{ $assignment->course->title }}</p>
                                    <small class="text-muted">Due: {{ $assignment->due_date->diffForHumans() }}</small>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No recent assignments.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
