<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Course Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isLecturer() ? route('lecturer.dashboard') : route('student.dashboard')) }}">
                CMS {{ ucfirst(auth()->user()->role) }}
            </a>
            <div class="navbar-nav ms-auto d-flex align-items-center flex-row">
                <span class="navbar-text me-2">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                                       id="profile_picture" name="profile_picture" accept="image/*">
                                <small class="text-muted">Maximum file size: 2MB</small>
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($user->profile_picture)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                             alt="Current Profile Picture" class="img-thumbnail" style="max-width: 150px;">
                                        <p class="text-muted mt-1">Current profile picture</p>
                                    </div>
                                @endif
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Change Password (Optional)</h5>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                <small class="text-muted">Leave blank to keep current password</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Account Information</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <input type="text" class="form-control" value="{{ $user->department ?? 'N/A' }}" readonly disabled>
                                </div>
                            </div>

                            @if($user->isStudent())
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Student ID</label>
                                    <input type="text" class="form-control" value="{{ $user->student_id ?? 'N/A' }}" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Level</label>
                                    <input type="text" class="form-control" value="{{ $user->level ?? 'N/A' }}" readonly disabled>
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Member Since</label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('F d, Y') }}" readonly disabled>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isLecturer() ? route('lecturer.dashboard') : route('student.dashboard')) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
