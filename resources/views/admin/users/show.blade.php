<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">CMS Admin</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- User Profile Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">User Profile</h5>
                        <div>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Profile Picture Section -->
                            <div class="col-md-3 text-center mb-4">
                                <div class="avatar-large mx-auto mb-3">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <h6 class="mb-1">{{ $user->name }}</h6>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'lecturer' ? 'warning' : 'primary') }} mb-2">
                                    {{ ucfirst($user->role) }}
                                </span>
                                @if($user->id == auth()->id())
                                    <div>
                                        <span class="badge bg-info">Current User</span>
                                    </div>
                                @endif
                            </div>

                            <!-- User Details -->
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <p class="form-control-plaintext">{{ $user->name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <p class="form-control-plaintext">
                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Role</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'lecturer' ? 'warning' : 'primary') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Department</label>
                                        <p class="form-control-plaintext">{{ $user->department ?? 'Not specified' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <p class="form-control-plaintext">
                                            @if($user->phone)
                                                <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                            @else
                                                Not provided
                                            @endif
                                        </p>
                                    </div>
                                    @if($user->student_id)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Student ID</label>
                                        <p class="form-control-plaintext">{{ $user->student_id }}</p>
                                    </div>
                                    @endif
                                    @if($user->level)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Level</label>
                                        <p class="form-control-plaintext">{{ $user->level }} Level</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Account Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Created</label>
                                <p class="form-control-plaintext">
                                    {{ $user->created_at->format('l, F j, Y \a\t g:i A') }}
                                    <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Last Updated</label>
                                <p class="form-control-plaintext">
                                    {{ $user->updated_at->format('l, F j, Y \a\t g:i A') }}
                                    <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role-specific Information -->
                @if($user->role == 'lecturer')
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Lecturer Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ $user->courses()->count() }}</h4>
                                    <p class="mb-0">Courses Teaching</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-success">{{ $user->courses()->withCount('students')->get()->sum('students_count') }}</h4>
                                    <p class="mb-0">Total Students</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-warning">{{ $user->courses()->withCount('assignments')->get()->sum('assignments_count') }}</h4>
                                    <p class="mb-0">Assignments Created</p>
                                </div>
                            </div>
                        </div>
                        @if($user->courses()->count() > 0)
                        <hr>
                        <h6>Current Courses:</h6>
                        <div class="row">
                            @foreach($user->courses()->latest()->take(6)->get() as $course)
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book text-muted me-2"></i>
                                    <div>
                                        <strong>{{ $course->code }}</strong><br>
                                        <small class="text-muted">{{ $course->title }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($user->role == 'student')
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-user-graduate"></i> Student Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ $user->enrolledCourses()->count() }}</h4>
                                    <p class="mb-0">Enrolled Courses</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-success">{{ $user->submissions()->count() }}</h4>
                                    <p class="mb-0">Submissions</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-info">{{ $user->level ?? 'N/A' }}</h4>
                                    <p class="mb-0">Current Level</p>
                                </div>
                            </div>
                        </div>
                        @if($user->enrolledCourses()->count() > 0)
                        <hr>
                        <h6>Enrolled Courses:</h6>
                        <div class="row">
                            @foreach($user->enrolledCourses()->latest()->take(6)->get() as $course)
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book text-muted me-2"></i>
                                    <div>
                                        <strong>{{ $course->code }}</strong><br>
                                        <small class="text-muted">{{ $course->title }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit User
                                </a>
                            </div>
                            @if($user->id !== auth()->id())
                            <div>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete User
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
        }
        .form-control-plaintext {
            margin-bottom: 0;
        }
    </style>
</body>
</html>
