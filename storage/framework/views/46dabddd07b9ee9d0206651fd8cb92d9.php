
<?php $__env->startSection('title'); ?>
    <?php echo e($project->title); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4 card-border-effect-none">
                <div class="bg-primary-subtle">
                    <div class="card-body pb-0 px-4">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-auto">
                                        <div class="avatar-md">
                                            <div class="avatar-title bg-white rounded-circle">
                                                <?php if($project->thumbnail): ?>
                                                    <img src="<?php echo e(asset('storage/' . $project->thumbnail)); ?>" alt="" class="avatar-xs rounded-circle">
                                                <?php else: ?>
                                                    <span class="avatar-xs"><?php echo e(strtoupper(substr($project->title, 0, 2))); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold"><?php echo e($project->title); ?></h4>
                                            <div class="hstack gap-3 flex-wrap">
                                                <?php if($project->category): ?>
                                                <div><i class="ri-building-line align-bottom me-1"></i> <?php echo e($project->category); ?></div>
                                                <div class="vr"></div>
                                                <?php endif; ?>
                                                <div>Created: <span class="fw-medium"><?php echo e($project->created_at->format('d M, Y')); ?></span></div>
                                                <div class="vr"></div>
                                                <div>Deadline: <span class="fw-medium"><?php echo e($project->deadline->format('d M, Y')); ?></span></div>
                                                <div class="vr"></div>
                                                <div class="badge rounded-pill bg-<?php echo e($project->status == 'Completed' ? 'success' : ($project->status == 'On Hold' ? 'warning' : 'info')); ?> fs-12"><?php echo e($project->status); ?></div>
                                                <div class="badge rounded-pill bg-<?php echo e($project->priority == 'High' ? 'danger' : ($project->priority == 'Low' ? 'success' : 'warning')); ?> fs-12"><?php echo e($project->priority); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="hstack gap-1 flex-wrap">
                                    <button type="button" class="btn py-0 fs-16 favourite-btn <?php echo e($project->is_favorite ? 'active' : ''); ?>" data-project-id="<?php echo e($project->id); ?>">
                                        <i class="ri-star-fill"></i>
                                    </button>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-projects')): ?>
                                    <a href="<?php echo e(route('management.projects.edit', $project->id)); ?>" class="btn py-0 fs-16 text-body">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-projects')): ?>
                                    <button type="button" class="btn py-0 fs-16 text-body" onclick="event.preventDefault(); 
                                        if(confirm('Are you sure?')) document.getElementById('delete-form-<?php echo e($project->id); ?>').submit();">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
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

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview"
                                    role="tab">
                                    Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-documents" role="tab">
                                    Documents
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-activities" role="tab">
                                    Activities
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-team" role="tab">
                                    Team
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content text-muted">
                <div class="tab-pane fade show active" id="project-overview" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-muted">
                                        <h6 class="mb-3 fw-semibold text-uppercase">Summary</h6>
                                        <div><?php echo $project->description; ?></div>

                                        <?php if($project->skills && count($project->skills) > 0): ?>
                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <h6 class="mb-3 fw-semibold text-uppercase">Skills</h6>
                                            <div class="d-flex flex-wrap gap-2 fs-15">
                                                <?php $__currentLoopData = $project->skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-primary-subtle text-primary"><?php echo e($skill); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>


                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <div class="row gy-3">

                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Create Date :</p>
                                                        <h5 class="fs-15 mb-0"><?php echo e($project->created_at->format('d M, Y')); ?></h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Deadline :</p>
                                                        <h5 class="fs-15 mb-0"><?php echo e($project->deadline->format('d M, Y')); ?></h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Priority :</p>
                                                        <div class="badge bg-<?php echo e($project->priority == 'High' ? 'danger' : ($project->priority == 'Low' ? 'success' : 'warning')); ?> fs-12"><?php echo e($project->priority); ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Status :</p>
                                                        <div class="badge bg-<?php echo e($project->status == 'Completed' ? 'success' : ($project->status == 'On Hold' ? 'warning' : 'info')); ?> fs-12"><?php echo e($project->status); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <h6 class="mb-3 fw-semibold text-uppercase">Resources</h6>
                                            <div class="row g-3">
                                                <div class="col-xxl-4 col-lg-6">
                                                    <div class="border rounded border-dashed p-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-light text-primary rounded fs-24">
                                                                        <i class="ri-folder-zip-line"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 overflow-hidden">
                                                                <h5 class="fs-13 mb-1"><a href="#"
                                                                        class="text-body text-truncate d-block">App
                                                                        pages.zip</a></h5>
                                                                <div>2.2MB</div>
                                                            </div>
                                                            <div class="flex-shrink-0 ms-2">
                                                                <div class="d-flex gap-1">
                                                                    <button type="button"
                                                                        class="btn btn-icon text-muted btn-sm fs-18"><i
                                                                            class="ri-download-2-line"></i></button>
                                                                    <div class="dropdown">
                                                                        <button
                                                                            class="btn btn-icon text-muted btn-sm fs-18 dropdown"
                                                                            type="button" data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ri-more-fill"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="#"><i
                                                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                                                    Rename</a></li>
                                                                            <li><a class="dropdown-item" href="#"><i
                                                                                        class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                                    Delete</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end col -->
                                                <div class="col-xxl-4 col-lg-6">
                                                    <div class="border rounded border-dashed p-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar-sm">
                                                                    <div
                                                                        class="avatar-title bg-light text-primary rounded fs-24">
                                                                        <i class="ri-file-ppt-2-line"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 overflow-hidden">
                                                                <h5 class="fs-13 mb-1"><a href="#"
                                                                        class="text-body text-truncate d-block">Velzon
                                                                        admin.ppt</a></h5>
                                                                <div>2.4MB</div>
                                                            </div>
                                                            <div class="flex-shrink-0 ms-2">
                                                                <div class="d-flex gap-1">
                                                                    <button type="button"
                                                                        class="btn btn-icon text-muted btn-sm fs-18"><i
                                                                            class="ri-download-2-line"></i></button>
                                                                    <div class="dropdown">
                                                                        <button
                                                                            class="btn btn-icon text-muted btn-sm fs-18 dropdown"
                                                                            type="button" data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                            <i class="ri-more-fill"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="dropdown-item" href="#"><i
                                                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                                                    Rename</a></li>
                                                                            <li><a class="dropdown-item" href="#"><i
                                                                                        class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                                    Delete</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->

                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Comments</h4>
                                    <div class="flex-shrink-0">
                                        <div class="dropdown card-header-dropdown">
                                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted">Recent<i
                                                        class="mdi mdi-chevron-down ms-1"></i></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Recent</a>
                                                <a class="dropdown-item" href="#">Top Rated</a>
                                                <a class="dropdown-item" href="#">Previous</a>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- end card header -->

                                <div class="card-body">

                                    <div data-simplebar style="height: 300px;" class="px-3 mx-n3 mb-2">
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-8.jpg')); ?>" alt=""
                                                    class="avatar-xs rounded-circle" />
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="fs-13">Joseph Parker <small class="text-muted ms-2">20
                                                        Dec 2021 - 05:47AM</small></h5>
                                                <p class="text-muted">I am getting message from customers that when
                                                    they place order always get error message .</p>
                                                <a href="javascript: void(0);" class="badge text-muted bg-light"><i
                                                        class="mdi mdi-reply"></i> Reply</a>
                                                <div class="d-flex mt-4">
                                                    <div class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-10.jpg')); ?>" alt=""
                                                            class="avatar-xs rounded-circle" />
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="fs-13">Alexis Clarke <small
                                                                class="text-muted ms-2">22 Dec 2021 - 02:32PM</small></h5>
                                                        <p class="text-muted">Please be sure to check your Spam mailbox
                                                            to see if your email filters have identified the email from Dell
                                                            as spam.</p>
                                                        <a href="javascript: void(0);" class="badge text-muted bg-light"><i
                                                                class="mdi mdi-reply"></i> Reply</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-6.jpg')); ?>" alt=""
                                                    class="avatar-xs rounded-circle" />
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="fs-13">Donald Palmer <small class="text-muted ms-2">24
                                                        Dec 2021 - 05:20PM</small></h5>
                                                <p class="text-muted">If you have further questions, please contact
                                                    Customer Support from the “Action Menu” on your <a
                                                        href="javascript:void(0);" class="text-decoration-underline">Online
                                                        Order Support</a>.</p>
                                                <a href="javascript: void(0);" class="badge text-muted bg-light"><i
                                                        class="mdi mdi-reply"></i> Reply</a>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-10.jpg')); ?>" alt=""
                                                    class="avatar-xs rounded-circle" />
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="fs-13">Alexis Clarke <small class="text-muted ms-2">26
                                                        min ago</small></h5>
                                                <p class="text-muted">Your <a href="javascript:void(0)"
                                                        class="text-decoration-underline">Online Order Support</a> provides
                                                    you with the most current status of your order. To help manage your
                                                    order refer to the “Action Menu” to initiate return, contact Customer
                                                    Support and more.</p>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-lg-1 col-sm-2 col-6">
                                                        <img src="<?php echo e(URL::asset('build/images/small/img-4.jpg')); ?>" alt=""
                                                            class="img-fluid rounded">
                                                    </div>
                                                    <div class="col-lg-1 col-sm-2 col-6">
                                                        <img src="<?php echo e(URL::asset('build/images/small/img-5.jpg')); ?>" alt=""
                                                            class="img-fluid rounded">
                                                    </div>
                                                </div>
                                                <a href="javascript: void(0);" class="badge text-muted bg-light"><i
                                                        class="mdi mdi-reply"></i> Reply</a>
                                                <div class="d-flex mt-4">
                                                    <div class="flex-shrink-0">
                                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-6.jpg')); ?>" alt=""
                                                            class="avatar-xs rounded-circle" />
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="fs-13">Donald Palmer <small
                                                                class="text-muted ms-2">8 sec ago</small></h5>
                                                        <p class="text-muted">Other shipping methods are available at
                                                            checkout if you want your purchase delivered faster.</p>
                                                        <a href="javascript: void(0);" class="badge text-muted bg-light"><i
                                                                class="mdi mdi-reply"></i> Reply</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <form class="mt-4">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="exampleFormControlTextarea1" class="form-label text-body">Leave
                                                    a Comments</label>
                                                <textarea class="form-control bg-light border-light" id="exampleFormControlTextarea1" rows="3"
                                                    placeholder="Enter your comment..."></textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="button"
                                                    class="btn btn-ghost-primary btn-icon waves-effect me-1"><i
                                                        class="ri-attachment-line fs-16"></i></button>
                                                <a href="javascript:void(0);" class="btn btn-primary">Post Comments</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!--end col-->
                        <div class="col-xl-3 col-lg-4">
                            <div class="card">
                                <div class="card-header align-items-center d-flex border-bottom-dashed">
                                    <h4 class="card-title mb-0 flex-grow-1">Members</h4>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-projects')): ?>
                                    <div class="flex-shrink-0">
                                        <a href="<?php echo e(route('management.projects.edit', $project->id)); ?>" class="btn btn-soft-primary btn-sm">
                                            Edit
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body">
                                    <div data-simplebar style="max-height: 235px;" class="mx-n3 px-3">
                                        <div class="vstack gap-3">
                                            <?php if($project->teamLead): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs flex-shrink-0 me-3">
                                                    <?php if($project->teamLead->avatar): ?>
                                                        <img src="<?php echo e(asset('storage/' . $project->teamLead->avatar)); ?>" alt="" class="img-fluid rounded-circle">
                                                    <?php else: ?>
                                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                            <?php echo e(strtoupper(substr($project->teamLead->name, 0, 1))); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-13 mb-0"><?php echo e($project->teamLead->name); ?></h5>
                                                    <small class="text-muted">Team Lead</small>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php $__currentLoopData = $project->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs flex-shrink-0 me-3">
                                                    <?php if($member->avatar): ?>
                                                        <img src="<?php echo e(asset('storage/' . $member->avatar)); ?>" alt="" class="img-fluid rounded-circle">
                                                    <?php else: ?>
                                                        <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                                            <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-13 mb-0"><?php echo e($member->name); ?></h5>
                                                    <small class="text-muted"><?php echo e($member->pivot->role ?? 'Member'); ?></small>
                                                </div>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end card-->

                            <div class="card">
                                <div class="card-header align-items-center d-flex border-bottom-dashed">
                                    <h4 class="card-title mb-0 flex-grow-1">Project Details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="pb-3 border-bottom border-bottom-dashed mb-3">
                                        <div class="mb-2 d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0">Progress</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="text-muted"><?php echo e($project->progress); ?>%</span>
                                            </div>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                style="width: <?php echo e($project->progress); ?>%" 
                                                aria-valuenow="<?php echo e($project->progress); ?>" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-muted text-uppercase fw-semibold mb-2">Privacy</p>
                                        <span class="badge bg-<?php echo e($project->privacy == 'Public' ? 'success' : ($project->privacy == 'Private' ? 'danger' : 'warning')); ?>-subtle text-<?php echo e($project->privacy == 'Public' ? 'success' : ($project->privacy == 'Private' ? 'danger' : 'warning')); ?>">
                                            <?php echo e($project->privacy); ?>

                                        </span>
                                    </div>
                                    <?php if($project->start_date): ?>
                                    <div class="mb-3">
                                        <p class="text-muted text-uppercase fw-semibold mb-2">Start Date</p>
                                        <h6 class="fs-14 mb-0"><?php echo e($project->start_date->format('d M, Y')); ?></h6>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="text-muted text-uppercase fw-semibold mb-2">Total Members</p>
                                        <h6 class="fs-14 mb-0"><?php echo e($project->members->count() + ($project->teamLead ? 1 : 0)); ?></h6>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header align-items-center d-flex border-bottom-dashed">
                                    <h4 class="card-title mb-0 flex-grow-1">Attachments</h4>
                                </div>

                                <div class="card-body">
                                    <?php if($project->attachments->count() > 0): ?>
                                    <div class="vstack gap-2">
                                        <?php $__currentLoopData = $project->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="border rounded border-dashed p-2">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar-sm">
                                                        <div class="avatar-title bg-light text-primary rounded fs-24">
                                                            <i class="<?php echo e(str_contains($attachment->file_path, '.pdf') ? 'ri-file-pdf-line' :
                                                                (str_contains($attachment->file_path, '.zip') ? 'ri-folder-zip-line' :
                                                                (str_contains($attachment->file_path, '.doc') ? 'ri-file-word-line' :
                                                                'ri-file-line'))); ?>"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="fs-13 mb-1">
                                                        <a href="<?php echo e(asset('storage/' . $attachment->file_path)); ?>" 
                                                           class="text-body text-truncate d-block" 
                                                           download><?php echo e(basename($attachment->file_path)); ?></a>
                                                    </h5>
                                                    <div><?php echo e($attachment->file_size); ?></div>
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <div class="d-flex gap-1">
                                                        <a href="<?php echo e(asset('storage/' . $attachment->file_path)); ?>" 
                                                           class="btn btn-icon text-muted btn-sm fs-18" 
                                                           download>
                                                            <i class="ri-download-2-line"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="ri-file-line fs-1 mb-2"></i>
                                        <p class="mb-0">No attachments yet</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-documents" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="card-title flex-grow-1">Documents</h5>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">File Name</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Size</th>
                                                    <th scope="col">Upload Date</th>
                                                    <th scope="col" style="width: 120px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm">
                                                                <div
                                                                    class="avatar-title bg-light text-primary rounded fs-24">
                                                                    <i class="ri-folder-zip-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3 flex-grow-1">
                                                                <h5 class="fs-14 mb-0"><a href="javascript:void(0)"
                                                                        class="text-body">Artboard-documents.zip</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Zip File</td>
                                                    <td>4.57 MB</td>
                                                    <td>12 Dec 2021</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-soft-secondary btn-sm btn-icon"
                                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-eye-fill me-2 align-bottom text-muted"></i>View</a>
                                                                </li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-download-2-fill me-2 align-bottom text-muted"></i>Download</a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-delete-bin-5-fill me-2 align-bottom text-muted"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm">
                                                                <div
                                                                    class="avatar-title bg-light text-danger rounded fs-24">
                                                                    <i class="ri-file-pdf-fill"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3 flex-grow-1">
                                                                <h5 class="fs-14 mb-0"><a href="javascript:void(0);"
                                                                        class="text-body">Bank Management System</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>PDF File</td>
                                                    <td>8.89 MB</td>
                                                    <td>24 Nov 2021</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-soft-secondary btn-sm btn-icon"
                                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-eye-fill me-2 align-bottom text-muted"></i>View</a>
                                                                </li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-download-2-fill me-2 align-bottom text-muted"></i>Download</a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-delete-bin-5-fill me-2 align-bottom text-muted"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm">
                                                                <div
                                                                    class="avatar-title bg-light text-primary rounded fs-24">
                                                                    <i class="ri-video-line"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3 flex-grow-1">
                                                                <h5 class="fs-14 mb-0"><a href="javascript:void(0);"
                                                                        class="text-body">Tour-video.mp4</a></h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>MP4 File</td>
                                                    <td>14.62 MB</td>
                                                    <td>19 Nov 2021</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-soft-secondary btn-sm btn-icon"
                                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-eye-fill me-2 align-bottom text-muted"></i>View</a>
                                                                </li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-download-2-fill me-2 align-bottom text-muted"></i>Download</a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-delete-bin-5-fill me-2 align-bottom text-muted"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm">
                                                                <div
                                                                    class="avatar-title bg-light text-success rounded fs-24">
                                                                    <i class="ri-file-excel-fill"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3 flex-grow-1">
                                                                <h5 class="fs-14 mb-0"><a href="javascript:void(0);"
                                                                        class="text-body">Account-statement.xsl</a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>XSL File</td>
                                                    <td>2.38 KB</td>
                                                    <td>14 Nov 2021</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-soft-secondary btn-sm btn-icon"
                                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-eye-fill me-2 align-bottom text-muted"></i>View</a>
                                                                </li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-download-2-fill me-2 align-bottom text-muted"></i>Download</a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-delete-bin-5-fill me-2 align-bottom text-muted"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm">
                                                                <div
                                                                    class="avatar-title bg-light text-warning rounded fs-24">
                                                                    <i class="ri-folder-fill"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3 flex-grow-1">
                                                                <h5 class="fs-14 mb-0"><a href="javascript:void(0);"
                                                                        class="text-body">Project Screenshots
                                                                        Collection</a></h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Folder File</td>
                                                    <td>87.24 MB</td>
                                                    <td>08 Nov 2021</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-soft-secondary btn-sm btn-icon"
                                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-eye-fill me-2 align-bottom text-muted"></i>View</a>
                                                                </li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-download-2-fill me-2 align-bottom text-muted"></i>Download</a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-delete-bin-5-fill me-2 align-bottom text-muted"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm">
                                                                <div
                                                                    class="avatar-title bg-light text-danger rounded fs-24">
                                                                    <i class="ri-image-2-fill"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3 flex-grow-1">
                                                                <h5 class="fs-14 mb-0"><a href="javascript:void(0);"
                                                                        class="text-body">Velzon-logo.png</a></h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>PNG File</td>
                                                    <td>879 KB</td>
                                                    <td>02 Nov 2021</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-soft-secondary btn-sm btn-icon"
                                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-eye-fill me-2 align-bottom text-muted"></i>View</a>
                                                                </li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-download-2-fill me-2 align-bottom text-muted"></i>Download</a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-delete-bin-5-fill me-2 align-bottom text-muted"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="javascript:void(0);" class="text-success "><i
                                                class="mdi mdi-loading mdi-spin fs-20 align-middle me-2"></i> Load more
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-activities" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Activities</h5>
                            <div class="acitivity-timeline py-3">
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-1.jpg')); ?>" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Oliver Phillips <span
                                                class="badge bg-primary-subtle text-primary align-middle">New</span></h6>
                                        <p class="text-muted mb-2">We talked about a project on linkedin.</p>
                                        <small class="mb-0 text-muted">Today</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                        <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                            N
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Nancy Martino <span
                                                class="badge bg-secondary-subtle text-primary align-middle">In
                                                Progress</span></h6>
                                        <p class="text-muted mb-2"><i class="ri-file-text-line align-middle ms-2"></i>
                                            Create new project Building product</p>
                                        <div class="avatar-group mb-2">
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title="Christi">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-4.jpg')); ?>" alt=""
                                                    class="rounded-circle avatar-xs" />
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title="Frank Hook">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>" alt=""
                                                    class="rounded-circle avatar-xs" />
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title=" Ruby">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title rounded-circle bg-light text-primary">
                                                        R
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title="more">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title rounded-circle">
                                                        2+
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <small class="mb-0 text-muted">Yesterday</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-2.jpg')); ?>" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Natasha Carey <span
                                                class="badge bg-success-subtle text-success align-middle">Completed</span>
                                        </h6>
                                        <p class="text-muted mb-2">Adding a new event with attachments</p>
                                        <div class="row">
                                            <div class="col-xxl-4">
                                                <div class="row border border-dashed gx-2 p-2 mb-2">
                                                    <div class="col-4">
                                                        <img src="<?php echo e(URL::asset('build/images/small/img-2.jpg')); ?>" alt=""
                                                            class="img-fluid rounded" />
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-4">
                                                        <img src="<?php echo e(URL::asset('build/images/small/img-3.jpg')); ?>" alt=""
                                                            class="img-fluid rounded" />
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-4">
                                                        <img src="<?php echo e(URL::asset('build/images/small/img-4.jpg')); ?>" alt=""
                                                            class="img-fluid rounded" />
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                        </div>
                                        <small class="mb-0 text-muted">25 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-6.jpg')); ?>" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Bethany Johnson</h6>
                                        <p class="text-muted mb-2">added a new member to velzon dashboard</p>
                                        <small class="mb-0 text-muted">19 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-danger-subtle text-danger">
                                                <i class="ri-shopping-bag-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Your order is placed <span
                                                class="badge bg-danger-subtle text-danger align-middle ms-1">Out of
                                                Delivery</span></h6>
                                        <p class="text-muted mb-2">These customers can rest assured their order has been
                                            placed.</p>
                                        <small class="mb-0 text-muted">16 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-7.jpg')); ?>" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Lewis Pratt</h6>
                                        <p class="text-muted mb-2">They all have something to say beyond the words on the
                                            page. They can come across as casual or neutral, exotic or graphic. </p>
                                        <small class="mb-0 text-muted">22 Oct</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                                <i class="ri-line-chart-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Monthly sales report</h6>
                                        <p class="text-muted mb-2"><span class="text-danger">2 days left</span>
                                            notification to submit the monthly sales report. <a href="javascript:void(0);"
                                                class="link-warning text-decoration-underline">Reports Builder</a></p>
                                        <small class="mb-0 text-muted">15 Oct</small>
                                    </div>
                                </div>
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-8.jpg')); ?>" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">New ticket received <span
                                                class="badge bg-success-subtle text-success align-middle">Completed</span>
                                        </h6>
                                        <p class="text-muted mb-2">User <span class="text-primary">Erica245</span>
                                            submitted a ticket.</p>
                                        <small class="mb-0 text-muted">26 Aug</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-team" role="tabpanel">
                    <div class="row g-4 mb-3">
                        <div class="col-sm">
                            <div class="d-flex">
                                <div class="search-box me-2">
                                    <input type="text" class="form-control" placeholder="Search member...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <div>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#inviteMembersModal"><i class="ri-share-line me-1 align-bottom"></i>
                                    Invite Member</button>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="team-list list-view-filter">
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-2.jpg')); ?>" alt=""
                                                    class="img-fluid d-block rounded-circle" />
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Nancy Martino</h5>
                                                </a>
                                                <p class="text-muted mb-0">Team Leader & HR</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">225</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">197</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn active">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <div class="avatar-title bg-danger-subtle text-danger rounded-circle">
                                                    HB
                                                </div>
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Henry Baird</h5>
                                                </a>
                                                <p class="text-muted mb-0">Full Stack Developer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">352</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">376</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn active">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>" alt=""
                                                    class="img-fluid d-block rounded-circle" />
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Frank Hook</h5>
                                                </a>
                                                <p class="text-muted mb-0">Project Manager</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">164</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">182</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-8.jpg')); ?>" alt=""
                                                    class="img-fluid d-block rounded-circle" />
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Jennifer Carter</h5>
                                                </a>
                                                <p class="text-muted mb-0">UI/UX Designer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">225</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">197</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                                    ME
                                                </div>
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Megan Elmore</h5>
                                                </a>
                                                <p class="text-muted mb-0">Team Leader & Web Developer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">201</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">263</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-4.jpg')); ?>" alt=""
                                                    class="img-fluid d-block rounded-circle" />
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Alexis Clarke</h5>
                                                </a>
                                                <p class="text-muted mb-0">Backend Developer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">132</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">147</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <div class="avatar-title bg-info-subtle text-info rounded-circle">
                                                    NC
                                                </div>
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Nathan Cole</h5>
                                                </a>
                                                <p class="text-muted mb-0">Front-End Developer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">352</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">376</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-7.jpg')); ?>" alt=""
                                                    class="img-fluid d-block rounded-circle" />
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Joseph Parker</h5>
                                                </a>
                                                <p class="text-muted mb-0">Team Leader & HR</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">64</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">93</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <img src="<?php echo e(URL::asset('build/images/users/avatar-5.jpg')); ?>" alt=""
                                                    class="img-fluid d-block rounded-circle" />
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Erica Kernan</h5>
                                                </a>
                                                <p class="text-muted mb-0">Web Designer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">345</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">298</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="flex-shrink-0 me-2">
                                                    <button type="button" class="btn fs-16 p-0 favourite-btn">
                                                        <i class="ri-star-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-eye-fill text-muted me-2 align-bottom"></i>View</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-star-fill text-muted me-2 align-bottom"></i>Favourite</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                <div class="avatar-title border bg-light text-primary rounded-circle">
                                                    DP
                                                </div>
                                            </div>
                                            <div class="team-content">
                                                <a href="#" class="d-block">
                                                    <h5 class="fs-16 mb-1">Donald Palmer</h5>
                                                </a>
                                                <p class="text-muted mb-0">Wed Developer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">97</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">135</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="pages-profile" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                    </div>
                    <!-- end team list -->

                    <div class="row g-0 text-center text-sm-start align-items-center mb-3">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">Showing 1 to 10 of 12 entries</p>
                            </div>
                        </div> <!-- end col -->
                        <div class="col-sm-6">
                            <ul
                                class="pagination pagination-separated justify-content-center justify-content-sm-end mb-sm-0">
                                <li class="page-item disabled"> <a href="#" class="page-link"><i
                                            class="mdi mdi-chevron-left"></i></a> </li>
                                <li class="page-item"> <a href="#" class="page-link">1</a> </li>
                                <li class="page-item active"> <a href="#" class="page-link">2</a> </li>
                                <li class="page-item"> <a href="#" class="page-link">3</a> </li>
                                <li class="page-item"> <a href="#" class="page-link">4</a> </li>
                                <li class="page-item"> <a href="#" class="page-link">5</a> </li>
                                <li class="page-item"> <a href="#" class="page-link"><i
                                            class="mdi mdi-chevron-right"></i></a> </li>
                            </ul>
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div>
                <!-- end tab pane -->
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    <!-- Modal -->
    <div class="modal fade" id="inviteMembersModal" tabindex="-1" aria-labelledby="inviteMembersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header p-3 ps-4 bg-success-subtle">
                    <h5 class="modal-title" id="inviteMembersModalLabel">Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="search-box mb-3">
                        <input type="text" class="form-control bg-light border-light" placeholder="Search here...">
                        <i class="ri-search-line search-icon"></i>
                    </div>

                    <div class="mb-4 d-flex align-items-center">
                        <div class="me-2">
                            <h5 class="mb-0 fs-13">Members :</h5>
                        </div>
                        <div class="avatar-group justify-content-center">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                <div class="avatar-xs">
                                    <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-placement="top" title="Sylvia Wright">
                                <div class="avatar-xs">
                                    <div class="avatar-title rounded-circle bg-secondary">
                                        S
                                    </div>
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-placement="top" title="Ellen Smith">
                                <div class="avatar-xs">
                                    <img src="<?php echo e(URL::asset('build/images/users/avatar-4.jpg')); ?>" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="mx-n4 px-4" data-simplebar style="max-height: 225px;">
                        <div class="vstack gap-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="<?php echo e(URL::asset('build/images/users/avatar-2.jpg')); ?>" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Nancy Martino</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <div class="avatar-title bg-danger-subtle text-danger rounded-circle">
                                        HB
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Henry Baird</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Frank Hook</a></h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="<?php echo e(URL::asset('build/images/users/avatar-4.jpg')); ?>" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Jennifer Carter</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                        AC
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Alexis Clarke</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="<?php echo e(URL::asset('build/images/users/avatar-7.jpg')); ?>" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Joseph Parker</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                        </div>
                        <!-- end list -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light w-xs" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success w-xs">Invite</button>
                </div>
            </div>
            <!-- end modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!-- end modal -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/js/pages/project-overview.init.js')); ?>"></script>
    <script>
        // Favorite toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteBtn = document.querySelector('.favourite-btn');
            
            if (favoriteBtn) {
                favoriteBtn.addEventListener('click', function() {
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
            }
        });
    </script>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/apps-projects-overview.blade.php ENDPATH**/ ?>