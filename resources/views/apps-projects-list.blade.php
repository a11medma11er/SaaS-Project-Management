@extends('layouts.master')
@section('title')
    @lang('translation.project-list')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Project
        @endslot
        @slot('title')
            Project List
        @endslot
    @endcomponent
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4 mb-3">
        <div class="col-sm-auto">
            <div>
                @can('create-projects')
                <a href="{{ route('management.projects.create') }}" class="btn btn-primary"><i class="ri-add-line align-bottom me-1"></i> Add
                    New</a>
                @endcan
            </div>
        </div>
        <div class="col-sm">
            <div class="d-flex justify-content-sm-end gap-2">
                <div class="search-box ms-2">
                    <input type="text" class="form-control" id="searchProject" placeholder="Search...">
                    <i class="ri-search-line search-icon"></i>
                </div>

                <select class="form-control w-md" id="filterStatus" data-choices data-choices-search-false>
                    <option value="">All Status</option>
                    <option value="Inprogress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($projects as $project)
        <div class="col-xxl-3 col-sm-6 project-card">
            <div class="card card-height-100">
                <div class="card-body">
                    <div class="d-flex flex-column h-100">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-4">Updated {{ $project->updated_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="d-flex gap-1 align-items-center">
                                    <button type="button" class="btn avatar-xs mt-n1 p-0 favourite-btn {{ $project->is_favorite ? 'active' : '' }}"
                                        data-project-id="{{ $project->id }}">
                                        <span class="avatar-title bg-transparent fs-15">
                                            <i class="ri-star-fill"></i>
                                        </span>
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <i data-feather="more-horizontal" class="icon-sm"></i>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('management.projects.show', $project->id) }}">
                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View
                                            </a>
                                            @can('edit-projects')
                                            <a class="dropdown-item" href="{{ route('management.projects.edit', $project->id) }}">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                            </a>
                                            @endcan
                                            @can('delete-projects')
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); 
                                                if(confirm('Are you sure?')) document.getElementById('delete-form-{{ $project->id }}').submit();">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Remove
                                            </a>
                                            <form id="delete-form-{{ $project->id }}" 
                                                action="{{ route('management.projects.destroy', $project->id) }}" 
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    @if($project->thumbnail)
                                        <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="" class="img-fluid rounded">
                                    @else
                                        <span class="avatar-title bg-primary-subtle text-primary rounded p-2">
                                            {{ strtoupper(substr($project->title, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1 fs-15">
                                    <a href="{{ route('management.projects.show', $project->id) }}" class="text-body">
                                        {{ $project->title }}
                                    </a>
                                </h5>
                                <p class="text-muted text-truncate-two-lines mb-3">
                                    {{ Str::limit(strip_tags($project->description), 100) }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex mb-2">
                                <div class="flex-grow-1">
                                    <div>Progress</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div>{{ $project->progress }}%</div>
                                </div>
                            </div>
                            <div class="progress progress-sm animated-progress">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                    aria-valuenow="{{ $project->progress }}" aria-valuemin="0"
                                    aria-valuemax="100" style="width: {{ $project->progress }}%;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end card body -->
                <div class="card-footer bg-transparent border-top-dashed py-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="avatar-group">
                                @foreach($project->members->take(3) as $member)
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-placement="top" title="{{ $member->name }}">
                                    <div class="avatar-xxs">
                                        @if($member->avatar)
                                            <img src="{{ asset('storage/' . $member->avatar) }}" alt="" class="rounded-circle img-fluid">
                                        @else
                                            <div class="avatar-title rounded-circle bg-primary">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                                @if($project->members->count() > 3)
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-placement="top" title="{{ $project->members->count() - 3 }} more">
                                    <div class="avatar-xxs">
                                        <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                            +{{ $project->members->count() - 3 }}
                                        </div>
                                    </div>
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="text-muted">
                                <i class="ri-calendar-event-fill me-1 align-bottom"></i> 
                                {{ $project->deadline->format('d M, Y') }}
                            </div>
                        </div>

                    </div>

                </div>
                <!-- end card footer -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                        colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                    </lord-icon>
                    <h5 class="mt-4">No Projects Found</h5>
                    <p class="text-muted">You haven't created any projects yet.</p>
                    @can('create-projects')
                    <a href="{{ route('management.projects.create') }}" class="btn btn-primary mt-2">
                        <i class="ri-add-line align-bottom me-1"></i> Create Project
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endforelse
    </div>
    <!-- end row -->

    <!-- Pagination -->
    <div class="row g-0 text-center text-sm-start align-items-center mb-4">
        <div class="col-sm-6">
            <div>
                <p class="mb-sm-0 text-muted">Showing <span class="fw-semibold">{{ $projects->firstItem() ?? 0 }}</span> to 
                    <span class="fw-semibold">{{ $projects->lastItem() ?? 0 }}</span> of 
                    <span class="fw-semibold text-decoration-underline">{{ $projects->total() }}</span> projects
                </p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="pagination-wrap hstack gap-2 justify-content-center justify-content-sm-end">
                {{ $projects->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script>
        // Favorite toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteButtons = document.querySelectorAll('.favourite-btn');
            
            favoriteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const projectId = this.dataset.projectId;
                    
                    fetch(`/management/projects/${projectId}/toggle-favorite`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.toggle('active');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchProject');
            const filterStatus = document.getElementById('filterStatus');
            
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        filterProjects();
                    }, 500);
                });
            }
            
            if (filterStatus) {
                filterStatus.addEventListener('change', function() {
                    filterProjects();
                });
            }
            
            function filterProjects() {
                const search = searchInput ? searchInput.value : '';
                const status = filterStatus ? filterStatus.value : '';
                const url = new URL(window.location.href);
                
                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');
                
                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');
                
                window.location.href = url.toString();
            }
        });
    </script>
    <script src="{{URL::asset('build/js/app.js') }}"></script>
@endsection
