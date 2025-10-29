<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-calendar-check"></i> Appointments Management</h2>
                    <p class="text-muted">Manage all appointments and cancellation requests</p>
                </div>
                <div>
                    <a href="<?= url('appointments/receptionist/create') ?>" class="btn btn-primary">
                        <i class="bi bi-calendar-plus"></i> New Appointment
                    </a>
                </div>
            </div>

            <!-- Pending Cancellation Requests (Priority) -->
            <?php if (!empty($pendingCancels)): ?>
            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Cancellation Requests Pending Your Review (<?= count($pendingCancels) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Action Required:</strong> Doctors have requested cancellation for these appointments. Please review and approve/reject.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Doctor</th>
                                    <th>Patient</th>
                                    <th>Schedule</th>
                                    <th>Cancellation Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($pendingCancels as $apt): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <i class="bi bi-person-badge"></i>
                                        <?= htmlspecialchars($apt->getDoctorName() ?? 'N/A') ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($apt->getPatientName() ?? 'N/A') ?></strong>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-event"></i>
                                        <?= $apt->getFormattedSchedule('D, M d, Y') ?><br>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?= $apt->getFormattedSchedule('H:i') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-danger">
                                            <i class="bi bi-chat-left-text"></i>
                                            <?= htmlspecialchars($apt->getCancelReason() ?? '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form method="POST" action="<?= url('appointments/receptionist/approve-cancel/' . $apt->getId()) ?>" style="display:inline;">
                                                <button type="submit" class="btn btn-danger" title="Approve Cancellation">
                                                    <i class="bi bi-check-circle"></i> Approve Cancel
                                                </button>
                                            </form>
                                            <form method="POST" action="<?= url('appointments/receptionist/reject-cancel/' . $apt->getId()) ?>" style="display:inline;">
                                                <button type="submit" class="btn btn-success" title="Reject Cancellation (Keep Scheduled)">
                                                    <i class="bi bi-x-circle"></i> Reject Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filter and Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= url('appointments/receptionist') ?>" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending_doctor" <?= ($_GET['status'] ?? '') === 'pending_doctor' ? 'selected' : '' ?>>
                                    Pending Doctor
                                </option>
                                <option value="scheduled" <?= ($_GET['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>
                                    Scheduled
                                </option>
                                <option value="pending_cancel" <?= ($_GET['status'] ?? '') === 'pending_cancel' ? 'selected' : '' ?>>
                                    Pending Cancel
                                </option>
                                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>
                                    Cancelled
                                </option>
                                <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>
                                    Completed
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Doctor</label>
                            <select name="doctor_id" class="form-select">
                                <option value="">All Doctors</option>
                                <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>" <?= ($_GET['doctor_id'] ?? '') == $doctor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($doctor['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="<?= $_GET['date'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="<?= url('appointments/receptionist') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- All Appointments -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Appointments (<?= $pagination['total_items'] ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No appointments found
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Doctor</th>
                                        <th>Patient</th>
                                        <th>Schedule</th>
                                        <th>Notes</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = ($pagination['current_page'] - 1) * $pagination['per_page'] + 1;
                                    foreach ($appointments as $apt): 
                                    ?>
                                    <tr class="<?= $apt->isToday() && $apt->isScheduled() ? 'table-warning' : '' ?>">
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="bi bi-person-badge"></i>
                                            <?= htmlspecialchars($apt->getDoctorName() ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($apt->getPatientName() ?? 'N/A') ?></strong>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar-event"></i>
                                            <?= $apt->getFormattedSchedule('M d, Y') ?>
                                            <?php if ($apt->isToday()): ?>
                                                <span class="badge bg-warning text-dark ms-1">Today</span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> <?= $apt->getFormattedSchedule('H:i') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small><?= htmlspecialchars(substr($apt->getNotes() ?? '-', 0, 30)) ?><?= strlen($apt->getNotes() ?? '') > 30 ? '...' : '' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $apt->getStatusBadgeClass() ?>">
                                                <?= $apt->getStatusLabel() ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($apt->getCreatedByName() ?? 'System') ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Info -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                <?= PaginationHelper::info($pagination['current_page'], $pagination['per_page'], $pagination['total_items']) ?>
                            </div>
                            <div>
                                <?= PaginationHelper::render($pagination['current_page'], $pagination['total_pages'], url('appointments/receptionist', false), $filters) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
