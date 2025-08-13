@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Course Materials</h1>
                <p class="text-muted mb-0">{{ $course->title }} ({{ $course->code }})</p>
            </div>

            @if(auth()->user()->isAdmin() || (auth()->user()->isLecturer() && $course->lecturer_id === auth()->id()))
                <a href="{{ route(auth()->user()->isAdmin() ? 'admin' : 'lecturer' . '.courses.materials.create', $course) }}"
                   class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Material
                </a>
            @endif
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($materials->count() > 0)
            <div class="row g-3">
                @foreach($materials as $material)
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="{{ $material->file_icon }} fs-2"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1">{{ $material->title }}</h5>
                                            <p class="text-muted small mb-2">{{ ucfirst($material->type) }} • {{ $material->created_at->format('M d, Y') }}</p>
                                            @if($material->description)
                                                <p class="card-text mb-2">{{ $material->description }}</p>
                                            @endif
                                            @if($material->file_size)
                                                <p class="text-muted small mb-0">Size: {{ $material->file_size }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        @if($material->type === 'link')
                                            <a href="{{ $material->file_path }}" target="_blank"
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-external-link-alt me-1"></i>Open Link
                                            </a>
                                        @else
                                            <a href="{{ route(auth()->user()->isAdmin() ? 'admin' : (auth()->user()->isLecturer() ? 'lecturer' : 'student') . '.courses.materials.download', [$course, $material]) }}"
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        @endif

                                        @if(auth()->user()->isAdmin() || (auth()->user()->isLecturer() && $course->lecturer_id === auth()->id()))
                                            <a href="{{ route(auth()->user()->isAdmin() ? 'admin' : 'lecturer' . '.courses.materials.edit', [$course, $material]) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>

                                            <form action="{{ route(auth()->user()->isAdmin() ? 'admin' : 'lecturer' . '.courses.materials.destroy', [$course, $material]) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this material?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open display-1 text-muted mb-3"></i>
                <h4 class="text-muted mb-2">No Materials Available</h4>
                <p class="text-muted">
                    @if(auth()->user()->isAdmin() || (auth()->user()->isLecturer() && $course->lecturer_id === auth()->id()))
                        Start by adding some course materials for your students.
                    @else
                        Your instructor hasn't uploaded any materials yet.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <div class="card-footer">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Course
        </a>
    </div>
</div>
@endsection
