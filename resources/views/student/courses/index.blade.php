<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Courses - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="fas fa-arrow-left me-2"></i>CMS Student
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
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <h1 class="mb-0">Course Enrollment</h1>
        </div>

        <!-- Desktop Layout: Side by Side -->
        <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
            <h1>Course Enrollment</h1>
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
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

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="courseTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab">
                    <i class="fas fa-search me-1"></i>Available Courses ({{ $availableCourses->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="enrolled-tab" data-bs-toggle="tab" data-bs-target="#enrolled" type="button" role="tab">
                    <i class="fas fa-book me-1"></i>My Courses ({{ $enrolledCourses->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content" id="courseTabsContent">
            <!-- Available Courses Tab -->
            <div class="tab-pane fade show active" id="available" role="tabpanel">
                @if($availableCourses->count() > 0)
                    <div class="row">
                        @foreach($availableCourses as $course)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $course->code }}</h6>
                                        <span class="badge bg-primary">{{ $course->credits }} Credits</span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->title }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $course->lecturer ? $course->lecturer->name : 'No lecturer assigned' }}
                                            </small>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-building me-1"></i>{{ $course->department }}
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-users me-1"></i>{{ $course->enrollments->count() }} students enrolled
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                            <form method="POST" action="{{ route('student.courses.enroll', $course) }}" class="flex-fill">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-plus me-1"></i>Enroll
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Available Courses</h4>
                        <p class="text-muted">You are already enrolled in all available courses or no courses have been created yet.</p>
                    </div>
                @endif
            </div>

            <!-- Enrolled Courses Tab -->
            <div class="tab-pane fade" id="enrolled" role="tabpanel">
                @if($enrolledCourses->count() > 0)
                    <div class="row">
                        @foreach($enrolledCourses as $course)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $course->code }}</h6>
                                        <span class="badge bg-light text-success">Enrolled</span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->title }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $course->lecturer ? $course->lecturer->name : 'No lecturer assigned' }}
                                            </small>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-building me-1"></i>{{ $course->department }}
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-tasks me-1"></i>{{ $course->assignments_count }} assignments
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i>View Course
                                            </a>
                                            <form method="POST" action="{{ route('student.courses.unenroll', $course) }}" class="flex-fill">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                        onclick="return confirm('Are you sure you want to unenroll from this course?')">
                                                    <i class="fas fa-times me-1"></i>Unenroll
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Enrolled Courses</h4>
                        <p class="text-muted">You haven't enrolled in any courses yet. Browse available courses to get started.</p>
                        <button class="btn btn-primary" onclick="document.getElementById('available-tab').click()">
                            <i class="fas fa-search me-1"></i>Browse Available Courses
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
