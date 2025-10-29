<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-calendar-check"></i> My Appointments</h2>
                    <p class="text-muted">Manage your appointment schedule</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card text-center bg-warning text-white">
                        <div class="card-body">
                            <h3><?= $stats[Appointment::STATUS_PENDING_DOCTOR] ?? 0 ?></h3>
                            <p class="mb-0">Pending Approval</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-success text-white">
                        <div class="card-body">
                            <h3><?= $stats[Appointment::STATUS_SCHEDULED] ?? 0 ?></h3>
                            <p class="mb-0">Scheduled</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-info text-white">
                        <div class="card-body">
                            <h3><?= $stats[Appointment::STATUS_PENDING_CANCEL] ?? 0 ?></h3>
                            <p class="mb-0">Pending Cancellation</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-danger text-white">
                        <div class="card-body">
                            <h3><?= $stats[Appointment::STATUS_CANCELLED] ?? 0 ?></h3>
                            <p class="mb-0">Cancelled</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals Tab -->
            <?php if ($pagination['pending']['total_items'] > 0): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pending Your Approval (<?= $pagination['pending']['total_items'] ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Patient</th>
                                    <th>Schedule</th>
                                    <th>Notes</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = ($pagination['pending']['current_page'] - 1) * $pagination['pending']['per_page'] + 1;
                                foreach ($pendingApprovals as $apt): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($apt->getPatientName()) ?></strong></td>
                                    <td>
                                        <i class="bi bi-calendar-event"></i>
                                        <?= $apt->getFormattedSchedule('D, M d, Y') ?><br>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?= $apt->getFormattedSchedule('H:i') ?>
                                        </small>
                                    </td>
                                    <td><?= htmlspecialchars($apt->getNotes() ?? '-') ?></td>
                                    <td><?= htmlspecialchars($apt->getCreatedByName() ?? 'Receptionist') ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form method="POST" action="<?= url('appointments/doctor/approve/' . $apt->getId()) ?>" style="display:inline;">
                                                <button type="submit" class="btn btn-success" title="Approve">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    class="btn btn-danger" 
                                                    onclick="showRejectModal(<?= $apt->getId() ?>, '<?= htmlspecialchars($apt->getPatientName()) ?>')"
                                                    title="Reject">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            <?= PaginationHelper::info($pagination['pending']['current_page'], $pagination['pending']['per_page'], $pagination['pending']['total_items']) ?>
                        </div>
                        <div>
                            <?= PaginationHelper::render($pagination['pending']['current_page'], $pagination['pending']['total_pages'], url('appointments/doctor', false), []) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>            <!-- Scheduled Appointments -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Scheduled Appointments (<?= $pagination['scheduled']['total_items'] ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($scheduled)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No scheduled appointments
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Patient</th>
                                        <th>Schedule</th>
                                        <th>Notes</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = ($pagination['scheduled']['current_page'] - 1) * $pagination['scheduled']['per_page'] + 1;
                                    foreach ($scheduled as $apt): 
                                    ?>
                                    <tr class="<?= $apt->isToday() ? 'table-warning' : '' ?>">
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($apt->getPatientName()) ?></strong>
                                            <?php if ($apt->isToday()): ?>
                                                <span class="badge bg-warning text-dark ms-1">Today</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar-event"></i>
                                            <?= $apt->getFormattedSchedule('D, M d, Y') ?><br>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> <?= $apt->getFormattedSchedule('H:i') ?>
                                            </small>
                                        </td>
                                        <td><?= htmlspecialchars($apt->getNotes() ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $apt->getStatusBadgeClass() ?>">
                                                <?= $apt->getStatusLabel() ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($apt->isScheduled() && $apt->isToday()): ?>
                                                <!-- Start Button -->
                                                <form method="POST" action="<?= url('appointments/doctor/start/' . $apt->getId()) ?>" style="display:inline;">
                                                    <button type="submit" class="btn btn-sm btn-primary" title="Start Appointment">
                                                        <i class="bi bi-play-circle"></i> Start
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($apt->isInProgress() || ($apt->isScheduled() && !$apt->isPast())): ?>
                                                <!-- Complete Button -->
                                                <form method="POST" action="<?= url('appointments/doctor/complete/' . $apt->getId()) ?>" style="display:inline;">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Complete Appointment">
                                                        <i class="bi bi-check-circle"></i> Complete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($apt->canDoctorRequestCancel()): ?>
                                                <!-- Cancel Request Button -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="showCancelModal(<?= $apt->getId() ?>, '<?= htmlspecialchars($apt->getPatientName()) ?>')">
                                                    <i class="bi bi-x-circle"></i> Request Cancel
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if (!$apt->canBeStarted() && !$apt->canBeCompleted() && !$apt->canDoctorRequestCancel()): ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Info -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                <?= PaginationHelper::info($pagination['scheduled']['current_page'], $pagination['scheduled']['per_page'], $pagination['scheduled']['total_items']) ?>
                            </div>
                            <div>
                                <?= PaginationHelper::render($pagination['scheduled']['current_page'], $pagination['scheduled']['total_pages'], url('appointments/doctor', false), ['scheduled_page' => $pagination['scheduled']['current_page']]) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Cancellations -->
            <?php if ($pagination['cancels']['total_items'] > 0): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Pending Cancellation Review (<?= $pagination['cancels']['total_items'] ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> These cancellation requests are awaiting receptionist approval
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Patient</th>
                                    <th>Schedule</th>
                                    <th>Cancel Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = ($pagination['cancels']['current_page'] - 1) * $pagination['cancels']['per_page'] + 1;
                                foreach ($pendingCancels as $apt): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($apt->getPatientName()) ?></strong></td>
                                    <td>
                                        <?= $apt->getFormattedSchedule('D, M d, Y H:i') ?>
                                    </td>
                                    <td><?= htmlspecialchars($apt->getCancelReason() ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $apt->getStatusBadgeClass() ?>">
                                            <?= $apt->getStatusLabel() ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            <?= PaginationHelper::info($pagination['cancels']['current_page'], $pagination['cancels']['per_page'], $pagination['cancels']['total_items']) ?>
                        </div>
                        <div>
                            <?= PaginationHelper::render($pagination['cancels']['current_page'], $pagination['cancels']['total_pages'], url('appointments/doctor', false), ['cancel_page' => $pagination['cancels']['current_page']]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Cancellation Requests History (Reviewed) -->
            <?php if ($pagination['reviewed']['total_items'] > 0): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Cancellation Requests History (<?= $pagination['reviewed']['total_items'] ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-secondary">
                        <i class="bi bi-info-circle"></i> Review status of your cancellation requests
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Patient</th>
                                    <th>Schedule</th>
                                    <th>Cancel Reason</th>
                                    <th>Review Status</th>
                                    <th>Reviewed By</th>
                                    <th>Reviewed At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = ($pagination['reviewed']['current_page'] - 1) * $pagination['reviewed']['per_page'] + 1;
                                foreach ($reviewedCancels as $apt): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($apt->getPatientName()) ?></strong></td>
                                    <td>
                                        <?= $apt->getFormattedSchedule('D, M d, Y H:i') ?>
                                    </td>
                                    <td><?= htmlspecialchars($apt->getCancelReason() ?? '-') ?></td>
                                    <td>
                                        <?php if ($apt->isCancelApproved()): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Approved
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> Rejected
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($apt->getCancelReviewedByName() ?? '-') ?></td>
                                    <td><?= $apt->getFormattedCancelReviewedAt('M d, Y H:i') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            <?= PaginationHelper::info($pagination['reviewed']['current_page'], $pagination['reviewed']['per_page'], $pagination['reviewed']['total_items']) ?>
                        </div>
                        <div>
                            <?= PaginationHelper::render($pagination['reviewed']['current_page'], $pagination['reviewed']['total_pages'], url('appointments/doctor', false), ['reviewed_page' => $pagination['reviewed']['current_page']]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject appointment with <strong id="rejectPatientName"></strong>?</p>
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Reason (optional)</label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" placeholder="Enter rejection reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Request Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cancelForm" method="POST">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Request Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Request cancellation for appointment with <strong id="cancelPatientName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> Your cancellation request will be reviewed by the receptionist
                    </div>
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason *</label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" placeholder="Enter cancellation reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(appointmentId, patientName) {
    document.getElementById('rejectForm').action = '<?= url('appointments/doctor/reject/') ?>' + appointmentId;
    document.getElementById('rejectPatientName').textContent = patientName;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function showCancelModal(appointmentId, patientName) {
    document.getElementById('cancelForm').action = '<?= url('appointments/doctor/request-cancel/') ?>' + appointmentId;
    document.getElementById('cancelPatientName').textContent = patientName;
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
