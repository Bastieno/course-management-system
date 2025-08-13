@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="h3 mb-1">Add Course Material</h1>
        <p class="text-muted mb-0">{{ $course->title }} ({{ $course->code }})</p>
    </div>

    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="material-form" action="{{ route(auth()->user()->isAdmin() ? 'admin' : 'lecturer' . '.courses.materials.store', $course) }}"
              method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="form-control">
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type *</label>
                <select id="type" name="type" required onchange="toggleFields()" class="form-select">
                    <option value="">Select type...</option>
                    <option value="pdf" {{ old('type') == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                    <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>Document (Word, PowerPoint)</option>
                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                    <option value="link" {{ old('type') == 'link' ? 'selected' : '' }}>External Link</option>
                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div id="file-field" class="mb-3">
                <label for="file" class="form-label">File</label>
                <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar,.mp4,.avi,.mov,.wmv"
                       class="form-control">
                <div class="form-text">Maximum file size: 50MB. Supported formats: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, RAR, MP4, AVI, MOV, WMV</div>
            </div>

            <div id="link-field" class="mb-3" style="display: none;">
                <label for="link_url" class="form-label">URL</label>
                <input type="url" id="link_url" name="link_url" value="{{ old('link_url') }}" placeholder="https://example.com"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Optional description of the material..."
                          class="form-control">{{ old('description') }}</textarea>
            </div>
        </form>
    </div>

    <div class="card-footer">
        <div class="d-flex justify-content-between">
            <a href="{{ route(auth()->user()->isAdmin() ? 'admin' : 'lecturer' . '.courses.materials.index', $course) }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Cancel
            </a>

            <button type="submit" form="material-form" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Add Material
            </button>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('type').value;
    const fileField = document.getElementById('file-field');
    const linkField = document.getElementById('link-field');

    if (type === 'link') {
        fileField.style.display = 'none';
        linkField.style.display = 'block';
        document.getElementById('file').required = false;
        document.getElementById('link_url').required = true;
    } else {
        fileField.style.display = 'block';
        linkField.style.display = 'none';
        document.getElementById('file').required = false; // Optional for other types
        document.getElementById('link_url').required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>
@endsection
