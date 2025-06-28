<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department - Course Management System</title>
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

    <div class="container my-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Department</h5>
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Departments
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                            @csrf
                            @method('PUT')

                            <!-- Department Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $department->name) }}"
                                           placeholder="e.g., Computer Science" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="code" class="form-label">Department Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code', $department->code) }}"
                                           placeholder="e.g., CSC" required maxlength="10">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Department Description -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="description" class="form-label">Department Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Brief description of the department's mission and programs">{{ old('description', $department->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Department Head and Location -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="head_of_department" class="form-label">Head of Department</label>
                                    <input type="text" class="form-control @error('head_of_department') is-invalid @enderror"
                                           id="head_of_department" name="head_of_department" value="{{ old('head_of_department', $department->head_of_department) }}"
                                           placeholder="e.g., Dr. John Smith">
                                    @error('head_of_department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="building" class="form-label">Building/Location</label>
                                    <input type="text" class="form-control @error('building') is-invalid @enderror"
                                           id="building" name="building" value="{{ old('building', $department->building) }}"
                                           placeholder="e.g., Science Complex A">
                                    @error('building')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $department->phone) }}"
                                           placeholder="e.g., +234-123-456-7890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $department->email) }}"
                                           placeholder="e.g., csc@university.edu">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Department Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label">Department Status</label>
                                    <select class="form-select @error('is_active') is-invalid @enderror"
                                            id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', $department->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $department->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Department
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Department Info Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Department Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Created:</strong> {{ $department->created_at->format('M d, Y \a\t g:i A') }}</p>
                                <p><strong>Last Updated:</strong> {{ $department->updated_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Associated Courses:</strong> {{ $department->courses()->count() }}</p>
                                <p><strong>Associated Users:</strong> {{ $department->users()->count() }}</p>
                            </div>
                        </div>
                        @if($department->courses()->count() > 0 || $department->users()->count() > 0)
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> This department has associated courses or users. Changing the department name may affect existing relationships.
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
