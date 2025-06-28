<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $department->name }} - Course Management System</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ $department->name }} Department</h1>
            <div>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Departments
                </a>
                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Department
                </a>
            </div>
        </div>

        <!-- Department Overview -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Department Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Department Name:</strong> {{ $department->name }}</p>
                                <p><strong>Department Code:</strong>
                                    <span class="badge bg-primary">{{ $department->code }}</span>
                                </p>
                                <p><strong>Status:</strong>
                                    @if($department->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Head of Department:</strong> {{ $department->head_of_department ?? 'Not assigned' }}</p>
                                <p><strong>Building:</strong> {{ $department->building ?? 'Not specified' }}</p>
                                <p><strong>Created:</strong> {{ $department->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        @if($department->description)
                            <hr>
                            <p><strong>Description:</strong></p>
                            <p class="text-muted">{{ $department->description }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Statistics Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Statistics</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6">
                                <h3 class="text-primary">{{ $stats['total_courses'] }}</h3>
                                <p class="mb-0">Courses</p>
                            </div>
                            <div class="col-6">
                                <h3 class="text-info">{{ $stats['total_users'] }}</h3>
                                <p class="mb-0">Users</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <h4 class="text-success">{{ $stats['total_lecturers'] }}</h4>
                                <p class="mb-0">Lecturers</p>
                            </div>
                            <div class="col-6">
                                <h4 class="text-warning">{{ $stats['total_students'] }}</h4>
                                <p class="mb-0">Students</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                @if($department->phone || $department->email)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Contact Information</h5>
                        </div>
                        <div class="card-body">
                            @if($department->phone)
                                <p><i class="fas fa-phone text-primary"></i> {{ $department->phone }}</p>
                            @endif
                            @if($department->email)
                                <p><i class="fas fa-envelope text-primary"></i>
                                    <a href="mailto:{{ $department->email }}">{{ $department->email }}</a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Courses Section -->
        @if($department->courses()->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Department Courses ({{ $department->courses()->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Title</th>
                                    <th>Credits</th>
                                    <th>Level</th>
                                    <th>Lecturer</th>
                                    <th>Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->courses as $course)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->credits }}</td>
                                    <td>{{ $course->level }} Level</td>
                                    <td>{{ $course->lecturer->name ?? 'Not assigned' }}</td>
                                    <td><span class="badge bg-info">{{ $course->students()->count() }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Users Section -->
        @if($department->users()->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Department Users ({{ $department->users()->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role == 'admin')
                                            <span class="badge bg-danger">Admin</span>
                                        @elseif($user->role == 'lecturer')
                                            <span class="badge bg-warning">Lecturer</span>
                                        @else
                                            <span class="badge bg-info">Student</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($department->courses()->count() == 0 && $department->users()->count() == 0)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h5>No Associated Data</h5>
                    <p class="text-muted">This department doesn't have any courses or users associated with it yet.</p>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
