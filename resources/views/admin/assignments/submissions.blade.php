<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }} - Submissions - Course Management System</title>
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
                <li class="breadcrumb-item">
                    <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.show', $assignment) : route('admin.assignments.show', $assignment) }}">
                        {{ $assignment->title }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Submissions</li>
            </ol>
        </nav>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="d-flex align-items-center mb-1">
                    <i class="fas fa-file-alt me-2" style="font-size: 1.5rem;"></i>Submissions
                </h2>
                <p class="text-muted mb-0">{{ $assignment->title }} - {{ $assignment->course->title }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ auth()->user()->role === 'lecturer' ? route('lecturer.assignments.show', $assignment) : route('admin.assignments.show', $assignment) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Assignment
                </a>
                @if($submissions->count() > 0)
                    <button type="button" class="btn btn-outline-primary" onclick="exportGrades()">
                        <i class="fas fa-download me-1"></i>Export Grades
                    </button>
                @endif
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $submissions->total() }}</h3>
                        <p class="card-text">Total Submissions</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-success text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $submissions->where('grade', '!=', null)->count() }}</h3>
                        <p class="card-text">Graded</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-warning text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">{{ $submissions->where('grade', null)->count() }}</h3>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100 bg-info text-white">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title">
                            @if($submissions->where('grade', '!=', null)->count() > 0)
                                {{ number_format($submissions->where('grade', '!=', null)->avg('grade'), 1) }}
                            @else
                                N/A
                            @endif
                        </h3>
                        <p class="card-text">Average Grade</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-filter me-2"></i>Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Student</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search by student name...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Submissions</option>
                                <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late Submissions</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="submitted_at" {{ request('sort') == 'submitted_at' ? 'selected' : '' }}>Submission Date</option>
                                <option value="student_name" {{ request('sort') == 'student_name' ? 'selected' : '' }}>Student Name</option>
                                <option value="grade" {{ request('sort') == 'grade' ? 'selected' : '' }}>Grade</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list me-2"></i>Submissions ({{ $submissions->total() }})</h5>
                @if($submissions->where('grade', null)->count() > 0)
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="bulkGradeModal()">
                        <i class="fas fa-tasks me-1"></i>Bulk Grade
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if($submissions->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                    <th>File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                                {{ strtoupper(substr($submission->student->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $submission->student->name }}</div>
                                                <small class="text-muted">{{ $submission->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $submission->submitted_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $submission->submitted_at->format('h:i A') }}</small>
                                        @if($submission->isLate())
                                            <br><span class="badge bg-warning">Late</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->grade !== null)
                                            <span class="badge bg-success">Graded</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->grade !== null)
                                            <div class="fw-bold">{{ $submission->grade }}/{{ $assignment->points }}</div>
                                            <small class="text-muted">{{ $submission->getScorePercentage() }}% ({{ $submission->getLetterGrade() }})</small>
                                        @else
                                            <span class="text-muted">Not graded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->file_path)
                                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>{{ Str::limit($submission->file_name, 15) }}
                                            </a>
                                        @else
                                            <span class="text-muted">No file</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="viewSubmission({{ $submission->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="gradeSubmission({{ $submission->id }})">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-lg-none">
                        @foreach($submissions as $submission)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                                            {{ strtoupper(substr($submission->student->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $submission->student->name }}</div>
                                            <small class="text-muted">{{ $submission->student->email }}</small>
                                        </div>
                                    </div>
                                    @if($submission->grade !== null)
                                        <span class="badge bg-success">Graded</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>

                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted">Submitted:</small>
                                        <div>{{ $submission->submitted_at->format('M d, Y h:i A') }}</div>
                                        @if($submission->isLate())
                                            <span class="badge bg-warning">Late</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Grade:</small>
                                        @if($submission->grade !== null)
                                            <div class="fw-bold">{{ $submission->grade }}/{{ $assignment->points }}</div>
                                            <small class="text-muted">{{ $submission->getScorePercentage() }}% ({{ $submission->getLetterGrade() }})</small>
                                        @else
                                            <div class="text-muted">Not graded</div>
                                        @endif
                                    </div>
                                </div>

                                @if($submission->file_path)
                                    <div class="mb-2">
                                        <small class="text-muted">File:</small>
                                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-download me-1"></i>{{ $submission->file_name }}
                                        </a>
                                    </div>
                                @endif

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="viewSubmission({{ $submission->id }})">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="gradeSubmission({{ $submission->id }})">
                                        <i class="fas fa-star me-1"></i>Grade
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $submissions->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No submissions found</h5>
                        <p class="text-muted">Students haven't submitted their work for this assignment yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- View Submission Modal -->
    <div class="modal fade" id="viewSubmissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="submissionContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="gradeFromView">
                        <i class="fas fa-star me-1"></i>Grade This Submission
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Submission Modal -->
    <div class="modal fade" id="gradeSubmissionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Grade Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="gradeForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="grade" class="form-label">Grade <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="grade" name="grade"
                                       min="0" max="{{ $assignment->points }}" required>
                                <span class="input-group-text">/ {{ $assignment->points }}</span>
                            </div>
                            <div class="form-text">Enter a grade between 0 and {{ $assignment->points }} points.</div>
                        </div>
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Feedback (Optional)</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="4"
                                      placeholder="Provide feedback to the student..."></textarea>
                        </div>
                        <div id="studentInfo" class="alert alert-light">
                            <!-- Student info will be loaded here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Save Grade
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Submission data for JavaScript
        const submissions = @json($submissions->items());
        const assignment = @json($assignment);

        function viewSubmission(submissionId) {
            const submission = submissions.find(s => s.id === submissionId);
            if (!submission) return;

            const content = `
                <div class="mb-3">
                    <strong>Student:</strong> ${submission.student.name} (${submission.student.email})
                </div>
                <div class="mb-3">
                    <strong>Submitted:</strong> ${new Date(submission.submitted_at).toLocaleString()}
                    ${submission.submitted_at > assignment.due_date ? '<span class="badge bg-warning ms-2">Late</span>' : ''}
                </div>
                ${submission.file_path ? `
                    <div class="mb-3">
                        <strong>Attached File:</strong>
                        <a href="/storage/${submission.file_path}" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                            <i class="fas fa-download me-1"></i>${submission.file_name}
                        </a>
                    </div>
                ` : ''}
                <div class="mb-3">
                    <strong>Content:</strong>
                    <div class="mt-2 p-3 border rounded bg-light" style="white-space: pre-wrap;">${submission.content}</div>
                </div>
                ${submission.grade !== null ? `
                    <div class="mb-3">
                        <strong>Current Grade:</strong> ${submission.grade}/${assignment.points} (${Math.round((submission.grade / assignment.points) * 100)}%)
                    </div>
                    ${submission.feedback ? `
                        <div class="mb-3">
                            <strong>Feedback:</strong>
                            <div class="mt-2 p-3 border rounded bg-warning bg-opacity-10">${submission.feedback}</div>
                        </div>
                    ` : ''}
                ` : ''}
            `;

            document.getElementById('submissionContent').innerHTML = content;
            document.getElementById('gradeFromView').onclick = () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('viewSubmissionModal'));
                modal.hide();
                gradeSubmission(submissionId);
            };

            new bootstrap.Modal(document.getElementById('viewSubmissionModal')).show();
        }

        function gradeSubmission(submissionId) {
            const submission = submissions.find(s => s.id === submissionId);
            if (!submission) return;

            // Set form action
            const form = document.getElementById('gradeForm');
            const route = '{{ auth()->user()->role === "lecturer" ? "lecturer" : "admin" }}';
            form.action = `/${route}/assignments/${assignment.id}/submissions/${submissionId}/grade`;

            // Pre-fill existing grade and feedback
            document.getElementById('grade').value = submission.grade || '';
            document.getElementById('feedback').value = submission.feedback || '';

            // Show student info
            document.getElementById('studentInfo').innerHTML = `
                <strong>Student:</strong> ${submission.student.name}<br>
                <strong>Submitted:</strong> ${new Date(submission.submitted_at).toLocaleString()}
                ${submission.submitted_at > assignment.due_date ? '<br><span class="badge bg-warning">Late Submission</span>' : ''}
            `;

            new bootstrap.Modal(document.getElementById('gradeSubmissionModal')).show();
        }

        function exportGrades() {
            // Create CSV content
            let csv = 'Student Name,Email,Submitted,Grade,Percentage,Letter Grade,Status\n';

            submissions.forEach(submission => {
                const percentage = submission.grade ? Math.round((submission.grade / assignment.points) * 100) : '';
                const letterGrade = submission.grade ? getLetterGrade(percentage) : '';
                const status = submission.grade !== null ? 'Graded' : 'Pending';
                const isLate = submission.submitted_at > assignment.due_date ? ' (Late)' : '';

                csv += `"${submission.student.name}","${submission.student.email}","${new Date(submission.submitted_at).toLocaleDateString()}${isLate}","${submission.grade || ''}","${percentage}","${letterGrade}","${status}"\n`;
            });

            // Download CSV
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${assignment.title.replace(/[^a-z0-9]/gi, '_')}_grades.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function getLetterGrade(percentage) {
            if (percentage >= 90) return 'A';
            if (percentage >= 80) return 'B';
            if (percentage >= 70) return 'C';
            if (percentage >= 60) return 'D';
            return 'F';
        }

        function bulkGradeModal() {
            // This would open a bulk grading interface
            alert('Bulk grading feature coming soon!');
        }
    </script>
</body>
</html>
