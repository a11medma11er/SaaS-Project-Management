
<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('translation.task-details'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title mb-3 flex-grow-1 text-start">Time Tracking</h6>
                <div class="mb-2">
                    <lord-icon src="https://cdn.lordicon.com/kbtmbyzy.json" trigger="loop"
                        colors="primary:#8c68cd,secondary:#4788ff" style="width:90px;height:90px">
                    </lord-icon>
                </div>
                <h3 class="mb-1"><?php echo e($task->timeEntries->sum('duration_minutes')); ?> min</h3>
                <h5 class="fs-14 mb-4"><?php echo e($task->title); ?></h5>
                <div class="hstack gap-2 justify-content-center">
                    <button class="btn btn-danger btn-sm"><i class="ri-stop-circle-line align-bottom me-1"></i> Stop</button>
                    <button class="btn btn-primary btn-sm"><i class="ri-play-circle-line align-bottom me-1"></i> Start</button>
                </div>
            </div>
        </div><!--end card-->
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-4">
                    <select class="form-control" name="choices-single-default" data-choices data-choices-search-false>
                        <option value="">Select Task board</option>
                        <option value="Unassigned">Unassigned</option>
                        <option value="To Do">To Do</option>
                        <option value="Inprogress">Inprogress</option>
                        <option value="In Reviews" selected>In Reviews</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="table-card">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-medium">Tasks No</td>
                                <td><?php echo e($task->task_number); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Tasks Title</td>
                                <td><?php echo e($task->title); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Project Name</td>
                                <td><?php echo e($task->project ? $task->project->title : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Priority</td>
                                <td><span class="badge <?php if($task->priority === 'High'): ?> bg-danger-subtle text-danger <?php elseif($task->priority === 'Medium'): ?> bg-warning-subtle text-warning <?php else: ?> bg-success-subtle text-success <?php endif; ?>"><?php echo e($task->priority); ?></span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Status</td>
                                <td><span class="badge <?php if($task->status === 'New'): ?> bg-info-subtle text-info <?php elseif($task->status === 'Pending'): ?> bg-warning-subtle text-warning <?php elseif($task->status === 'Inprogress'): ?> bg-secondary-subtle text-secondary <?php else: ?> bg-success-subtle text-success <?php endif; ?>"><?php echo e($task->status); ?></span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Due Date</td>
                                <td><?php echo e($task->due_date->format('d M, Y')); ?></td>
                            </tr>
                        </tbody>
                    </table><!--end table-->
                </div>
            </div>
        </div><!--end card-->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <h6 class="card-title mb-0 flex-grow-1">Assigned To</h6>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteMembersModal"><i class="ri-share-line me-1 align-bottom"></i> Assigned Member</button>
                    </div>
                </div>
                <ul class="list-unstyled vstack gap-3 mb-0">
                    <?php $__empty_1 = true; $__currentLoopData = $task->assignedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <?php if($user->avatar): ?>
                                    <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="" class="avatar-xs rounded-circle">
                                <?php else: ?>
                                    <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle avatar-xs">
                                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-1"><a href="javascript:void(0);"><?php echo e($user->name); ?></a></h6>
                                <p class="text-muted mb-0"><?php echo e($user->email); ?></p>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li>
                        <div class="text-muted text-center">No members assigned</div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div><!--end card-->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Attachments</h5>
                <div class="vstack gap-2">
                    <?php $__empty_1 = true; $__currentLoopData = $task->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                            $icon = match($ext) {
                                'zip', 'rar', '7z' => 'ri-folder-zip-line',
                                'ppt', 'pptx' => 'ri-file-ppt-2-line',
                                'doc', 'docx' => 'ri-file-word-line',
                                'xls', 'xlsx', 'csv' => 'ri-file-excel-line',
                                'pdf' => 'ri-file-pdf-line',
                                'jpg', 'jpeg', 'png', 'gif', 'svg' => 'ri-image-2-line',
                                'txt' => 'ri-file-text-line',
                                default => 'ri-file-line'
                            };
                            $size = $attachment->file_size;
                            $formattedSize = $size > 1048576 ? round($size / 1048576, 2) . ' MB' : round($size / 1024, 2) . ' KB';
                        ?>
                    <div class="border rounded border-dashed p-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-light text-primary rounded fs-24">
                                        <i class="<?php echo e($icon); ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-13 mb-1"><a href="<?php echo e(asset('storage/' . $attachment->file_path)); ?>" target="_blank" class="text-body text-truncate d-block"><?php echo e($attachment->file_name); ?></a></h5>
                                <div><?php echo e($formattedSize); ?></div>
                            </div>
                            <div class="flex-shrink-0 ms-2">
                                <div class="d-flex gap-1">
                                    <a href="<?php echo e(asset('storage/' . $attachment->file_path)); ?>" download class="btn btn-icon text-muted btn-sm fs-18"><i class="ri-download-2-line"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-muted text-center py-2">No attachments</div>
                    <?php endif; ?>
                </div>
            </div>
        </div><!--end card-->
    </div><!---end col-->
    <div class="col-xxl-9">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">
                    <h6 class="mb-3 fw-semibold text-uppercase">Sub-tasks</h6>
                    <ul class=" ps-3 list-unstyled vstack gap-2">
                        <?php $__empty_1 = true; $__currentLoopData = $task->subTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subTask): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input subtask-checkbox" type="checkbox" value="<?php echo e($subTask->id); ?>" id="subTask<?php echo e($subTask->id); ?>" 
                                    data-url="<?php echo e(route('management.tasks.sub-tasks.toggle', [$task, $subTask])); ?>"
                                    <?php echo e($subTask->is_completed ? 'checked' : ''); ?>>
                                <label class="form-check-label <?php echo e($subTask->is_completed ? 'text-decoration-line-through text-muted' : ''); ?>" for="subTask<?php echo e($subTask->id); ?>">
                                    <?php echo e($subTask->title); ?>

                                </label>
                            </div>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li><div class="text-muted">No sub-tasks</div></li>
                        <?php endif; ?>
                    </ul>

                    <div class="pt-3 border-top border-top-dashed mt-4">
                        <h6 class="mb-3 fw-semibold text-uppercase">Tasks Tags</h6>
                        <div class="hstack flex-wrap gap-2 fs-15">
                             <?php $__empty_1 = true; $__currentLoopData = $task->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="badge fw-medium bg-primary-subtle text-primary"><?php echo e($tag->tag); ?></div>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-muted fs-12">No tags</div>
                             <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end card-->
        <div class="card">
            <div class="card-header">
                <div>
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home-1" role="tab">
                                Comments (<?php echo e($task->comments->count()); ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#messages-1" role="tab">
                                Attachments File (<?php echo e($task->attachments->count()); ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#profile-1" role="tab">
                                <?php
                                   $totalMin = $task->timeEntries->sum('duration_minutes');
                                   $hrs = floor($totalMin / 60);
                                   $min = $totalMin % 60;
                                ?>
                                Time Entries (<?php echo e($hrs); ?> hrs <?php echo e($min); ?> min)
                            </a>
                        </li>
                    </ul><!--end nav-->
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="home-1" role="tabpanel">
                        <h5 class="card-title mb-4">Comments</h5>
                        <div data-simplebar style="height: 508px;" class="px-3 mx-n3 mb-2">
                             <?php $__empty_1 = true; $__currentLoopData = $task->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                         <?php if($comment->user->avatar): ?>
                                            <img src="<?php echo e(asset('storage/' . $comment->user->avatar)); ?>" alt="" class="avatar-xs rounded-circle" />
                                         <?php else: ?>
                                            <div class="avatar-xs rounded-circle bg-secondary-subtle">
                                                <span class="d-flex align-items-center justify-content-center w-100 h-100 text-secondary fs-10">
                                                    <?php echo e(strtoupper(substr($comment->user->name, 0, 1))); ?>

                                                </span>
                                            </div>
                                         <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fs-13"><a href="javascript:void(0)" class="text-body"><?php echo e($comment->user->name); ?></a> <small class="text-muted"><?php echo e($comment->created_at->format('d M Y - h:i A')); ?></small></h5>
                                        <p class="text-muted"><?php echo e($comment->comment); ?></p>
                                        <a href="javascript: void(0);" class="badge text-muted bg-light" onclick="document.getElementById('reply-form-<?php echo e($comment->id); ?>').classList.toggle('d-none')"><i class="mdi mdi-reply"></i> Reply</a>
                                        
                                        <!-- Replies -->
                                        <?php $__currentLoopData = $comment->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="d-flex mt-4">
                                                <div class="flex-shrink-0">
                                                    <?php if($reply->user->avatar): ?>
                                                        <img src="<?php echo e(asset('storage/' . $reply->user->avatar)); ?>" alt="" class="avatar-xs rounded-circle" />
                                                    <?php else: ?>
                                                        <div class="avatar-xs rounded-circle bg-secondary-subtle">
                                                            <span class="d-flex align-items-center justify-content-center w-100 h-100 text-secondary fs-10">
                                                                <?php echo e(strtoupper(substr($reply->user->name, 0, 1))); ?>

                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="fs-13"><a href="javascript:void(0)" class="text-body"><?php echo e($reply->user->name); ?></a> <small class="text-muted"><?php echo e($reply->created_at->diffForHumans()); ?></small></h5>
                                                    <p class="text-muted"><?php echo e($reply->comment); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        <!-- Reply Form -->
                                        <div class="mt-3 d-none" id="reply-form-<?php echo e($comment->id); ?>">
                                            <form action="<?php echo e(route('management.tasks.comments.store', $task)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="parent_id" value="<?php echo e($comment->id); ?>">
                                                <div class="input-group">
                                                    <input type="text" name="comment" class="form-control" placeholder="Reply..." required>
                                                    <button class="btn btn-primary" type="submit">Send</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-4">
                                    <p class="text-muted">No comments yet.</p>
                                </div>
                             <?php endif; ?>
                        </div>
                        <form action="<?php echo e(route('management.tasks.comments.store', $task)); ?>" method="POST" class="mt-4">
                            <?php echo csrf_field(); ?>
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label for="comment-textarea" class="form-label">Leave a Comment</label>
                                    <textarea class="form-control bg-light border-light" name="comment" id="comment-textarea" rows="3" placeholder="Enter your comment..." required></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary"><i class="ri-send-plane-2-fill align-bottom me-1"></i> Post Comment</button>
                                </div>
                            </div>
                        </form>
                    </div><!--end tab-pane-->
                    <div class="tab-pane" id="messages-1" role="tabpanel">
                        <div class="table-responsive table-card">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">File Name</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Upload Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $task->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                                            $icon = match($ext) {
                                                'zip', 'rar', '7z' => 'ri-file-zip-fill',
                                                'ppt', 'pptx' => 'ri-file-ppt-fill',
                                                'doc', 'docx' => 'ri-file-word-fill',
                                                'xls', 'xlsx', 'csv' => 'ri-file-excel-fill',
                                                'pdf' => 'ri-file-pdf-fill',
                                                'jpg', 'jpeg', 'png', 'gif', 'svg' => 'ri-image-2-fill',
                                                'txt' => 'ri-file-text-fill',
                                                default => 'ri-file-fill'
                                            };
                                            $color = match($ext) {
                                                'zip', 'rar', '7z' => 'primary',
                                                'ppt', 'pptx' => 'danger',
                                                'doc', 'docx' => 'info',
                                                'xls', 'xlsx', 'csv' => 'success',
                                                'pdf' => 'danger',
                                                default => 'secondary'
                                            };
                                            $size = $attachment->file_size;
                                            $formattedSize = $size > 1048576 ? round($size / 1048576, 2) . ' MB' : round($size / 1024, 2) . ' KB';
                                        ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-<?php echo e($color); ?>-subtle text-<?php echo e($color); ?> rounded fs-20">
                                                        <i class="<?php echo e($icon); ?>"></i>
                                                    </div>
                                                </div>
                                                <div class="ms-3 flex-grow-1">
                                                    <h6 class="fs-15 mb-0"><a href="<?php echo e(asset('storage/' . $attachment->file_path)); ?>" target="_blank"><?php echo e($attachment->file_name); ?></a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e(strtoupper($ext)); ?> File</td>
                                        <td><?php echo e($formattedSize); ?></td>
                                        <td><?php echo e($attachment->created_at->format('d M, Y')); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="btn btn-light btn-icon" id="dropdownMenuLink<?php echo e($attachment->id); ?>" data-bs-toggle="dropdown" aria-expanded="true">
                                                    <i class="ri-equalizer-fill"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink<?php echo e($attachment->id); ?>">
                                                    <li><a class="dropdown-item" href="<?php echo e(asset('storage/' . $attachment->file_path)); ?>" target="_blank"><i class="ri-eye-fill me-2 align-middle text-muted"></i>View</a></li>
                                                    <li><a class="dropdown-item" href="<?php echo e(asset('storage/' . $attachment->file_path)); ?>" download><i class="ri-download-2-fill me-2 align-middle text-muted"></i>Download</a></li>
                                                    <li class="dropdown-divider"></li>
                                                    <!-- Delete not implemented yet properly -->
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-5-line me-2 align-middle text-muted"></i>Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No attachments found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table><!--end table-->
                        </div>
                    </div><!--end tab-pane-->
                    <div class="tab-pane" id="profile-1" role="tabpanel">
                        <h6 class="card-title mb-4 pb-2">Time Entries</h6>
                        <div class="table-responsive table-card">
                            <table class="table align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Member</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Duration</th>
                                        <th scope="col">Timer Idle</th>
                                        <th scope="col">Tasks Title</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-8.jpg')); ?>" alt="" class="rounded-circle avatar-xxs">
                                                <div class="flex-grow-1 ms-2">
                                                    <a href="pages-profile" class="fw-medium">Thomas Taylor</a>
                                                </div>
                                            </div>
                                        </th>
                                        <td>02 Jan, 2022</td>
                                        <td>3 hrs 12 min</td>
                                        <td>05 min</td>
                                        <td>Apps Pages</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-10.jpg')); ?>" alt="" class="rounded-circle avatar-xxs">
                                                <div class="flex-grow-1 ms-2">
                                                    <a href="pages-profile" class="fw-medium">Tonya Noble</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>28 Dec, 2021</td>
                                        <td>1 hrs 35 min</td>
                                        <td>-</td>
                                        <td>Profile Page Design</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-10.jpg')); ?>" alt="" class="rounded-circle avatar-xxs">
                                                <div class="flex-grow-1 ms-2">
                                                    <a href="pages-profile" class="fw-medium">Tonya Noble</a>
                                                </div>
                                            </div>
                                        </th>
                                        <td>27 Dec, 2021</td>
                                        <td>4 hrs 26 min</td>
                                        <td>03 min</td>
                                        <td>Ecommerce Dashboard</td>
                                    </tr>
                                </tbody>
                            </table><!--end table-->
                        </div>
                    </div><!--edn tab-pane-->

                </div><!--end tab-content-->
            </div>
        </div><!--end card-->
    </div><!--end col-->
</div><!--end row-->

<div class="modal fade" id="inviteMembersModal" tabindex="-1" aria-labelledby="inviteMembersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <form action="<?php echo e(route('management.tasks.update', $task)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header p-3 ps-4 bg-primary-subtle">
                    <h5 class="modal-title" id="inviteMembersModalLabel">Team Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="search-box mb-3">
                        <input type="text" class="form-control bg-light border-light" placeholder="Search here...">
                        <i class="ri-search-line search-icon"></i>
                    </div>

                    <div class="mx-n4 px-4" data-simplebar style="max-height: 225px;">
                        <div class="vstack gap-3">
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <?php if($user->avatar): ?>
                                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="" class="img-fluid rounded-circle">
                                    <?php else: ?>
                                        <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle">
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="javascript:void(0);" class="text-body d-block"><?php echo e($user->name); ?></a></h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="assigned_users[]" value="<?php echo e($user->id); ?>" 
                                            id="user_<?php echo e($user->id); ?>" <?php echo e($task->assignedUsers->contains($user->id) ? 'checked' : ''); ?>>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light w-xs" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary w-xs">Update Assignments</button>
                </div>
            </form>
        </div>
        <!-- end modal-content -->
    </div>
    <!-- modal-dialog -->
</div>
<!-- end modal -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.subtask-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const url = this.dataset.url;
                const isChecked = this.checked;
                const label = this.nextElementSibling;

                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
                    },
                    body: JSON.stringify({ is_completed: isChecked })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.is_completed) {
                            label.classList.add('text-decoration-line-through', 'text-muted');
                        } else {
                            label.classList.remove('text-decoration-line-through', 'text-muted');
                        }
                    } else {
                        alert('Failed to update subtask');
                        this.checked = !isChecked; // Revert
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating subtask');
                    this.checked = !isChecked; // Revert
                });
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/apps-tasks-details.blade.php ENDPATH**/ ?>