<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="{{ route('student.courses.index') }}">
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
                <a href="{{ route('student.courses.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <h1 class="mb-0">{{ $course->title }}</h1>
        </div>

        <!-- Desktop Layout: Side by Side -->
        <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
            <h1>{{ $course->title }}</h1>
            <a href="{{ route('student.courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Courses
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

        <div class="row">
            <!-- Course Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Course Information</h5>
                        @if($isEnrolled)
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check me-1"></i>Enrolled
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-times me-1"></i>Not Enrolled
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Course Code:</strong> {{ $course->code }}
                            </div>
                            <div class="col-md-6">
                                <strong>Credits:</strong> {{ $course->credits }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Department:</strong> {{ $course->department }}
                            </div>
                            <div class="col-md-6">
                                <strong>Level:</strong> {{ $course->level }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Semester:</strong> {{ $course->semester }}
                            </div>
                            <div class="col-md-6">
                                <strong>Lecturer:</strong> {{ $course->lecturer ? $course->lecturer->name : 'Not assigned' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Description:</strong>
                            <div class="mt-2" style="white-space: pre-wrap;">{{ $course->description ?: 'No description available.' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Assignments -->
                @if($isEnrolled && $course->assignments->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-tasks me-2"></i>Course Assignments ({{ $course->assignments->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($course->assignments as $assignment)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                <a href="{{ route('student.assignments.show', $assignment) }}" class="text-decoration-none">
                                                    {{ $assignment->title }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">Due: {{ $assignment->due_date->format('M d, Y') }}</small>
                                        </div>
                                        <div class="mb-1" style="white-space: pre-wrap;">{{ Str::limit($assignment->description, 150) }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-star me-1"></i>{{ $assignment->points }} points
                                                </small>
                                                @php
                                                    $submission = $assignment->submissions()->where('student_id', auth()->id())->first();
                                                @endphp
                                                @if($submission)
                                                    @if($submission->grade !== null)
                                                        <small class="text-primary">
                                                            <i class="fas fa-check-circle me-1"></i>Graded: {{ $submission->grade }}/{{ $assignment->points }}
                                                        </small>
                                                    @else
                                                        <small class="text-info">
                                                            <i class="fas fa-clock me-1"></i>Submitted
                                                        </small>
                                                    @endif
                                                @elseif($assignment->due_date->isPast())
                                                    <small class="text-danger">
                                                        <i class="fas fa-times me-1"></i>Not Submitted
                                                    </small>
                                                @else
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation me-1"></i>Pending
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($assignment->due_date->isPast())
                                                    <span class="badge bg-danger">Overdue</span>
                                                @elseif($assignment->due_date->diffInDays() <= 3)
                                                    <span class="badge bg-warning">Due Soon</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                                <a href="{{ route('student.assignments.show', $assignment) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($isEnrolled)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-tasks me-2"></i>Course Assignments
                            </h5>
                        </div>
                        <div class="card-body text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No assignments yet</h6>
                            <p class="text-muted">Your lecturer hasn't created any assignments for this course yet.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Enrollment Action -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Enrollment</h6>
                    </div>
                    <div class="card-body text-center">
                        @if($isEnrolled)
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                                <p class="text-success mb-0">You are enrolled in this course</p>
                            </div>

                            @php
                                $enrollment = auth()->user()->enrollments()->where('course_id', $course->id)->first();
                                $enrollmentDays = $enrollment ? $enrollment->enrolled_at->diffInDays(now()) : 0;
                                $maxUnenrollDays = 14;
                                $canUnenroll = $enrollmentDays <= $maxUnenrollDays;

                                // Check for submissions
                                $hasSubmissions = \App\Models\Submission::where('student_id', auth()->id())
                                    ->whereHas('assignment', function($query) use ($course) {
                                        $query->where('course_id', $course->id);
                                    })->exists();
                            @endphp

                            @if($canUnenroll && !$hasSubmissions)
                                <form method="POST" action="{{ route('student.courses.unenroll', $course) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                            onclick="return confirm('Are you sure you want to unenroll from this course? This action cannot be undone.')">
                                        <i class="fas fa-times me-1"></i>Unenroll from Course
                                    </button>
                                </form>
                                <small class="text-muted mt-2 d-block text-center">
                                    <i class="fas fa-info-circle me-1"></i>
                                    You can unenroll within {{ ceil($maxUnenrollDays - $enrollmentDays) }} more days
                                </small>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-lock me-2"></i>
                                    <strong>Unenrollment Not Available</strong>
                                    <br>
                                    @if(!$canUnenroll)
                                        <small>You cannot unenroll after {{ $maxUnenrollDays }} days of enrollment.</small>
                                    @endif
                                    @if($hasSubmissions)
                                        <small>You have submitted assignments for this course.</small>
                                    @endif
                                    <br>
                                    <small>Contact your academic advisor for assistance.</small>
                                </div>
                            @endif
                        @else
                            <div class="mb-3">
                                <i class="fas fa-plus-circle fa-3x text-primary mb-2"></i>
                                <p class="text-muted mb-0">Join this course to access assignments and materials</p>
                            </div>
                            <form method="POST" action="{{ route('student.courses.enroll', $course) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-plus me-1"></i>Enroll in Course
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Course Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Course Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Students:</span>
                            <strong>{{ $enrollmentCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Assignments:</span>
                            <strong>{{ $course->assignments->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Credits:</span>
                            <strong>{{ $course->credits }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Students (if enrolled) -->
                @if($isEnrolled && $course->enrollments->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Classmates</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $classmates = $course->enrollments->where('student_id', '!=', auth()->id())->take(5);
                                $totalClassmates = $course->enrollments->where('student_id', '!=', auth()->id())->count();
                            @endphp

                            @foreach($classmates as $enrollment)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <span class="text-truncate">{{ $enrollment->student->name }}</span>
                                </div>
                            @endforeach

                            @if($totalClassmates > 5)
                                <small class="text-muted">
                                    and {{ $totalClassmates - 5 }} more students...
                                </small>
                            @elseif($totalClassmates == 0)
                                <p class="text-muted text-center mb-0">
                                    <i class="fas fa-user-friends me-1"></i>
                                    No other students enrolled yet
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
