<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid px-2 px-sm-3 px-lg-4">
            <a class="navbar-brand" href="#" style="font-size: 1rem;">
                <span class="d-none d-sm-inline">CMS Lecturer</span>
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
        <h2 class="d-flex align-items-center"><i class="fas fa-chalkboard-teacher me-2" style="font-size: 1.5rem;"></i>Lecturer Dashboard</h2>

        <!-- Statistics Cards -->
        <div class="row mb-4 g-3">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['my_courses'] }}</h5>
                        <p class="card-text">My Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_students'] }}</h5>
                        <p class="card-text">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $stats['total_assignments'] }}</h5>
                        <p class="card-text">Total Assignments</p>
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
                            <button class="btn btn-primary" disabled title="Course creation is managed by administrators">
                                <i class="fas fa-plus me-1"></i>Create Course
                            </button>
                            <a href="{{ route('lecturer.assignments.create') }}" class="btn btn-success">
                                <i class="fas fa-tasks me-1"></i>Add Assignment
                            </a>
                            <a href="{{ route('lecturer.assignments.index') }}" class="btn btn-warning">
                                <i class="fas fa-check-circle me-1"></i>Grade Submissions
                            </a>
                            <button class="btn btn-info" disabled title="Material upload coming soon">
                                <i class="fas fa-upload me-1"></i>Upload Materials
                            </button>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Course creation is managed by administrators. Contact your admin to create new courses.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Courses -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>My Courses</h5>
                    </div>
                    <div class="card-body">
                        @if($my_courses->count() > 0)
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course Title</th>
                                            <th>Course Code</th>
                                            <th>Department</th>
                                            <th>Students</th>
                                            <th>Assignments</th>
                                            <th style="min-width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($my_courses as $course)
                                        <tr>
                                            <td>{{ $course->title }}</td>
                                            <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                            <td>{{ $course->department }}</td>
                                            <td>{{ $course->students_count }}</td>
                                            <td>{{ $course->assignments_count }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-outline-primary" disabled title="Course viewing coming soon">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" disabled title="Course editing managed by administrators">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none">
                                @foreach($my_courses as $course)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="fw-bold">{{ $course->title }}</div>
                                            <span class="badge bg-primary">{{ $course->code }}</span>
                                        </div>
                                        <div class="small text-muted mb-2">{{ $course->department }}</div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <small class="text-muted">Students:</small>
                                                <div class="fw-semibold">{{ $course->students_count }}</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Assignments:</small>
                                                <div class="fw-semibold">{{ $course->assignments_count }}</div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" disabled title="Course viewing coming soon">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" disabled title="Course editing managed by administrators">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No courses assigned yet.</p>
                                <p class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Contact your administrator to have courses assigned to you.
                                </p>
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
