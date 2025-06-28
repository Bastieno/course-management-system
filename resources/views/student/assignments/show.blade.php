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
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="{{ route('student.courses.show', $assignment->course) }}">
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
                <a href="{{ route('student.courses.show', $assignment->course) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <h1 class="mb-0">{{ $assignment->title }}</h1>
        </div>

        <!-- Desktop Layout: Side by Side -->
        <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
            <h1>{{ $assignment->title }}</h1>
            <a href="{{ route('student.courses.show', $assignment->course) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Course
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
            <!-- Assignment Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Assignment Details</h5>
                        @if($assignment->due_date->isPast())
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-clock me-1"></i>Overdue
                            </span>
                        @elseif($assignment->due_date->diffInDays() <= 3)
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-exclamation-triangle me-1"></i>Due Soon
                            </span>
                        @else
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check me-1"></i>Active
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Course:</strong> {{ $assignment->course->title }} ({{ $assignment->course->code }})
                            </div>
                            <div class="col-md-6">
                                <strong>Points:</strong> {{ $assignment->points }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Due Date:</strong> {{ $assignment->due_date->format('M d, Y \a\t g:i A') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Time Remaining:</strong>
                                @if($assignment->due_date->isPast())
                                    <span class="text-danger">Overdue by {{ $assignment->due_date->diffForHumans() }}</span>
                                @else
                                    <span class="text-success">{{ $assignment->due_date->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Instructions:</strong>
                            <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $assignment->description }}</div>
                        </div>
                    </div>
                </div>

                <!-- Submission Status -->
                @if($submission)
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Your Submission</h5>
                            @if($submission->grade !== null)
                                <span class="badge bg-primary fs-6">
                                    <i class="fas fa-star me-1"></i>Graded: {{ $submission->grade }}/{{ $assignment->points }}
                                </span>
                            @else
                                <span class="badge bg-info fs-6">
                                    <i class="fas fa-clock me-1"></i>Pending Review
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Submitted:</strong> {{ $submission->submitted_at->format('M d, Y \a\t g:i A') }}
                                <small class="text-muted">({{ $submission->submitted_at->diffForHumans() }})</small>
                            </div>

                            @if($submission->file_path)
                                <div class="mb-3">
                                    <strong>Attached File:</strong>
                                    <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                                        <i class="fas fa-download me-1"></i>{{ $submission->file_name }}
                                    </a>
                                </div>
                            @endif

                            <div class="mb-3">
                                <strong>Submission Content:</strong>
                                <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $submission->content }}</div>
                            </div>

                            @if($submission->feedback)
                                <div class="mb-3">
                                    <strong>Instructor Feedback:</strong>
                                    <div class="mt-2 p-3 bg-warning bg-opacity-10 border border-warning rounded" style="white-space: pre-wrap;">{{ $submission->feedback }}</div>
                                </div>
                            @endif

                            <!-- Update/Delete Actions -->
                            @if($submission->grade === null && !$assignment->due_date->isPast())
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateSubmissionModal">
                                        <i class="fas fa-edit me-1"></i>Update Submission
                                    </button>
                                    <form method="POST" action="{{ route('student.assignments.delete', $assignment) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete your submission? This action cannot be undone.')">
                                            <i class="fas fa-trash me-1"></i>Delete Submission
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Submission Action -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Submission</h6>
                    </div>
                    <div class="card-body text-center">
                        @if($submission)
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                                <p class="text-success mb-0">Assignment Submitted</p>
                            </div>
                            @if($submission->grade !== null)
                                <div class="alert alert-primary text-center">
                                    <i class="fas fa-star me-2"></i>
                                    <strong>Grade: {{ $submission->grade }}/{{ $assignment->points }}</strong>
                                    <br>
                                    <small>{{ number_format(($submission->grade / $assignment->points) * 100, 1) }}%</small>
                                </div>
                            @endif
                        @elseif($assignment->due_date->isPast())
                            <div class="mb-3">
                                <i class="fas fa-times-circle fa-3x text-danger mb-2"></i>
                                <p class="text-danger mb-0">Assignment Overdue</p>
                            </div>
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Submission Closed</strong>
                                <br>
                                <small>This assignment is past due and no longer accepts submissions.</small>
                            </div>
                        @else
                            <div class="mb-3">
                                <i class="fas fa-upload fa-3x text-primary mb-2"></i>
                                <p class="text-muted mb-0">Submit your assignment</p>
                            </div>
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#submitAssignmentModal">
                                <i class="fas fa-upload me-1"></i>Submit Assignment
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Assignment Info -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Assignment Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Points:</span>
                            <strong>{{ $assignment->points }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Due Date:</span>
                            <strong>{{ $assignment->due_date->format('M d, Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Due Time:</span>
                            <strong>{{ $assignment->due_date->format('g:i A') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Status:</span>
                            @if($submission)
                                @if($submission->grade !== null)
                                    <span class="badge bg-primary">Graded</span>
                                @else
                                    <span class="badge bg-info">Submitted</span>
                                @endif
                            @elseif($assignment->due_date->isPast())
                                <span class="badge bg-danger">Overdue</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Assignment Modal -->
    @if(!$submission && !$assignment->due_date->isPast())
        <div class="modal fade" id="submitAssignmentModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Submit Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="content" class="form-label">Assignment Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="8" required
                                          placeholder="Enter your assignment content here..."></textarea>
                                <div class="form-text">Provide your answer, solution, or response to the assignment.</div>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Attach File (Optional)</label>
                                <input type="file" class="form-control" id="file" name="file"
                                       accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                                <div class="form-text">Supported formats: PDF, DOC, DOCX, TXT, ZIP, RAR (Max: 10MB)</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload me-1"></i>Submit Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Update Submission Modal -->
    @if($submission && $submission->grade === null && !$assignment->due_date->isPast())
        <div class="modal fade" id="updateSubmissionModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('student.assignments.update', $assignment) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="update_content" class="form-label">Assignment Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="update_content" name="content" rows="8" required>{{ $submission->content }}</textarea>
                                <div class="form-text">Update your assignment content.</div>
                            </div>
                            <div class="mb-3">
                                <label for="update_file" class="form-label">Replace File (Optional)</label>
                                @if($submission->file_path)
                                    <div class="mb-2">
                                        <small class="text-muted">Current file: {{ $submission->file_name }}</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="update_file" name="file"
                                       accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                                <div class="form-text">Leave empty to keep current file. Supported formats: PDF, DOC, DOCX, TXT, ZIP, RAR (Max: 10MB)</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Submission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
