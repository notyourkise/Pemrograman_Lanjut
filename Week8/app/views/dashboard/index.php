<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 36px;
        color: white;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }
    
    .card-header.bg-white {
        background-color: white !important;
    }
</style>

<div class="container my-4">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col">
            <h2>
                <i class="bi bi-speedometer2"></i> Dashboard
            </h2>
            <p class="text-muted">Welcome back, <?= htmlspecialchars($user->getFullName()) ?>! 
                <span class="badge bg-<?= $user->isAdmin() ? 'danger' : ($user->isDoctor() ? 'primary' : 'success') ?>">
                    <?= $user->getRoleLabel() ?>
                </span>
            </p>
        </div>
        <div class="col-auto">
            <div class="text-end">
                <small class="text-muted">
                    <i class="bi bi-clock"></i> 
                    Last login: <?= $user->getLastLogin() ? date('M d, Y H:i', strtotime($user->getLastLogin())) : 'Never' ?>
                </small>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Patients -->
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Patients</h6>
                            <h2 class="card-title mb-0"><?= number_format($stats['total_patients']) ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
                <?php if (Auth::getInstance()->can('patients.view')): ?>
                <div class="card-footer bg-primary bg-opacity-75 border-0">
                    <a href="<?= url('patients') ?>" class="text-white text-decoration-none">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Total Appointments -->
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Appointments</h6>
                            <h2 class="card-title mb-0"><?= number_format($stats['total_appointments']) ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
                <?php if (Auth::getInstance()->can('appointments.view')): ?>
                <div class="card-footer bg-success bg-opacity-75 border-0">
                    <a href="<?= url('appointments') ?>" class="text-white text-decoration-none">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Scheduled Appointments -->
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-dark-50">Scheduled</h6>
                            <h2 class="card-title mb-0"><?= number_format($stats['scheduled_appointments']) ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning bg-opacity-75 border-0">
                    <a href="<?= url('appointments?status=scheduled') ?>" class="text-dark text-decoration-none">
                        View Scheduled <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Doctors -->
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Doctors</h6>
                            <h2 class="card-title mb-0"><?= number_format($stats['total_doctors']) ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-info bg-opacity-75 border-0">
                    <a href="<?= url('doctors') ?>" class="text-white text-decoration-none">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-Specific Content -->
    <div class="row">
        <div class="col-md-8">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php if (Auth::getInstance()->can('appointments.create')): ?>
                        <div class="col-md-6">
                            <?php if (Auth::getInstance()->hasRole('receptionist')): ?>
                                <a href="<?= url('appointments/receptionist/create') ?>" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-calendar-plus"></i> New Appointment
                                </a>
                            <?php else: ?>
                                <a href="<?= url('appointments/create') ?>" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-calendar-plus"></i> New Appointment
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (Auth::getInstance()->can('patients.create')): ?>
                        <div class="col-md-6">
                            <a href="<?= url('patients/create') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-person-plus"></i> New Patient
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (Auth::getInstance()->hasRole('admin')): ?>
                        <div class="col-md-6">
                            <a href="<?= url('users/create') ?>" class="btn btn-outline-danger w-100">
                                <i class="bi bi-person-plus-fill"></i> New User
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= url('audit-logs') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-journal-text"></i> View Audit Logs
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Role-Specific Information -->
            <?php if ($user->isAdmin()): ?>
            <!-- Admin: User Statistics -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-people"></i> User Statistics</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['user_counts'] as $role => $count): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= ucfirst($role) ?></span></td>
                                <td class="text-end"><strong><?= $count ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php elseif ($user->isDoctor()): ?>
            <!-- Doctor: Today's Schedule -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Today's Schedule</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Your appointments for today will appear here.</p>
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-2 text-muted">No appointments scheduled for today</p>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Receptionist: Recent Activities -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-activity"></i> Recent Activities</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Recent patient registrations and appointment bookings will appear here.</p>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-2 text-muted">No recent activities</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- User Profile Card -->
            <div class="card mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profile</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($user->getFullName(), 0, 1)) ?>
                        </div>
                    </div>
                    <h5 class="mb-1"><?= htmlspecialchars($user->getFullName()) ?></h5>
                    <p class="text-muted mb-3">@<?= htmlspecialchars($user->getUsername()) ?></p>
                    <span class="badge bg-<?= $user->isAdmin() ? 'danger' : ($user->isDoctor() ? 'primary' : 'success') ?> mb-3">
                        <?= $user->getRoleLabel() ?>
                    </span>
                    <div class="d-grid">
                        <a href="/profile" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
