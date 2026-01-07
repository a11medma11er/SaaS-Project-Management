@extends('layouts.master')
@section('title')
    {{ isset($task) ? 'Edit Task' : 'Create Task' }}
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Tasks
        @endslot
        @slot('title')
            {{ isset($task) ? 'Edit Task' : 'Create Task' }}
        @endslot
    @endcomponent
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ isset($task) ? route('management.tasks.update', $task) : route('management.tasks.store') }}" 
          method="POST">
        @csrf
        @if(isset($task))
            @method('PUT')
        @endif
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="task-title-input">Task Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="task-title-input" name="title" 
                               value="{{ old('title', $task->title ?? '') }}" 
                               placeholder="Enter task title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Task Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="5" 
                                  placeholder="Enter task description">{{ old('description', $task->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" name="project_id">
                                    <option value="">Select Project (Optional)</option>
                                    @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                        {{ old('project_id', $task->project_id ?? '') == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Client Name</label>
                                <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                       name="client_name" 
                                       value="{{ old('client_name', $task->client_name ?? '') }}" 
                                       placeholder="Enter client name">
                                @error('client_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       name="due_date" 
                                       value="{{ old('due_date', isset($task) ? $task->due_date->format('Y-m-d') : '') }}" 
                                       required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="New" {{ old('status', $task->status ?? '') == 'New' ? 'selected' : '' }}>New</option>
                                    <option value="Pending" {{ old('status', $task->status ?? '') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Inprogress" {{ old('status', $task->status ?? '') == 'Inprogress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ old('status', $task->status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select @error('priority') is-invalid @enderror" name="priority" required>
                            <option value="Low" {{ old('priority', $task->priority ?? '') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ old('priority', $task->priority ?? 'Medium') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ old('priority', $task->priority ?? '') == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assign Users</h5>
                </div>
                <div class="card-body">
                    @foreach($users as $user)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="assigned_users[]" 
                               value="{{ $user->id }}" id="user-{{ $user->id }}"
                               {{ isset($task) && $task->assignedUsers->contains($user->id) ? 'checked' : '' }}>
                        <label class="form-check-label" for="user-{{ $user->id }}">
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar-xs rounded-circle me-2">
                                @else
                                <div class="avatar-xs me-2">
                                    <div class="avatar-title rounded-circle bg-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                </div>
                                @endif
                                <span>{{ $user->name }}</span>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control" name="tags[]" placeholder="Enter tags (comma separated)">
                    <small class="text-muted">Example: UI/UX, Design, Dashboard</small>
                </div>
            </div>

            <div class="text-end mb-3">
                <button type="submit" class="btn btn-success w-100">
                    <i class="ri-save-line align-bottom me-1"></i> {{ isset($task) ? 'Update Task' : 'Create Task' }}
                </button>
            </div>
        </div>
    </div>
    </form>
@endsection
