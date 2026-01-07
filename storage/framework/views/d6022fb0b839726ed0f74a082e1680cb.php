
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.list-view'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Tasks
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Tasks view
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Total Tasks</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?php echo e($stats['total']); ?>">0</span>
                            </h2>
                            <p class="mb-0 text-muted">All tasks in system</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                                    <i class="ri-ticket-2-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div> <!-- end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Pending Tasks</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="<?php echo e($stats['pending']); ?>">0</span></h2>
                            <p class="mb-0 text-muted">Tasks pending completion</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                                    <i class="mdi mdi-timer-sand"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Completed Tasks</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="<?php echo e($stats['completed']); ?>">0</span></h2>
                            <p class="mb-0 text-muted">Successfully completed</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                                    <i class="ri-checkbox-circle-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Deleted Tasks</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="<?php echo e($stats['deleted']); ?>">0</span></h2>
                            <p class="mb-0 text-muted">Soft deleted tasks</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                                    <i class="ri-delete-bin-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">All Tasks</h5>
                        <div class="flex-shrink-0">
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary add-btn" data-bs-toggle="modal"
                                    data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Create
                                    Task</button>
                                <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form>
                        <div class="row g-3">
                            <div class="col-xxl-5 col-sm-12">
                                <div class="search-box">
                                    <input type="text" class="form-control search bg-light border-light"
                                        placeholder="Search for tasks or something...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-xxl-3 col-sm-4">
                                <input type="text" class="form-control bg-light border-light" id="demo-datepicker"
                                    data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true"
                                    placeholder="Select date range">
                            </div>
                            <!--end col-->

                            <div class="col-xxl-3 col-sm-4">
                                <div class="input-light">
                                    <select class="form-control" data-choices data-choices-search-false
                                        name="choices-single-default" id="idStatus">
                                        <option value="">Status</option>
                                        <option value="all" selected>All</option>
                                        <option value="New">New</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Inprogress">Inprogress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-1 col-sm-4">
                                <button type="button" class="btn btn-primary w-100" onclick="SearchData();"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Filters
                                </button>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
                <!--end card-body-->
                <div class="card-body">
                    <div class="table-responsive table-card mb-4">
                        <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col" style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll"
                                                value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="id">ID</th>
                                    <th class="sort" data-sort="project_name">Project</th>
                                    <th class="sort" data-sort="tasks_name">Task</th>
                                    <th class="sort" data-sort="client_name">Client Name</th>
                                    <th class="sort" data-sort="assignedto">Assigned To</th>
                                    <th class="sort" data-sort="due_date">Due Date</th>
                                    <th class="sort" data-sort="status">Status</th>
                                    <th class="sort" data-sort="priority">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chk_child" value="<?php echo e($task->id); ?>">
                                        </div>
                                    </th>
                                    <td class="id"><a href="<?php echo e(route('management.tasks.show', $task)); ?>" class="fw-medium link-primary"><?php echo e($task->task_number); ?></a></td>
                                    <td class="project_name">
                                        <?php if($task->project): ?>
                                        <a href="<?php echo e(route('management.projects.show', $task->project)); ?>" class="fw-medium link-primary"><?php echo e($task->project->title); ?></a>
                                        <?php else: ?>
                                        <span class="text-muted">No Project</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <div class="flex-grow-1 tasks_name"><?php echo e($task->title); ?></div>
                                            <div class="flex-shrink-0 ms-4">
                                                <ul class="list-inline tasks-list-menu mb-0">
                                                    <li class="list-inline-item"><a href="<?php echo e(route('management.tasks.show', $task)); ?>"><i class="ri-eye-fill align-bottom me-2 text-muted"></i></a></li>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-tasks')): ?>
                                                    <li class="list-inline-item"><a href="<?php echo e(route('management.tasks.edit', $task)); ?>"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-tasks')): ?>
                                                    <li class="list-inline-item"><a class="remove-item-btn" data-bs-toggle="modal" href="#deleteOrder" data-task-id="<?php echo e($task->id); ?>"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i></a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="client_name"><?php echo e($task->client_name ?? 'N/A'); ?></td>
                                    <td class="assignedto">
                                        <?php if($task->assignedUsers->count() > 0): ?>
                                        <div class="avatar-group">
                                            <?php $__currentLoopData = $task->assignedUsers->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($user->name); ?>">
                                                <?php if($user->avatar): ?>
                                                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>" class="rounded-circle avatar-xxs" />
                                                <?php else: ?>
                                                <div class="avatar-xxs">
                                                    <div class="avatar-title rounded-circle bg-primary"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
                                                </div>
                                                <?php endif; ?>
                                            </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($task->assignedUsers->count() > 3): ?>
                                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="<?php echo e($task->assignedUsers->count() - 3); ?> more">
                                                <div class="avatar-xxs">
                                                    <div class="avatar-title rounded-circle bg-secondary">+<?php echo e($task->assignedUsers->count() - 3); ?></div>
                                                </div>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                        <?php else: ?>
                                        <span class="text-muted">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="due_date"><?php echo e($task->due_date->format('d M, Y')); ?></td>
                                    <td class="status">
                                        <span class="badge <?php if($task->status === 'New'): ?> bg-info-subtle text-info <?php elseif($task->status === 'Pending'): ?> bg-warning-subtle text-warning <?php elseif($task->status === 'Inprogress'): ?> bg-secondary-subtle text-secondary <?php else: ?> bg-success-subtle text-success <?php endif; ?> text-uppercase"><?php echo e($task->status); ?></span>
                                    </td>
                                    <td class="priority">
                                        <span class="badge <?php if($task->priority === 'High'): ?> bg-danger <?php elseif($task->priority === 'Medium'): ?> bg-warning <?php else: ?> bg-success <?php endif; ?> text-uppercase"><?php echo e($task->priority); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="text-muted py-4">
                                            <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#8c68cd,secondary:#4788ff" style="width:75px;height:75px"></lord-icon>
                                            <h5 class="mt-2">No Tasks Found</h5>
                                            <p class="text-muted mb-0">Create your first task to get started!</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <!--end table-->
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#8c68cd,secondary:#4788ff" style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted mb-0">We've searched more than 200k+ tasks We did not find any tasks
                                    for you search.</p>
                            </div>
                        </div>
                    </div>
                    <?php if($tasks->hasPages()): ?>
                    <div class="d-flex justify-content-end mt-2">
                        <?php echo e($tasks->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->


    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-5 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                        colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                    <div class="mt-4 text-center">
                        <h4>You are about to delete a task?</h4>
                        <p class="text-muted fs-14 mb-4">Deleting your task will remove all of
                            your information from our database.</p>
                        <form id="deleteTaskForm" method="POST" action="">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <div class="hstack gap-2 justify-content-center">
                                <button type="button" class="btn btn-link btn-ghost-success fw-medium text-decoration-none"
                                    data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</button>
                                <button type="submit" class="btn btn-danger" id="delete-task">
                                    <i class="ri-delete-bin-fill me-1 align-middle"></i> Yes, Delete It!
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete button clicks
        const deleteButtons = document.querySelectorAll('.remove-item-btn');
        const deleteForm = document.getElementById('deleteTaskForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-task-id');
                const action = "<?php echo e(route('management.tasks.destroy', ':id')); ?>".replace(':id', taskId);
                deleteForm.setAttribute('action', action);
            });
        });
    });
    </script>
    <!--end delete modal -->

    <div class="modal fade zoomIn" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-primary-subtle">
                    <h5 class="modal-title" id="exampleModalLabel">Create Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form class="tablelist-form" autocomplete="off">
                    <div class="modal-body">
                        <input type="hidden" id="tasksId" />
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="projectName-field" class="form-label">Project Name</label>
                                <input type="text" id="projectName-field" class="form-control"
                                    placeholder="Project name" required />
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div>
                                    <label for="tasksTitle-field" class="form-label">Title</label>
                                    <input type="text" id="tasksTitle-field" class="form-control" placeholder="Title"
                                        required />
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="clientName-field" class="form-label">Client Name</label>
                                <input type="text" id="clientName-field" class="form-control"
                                    placeholder="Client name" required />
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label class="form-label">Assigned To</label>
                                <div data-simplebar style="height: 95px;">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-2.jpg" id="james-forbes">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="james-forbes">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-2.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">James Forbes</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-3.jpg" id="john-robles">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="john-robles">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">John Robles</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-4.jpg" id="mary-gant">
                                                <label class="form-check-label d-flex align-items-center" for="mary-gant">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-4.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Mary Gant</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-1.jpg" id="curtis-saenz">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="curtis-saenz">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-1.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Curtis Saenz</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-5.jpg" id="virgie-price">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="virgie-price">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-5.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Virgie Price</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-10.jpg" id="anthony-mills">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="anthony-mills">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-10.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Anthony Mills</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-6.jpg" id="marian-angel">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="marian-angel">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-6.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Marian Angel</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-10.jpg" id="johnnie-walton">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="johnnie-walton">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-7.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Johnnie Walton</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-8.jpg" id="donna-weston">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="donna-weston">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-8.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Donna Weston</span>
                                                </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" name="assignedTo[]"
                                                    value="avatar-9.jpg" id="diego-norris">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="diego-norris">
                                                    <span class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-9.jpg')); ?>"
                                                            alt="" class="avatar-xxs rounded-circle">
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">Diego Norris</span>
                                                </label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <label for="duedate-field" class="form-label">Due Date</label>
                                <input type="text" id="duedate-field" class="form-control" data-provider="flatpickr"
                                    placeholder="Due date" required />
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <label for="ticket-status" class="form-label">Status</label>
                                <select class="form-control" id="ticket-status">
                                    <option value="">Status</option>
                                    <option value="New">New</option>
                                    <option value="Inprogress">Inprogress</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="priority-field" class="form-label">Priority</label>
                                <select class="form-control" id="priority-field">
                                    <option value="">Priority</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" id="close-modal"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" id="add-btn">Add Task</button>
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end modal-->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/list.js/list.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/list.pagination.js/list.pagination.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/tasks-list.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/apps-tasks-list-view.blade.php ENDPATH**/ ?>