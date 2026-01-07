
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.project-list'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Project
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Project List
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4 mb-3">
        <div class="col-sm-auto">
            <div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-projects')): ?>
                <a href="<?php echo e(route('management.projects.create')); ?>" class="btn btn-primary"><i class="ri-add-line align-bottom me-1"></i> Add
                    New</a>
                <?php endif; ?>
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
        <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-xxl-3 col-sm-6 project-card">
            <div class="card card-height-100">
                <div class="card-body">
                    <div class="d-flex flex-column h-100">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-4">Updated <?php echo e($project->updated_at->diffForHumans()); ?></p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="d-flex gap-1 align-items-center">
                                    <button type="button" class="btn avatar-xs mt-n1 p-0 favourite-btn <?php echo e($project->is_favorite ? 'active' : ''); ?>"
                                        data-project-id="<?php echo e($project->id); ?>">
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
                                            <a class="dropdown-item" href="<?php echo e(route('management.projects.show', $project->id)); ?>">
                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View
                                            </a>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-projects')): ?>
                                            <a class="dropdown-item" href="<?php echo e(route('management.projects.edit', $project->id)); ?>">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                            </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-projects')): ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); 
                                                if(confirm('Are you sure?')) document.getElementById('delete-form-<?php echo e($project->id); ?>').submit();">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Remove
                                            </a>
                                            <form id="delete-form-<?php echo e($project->id); ?>" 
                                                action="<?php echo e(route('management.projects.destroy', $project->id)); ?>" 
                                                method="POST" style="display: none;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <?php if($project->thumbnail): ?>
                                        <img src="<?php echo e(asset('storage/' . $project->thumbnail)); ?>" alt="" class="img-fluid rounded">
                                    <?php else: ?>
                                        <span class="avatar-title bg-primary-subtle text-primary rounded p-2">
                                            <?php echo e(strtoupper(substr($project->title, 0, 2))); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1 fs-15">
                                    <a href="<?php echo e(route('management.projects.show', $project->id)); ?>" class="text-body">
                                        <?php echo e($project->title); ?>

                                    </a>
                                </h5>
                                <p class="text-muted text-truncate-two-lines mb-3">
                                    <?php echo e(Str::limit(strip_tags($project->description), 100)); ?>

                                </p>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex mb-2">
                                <div class="flex-grow-1">
                                    <div>Progress</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div><?php echo e($project->progress); ?>%</div>
                                </div>
                            </div>
                            <div class="progress progress-sm animated-progress">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                    aria-valuenow="<?php echo e($project->progress); ?>" aria-valuemin="0"
                                    aria-valuemax="100" style="width: <?php echo e($project->progress); ?>%;">
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
                                <?php $__currentLoopData = $project->members->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($member->name); ?>">
                                    <div class="avatar-xxs">
                                        <?php if($member->avatar): ?>
                                            <img src="<?php echo e(asset('storage/' . $member->avatar)); ?>" alt="" class="rounded-circle img-fluid">
                                        <?php else: ?>
                                            <div class="avatar-title rounded-circle bg-primary">
                                                <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($project->members->count() > 3): ?>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($project->members->count() - 3); ?> more">
                                    <div class="avatar-xxs">
                                        <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                            +<?php echo e($project->members->count() - 3); ?>

                                        </div>
                                    </div>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="text-muted">
                                <i class="ri-calendar-event-fill me-1 align-bottom"></i> 
                                <?php echo e($project->deadline->format('d M, Y')); ?>

                            </div>
                        </div>

                    </div>

                </div>
                <!-- end card footer -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                        colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                    </lord-icon>
                    <h5 class="mt-4">No Projects Found</h5>
                    <p class="text-muted">You haven't created any projects yet.</p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-projects')): ?>
                    <a href="<?php echo e(route('management.projects.create')); ?>" class="btn btn-primary mt-2">
                        <i class="ri-add-line align-bottom me-1"></i> Create Project
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <!-- end row -->

    <!-- Pagination -->
    <div class="row g-0 text-center text-sm-start align-items-center mb-4">
        <div class="col-sm-6">
            <div>
                <p class="mb-sm-0 text-muted">Showing <span class="fw-semibold"><?php echo e($projects->firstItem() ?? 0); ?></span> to 
                    <span class="fw-semibold"><?php echo e($projects->lastItem() ?? 0); ?></span> of 
                    <span class="fw-semibold text-decoration-underline"><?php echo e($projects->total()); ?></span> projects
                </p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="pagination-wrap hstack gap-2 justify-content-center justify-content-sm-end">
                <?php echo e($projects->links()); ?>

            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/list.js/list.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/list.pagination.js/list.pagination.min.js')); ?>"></script>
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
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/apps-projects-list.blade.php ENDPATH**/ ?>