@extends('layouts.master')

@section('title') Create AI Prompt @endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="ri-add-line"></i> Create New Prompt
                </h4>

                <div class="page-title-right">
                    <a href="{{ route('ai.prompts.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('ai.prompts.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Prompt Details</h5>

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Name <span class="text-danger">*</span>
                                <small class="text-muted">(lowercase, alphanumeric, dash, and underscore only)</small>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="task_analysis_prompt"
                                   pattern="[a-z0-9_\-]+"
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unique identifier for this prompt</small>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type...</option>
                                <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>System</option>
                                <option value="user" {{ old('type') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="assistant" {{ old('type') == 'assistant' ? 'selected' : '' }}>Assistant</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                        <option value="">No Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <select class="form-select select2-tags @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                                        @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, (array)old('tags', [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Select multiple tags to organize this prompt</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      maxlength="1000"
                                      placeholder="Brief description of what this prompt does...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="template" class="form-label">
                                Template <span class="text-danger">*</span>
                                <small class="text-muted">(Use @{{ variable }} syntax for variables)</small>
                            </label>
                            <textarea class="form-control @error('template') is-invalid @enderror" 
                                      id="template" 
                                      name="template" 
                                      rows="15"
                                      placeholder="Example:&#10;&#10;Analyze the task: &lcub;&lcub;task_title&rcub;&rcub;&#10;Description: &lcub;&lcub;task_description&rcub;&rcub;&#10;Status: &lcub;&lcub;status&rcub;&rcub;"
                                      required>{{ old('template') }}</textarea>
                            @error('template')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="variables-list" class="mt-2"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Create Prompt
                            </button>
                            <button type="button" id="test-btn" class="btn btn-soft-info">
                                <i class="ri-flask-line"></i> Quick Test
                            </button>
                            <a href="{{ route('ai.prompts.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/markdown/markdown.min.js"></script>
<script>
// Initialize Select2
$('.select2-tags').select2({
    placeholder: 'Select tags...',
    allowClear: true,
    width: '100%'
});

let editor = CodeMirror.fromTextArea(document.getElementById('template'), {
    mode: 'markdown',
    theme: 'monokai',
    lineNumbers: true,
    lineWrapping: true,
    height: '400px'
});

// Extract and display variables
function updateVariables() {
    const template = editor.getValue();
    const regex = /\{\{([^}]+)\}\}/g;
    const matches = [...template.matchAll(regex)];
    const variables = [...new Set(matches.map(m => m[1]))];
    
    const list = document.getElementById('variables-list');
    if (variables.length > 0) {
        list.innerHTML = '<div class="alert alert-info"><strong>Detected Variables:</strong> ' + 
            variables.map(v => '<code>{{' + v + '}}</code>').join(', ') + '</div>';
    } else {
        list.innerHTML = '';
    }
}

// Sync CodeMirror with textarea on every change (not just on submit)
// This fixes the "invalid form control is not focusable" error
editor.on('change', function() {
    updateVariables();
    document.getElementById('template').value = editor.getValue();
});

// Quick test
document.getElementById('test-btn').addEventListener('click', function() {
    alert('Test functionality coming in next version!');
});
</script>
@endsection
