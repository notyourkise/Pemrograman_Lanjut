<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
.luxury-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border: 1px solid #d4af37;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(212, 175, 55, 0.1);
}

.luxury-card-header {
    background: linear-gradient(135deg, #d4af37 0%, #f4e4a6 50%, #d4af37 100%);
    color: #1a1a1a;
    border-radius: 15px 15px 0 0 !important;
    border-bottom: 3px solid #b8941f;
    padding: 1.5rem;
}

.luxury-card-body {
    padding: 2rem;
    color: #e0e0e0;
}

.form-label {
    color: #d4af37;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    background-color: #2d2d2d;
    border: 1px solid #444;
    color: #e0e0e0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    background-color: #333;
    border-color: #d4af37;
    box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    color: #fff;
}

.form-control::placeholder {
    color: #888;
}

.btn-outline-primary, .btn-outline-success {
    border-color: #d4af37;
    color: #d4af37;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover, .btn-outline-success:hover {
    background-color: #d4af37;
    border-color: #d4af37;
    color: #1a1a1a;
}

.btn-check:checked + .btn-outline-primary,
.btn-check:checked + .btn-outline-success {
    background-color: #d4af37;
    border-color: #d4af37;
    color: #1a1a1a;
}

.luxury-alert {
    background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 100%);
    border: 1px solid #d4af37;
    border-left: 4px solid #d4af37;
    color: #e0e0e0;
    border-radius: 10px;
    padding: 1rem 1.5rem;
}

.luxury-divider {
    border-top: 2px solid #d4af37;
    margin: 2rem 0;
    opacity: 0.3;
}

.section-title {
    color: #d4af37;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary-luxury {
    background: linear-gradient(135deg, #d4af37 0%, #f4e4a6 50%, #d4af37 100%);
    border: none;
    color: #1a1a1a;
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
}

.btn-primary-luxury:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    background: linear-gradient(135deg, #f4e4a6 0%, #d4af37 50%, #f4e4a6 100%);
}

.btn-secondary-luxury {
    background-color: #2d2d2d;
    border: 1px solid #d4af37;
    color: #d4af37;
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-secondary-luxury:hover {
    background-color: #3a3a3a;
    color: #f4e4a6;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb-item a {
    color: #d4af37;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #888;
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}
</style>

<div class="container-fluid px-4 my-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('appointments/receptionist') ?>">Appointments</a></li>
                    <li class="breadcrumb-item active">Create Appointment</li>
                </ol>
            </nav>

            <div class="luxury-card">
                <div class="luxury-card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-plus"></i> Create New Appointment
                    </h4>
                </div>
                <div class="luxury-card-body">
                    <!-- Info Alert -->
                    <div class="luxury-alert">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Note:</strong> You can either select an existing patient or register a new patient below.
                        Appointment will be sent to the selected doctor for approval.
                    </div>

                    <form method="POST" action="<?= url('appointments/receptionist/store') ?>">
                        <!-- Doctor Selection -->
                        <div class="mb-4">
                            <label for="doctor_id" class="form-label">
                                <i class="bi bi-person-badge"></i> Select Doctor *
                            </label>
                            <select class="form-select <?= isset($_SESSION['errors']['doctor_id']) ? 'is-invalid' : '' ?>" 
                                    id="doctor_id" 
                                    name="doctor_id" 
                                    required>
                                <option value="">Choose a doctor...</option>
                                <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>" <?= ($_SESSION['old']['doctor_id'] ?? '') == $doctor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($doctor['name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($_SESSION['errors']['doctor_id'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['doctor_id'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <hr class="luxury-divider">
                        <div class="section-title">
                            <i class="bi bi-person"></i> Patient Information
                        </div>

                        <!-- Patient Selection Type -->
                        <div class="mb-4">
                            <label class="form-label">Patient Type *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="patient_type" id="existing_patient" value="existing" 
                                       <?= ($_SESSION['old']['patient_type'] ?? 'new') === 'existing' ? 'checked' : '' ?>
                                       onchange="togglePatientForm()">
                                <label class="btn btn-outline-primary" for="existing_patient">
                                    <i class="bi bi-search"></i> Select Existing Patient
                                </label>

                                <input type="radio" class="btn-check" name="patient_type" id="new_patient" value="new"
                                       <?= ($_SESSION['old']['patient_type'] ?? 'new') === 'new' ? 'checked' : '' ?>
                                       onchange="togglePatientForm()">
                                <label class="btn btn-outline-success" for="new_patient">
                                    <i class="bi bi-person-plus"></i> Register New Patient
                                </label>
                            </div>
                        </div>

                        <!-- Existing Patient Dropdown -->
                        <div id="existing_patient_form" style="display: none;">
                            <div class="mb-3">
                                <label for="patient_id" class="form-label">Select Patient</label>
                                <select class="form-select <?= isset($_SESSION['errors']['patient_id']) ? 'is-invalid' : '' ?>" 
                                        id="patient_id" 
                                        name="patient_id">
                                    <option value="">Select Patient</option>
                                    <?php foreach ($patients as $patient): ?>
                                    <option value="<?= $patient['id'] ?>" <?= ($_SESSION['old']['patient_id'] ?? '') == $patient['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($patient['name']) ?> - <?= htmlspecialchars($patient['phone']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($_SESSION['errors']['patient_id'])): ?>
                                <div class="invalid-feedback">
                                    <?= $_SESSION['errors']['patient_id'] ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- New Patient Form -->
                        <div id="new_patient_form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_name" class="form-label">Patient Name *</label>
                                    <input type="text" 
                                           class="form-control <?= isset($_SESSION['errors']['patient_name']) ? 'is-invalid' : '' ?>" 
                                           id="patient_name" 
                                           name="patient_name" 
                                           value="<?= $_SESSION['old']['patient_name'] ?? '' ?>"
                                           placeholder="Full name">
                                    <?php if (isset($_SESSION['errors']['patient_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['errors']['patient_name'] ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="patient_gender" class="form-label">Gender *</label>
                                    <select class="form-select <?= isset($_SESSION['errors']['patient_gender']) ? 'is-invalid' : '' ?>" 
                                            id="patient_gender" 
                                            name="patient_gender">
                                        <option value="">Select Gender</option>
                                        <option value="M" <?= ($_SESSION['old']['patient_gender'] ?? '') === 'M' ? 'selected' : '' ?>>Male</option>
                                        <option value="F" <?= ($_SESSION['old']['patient_gender'] ?? '') === 'F' ? 'selected' : '' ?>>Female</option>
                                    </select>
                                    <?php if (isset($_SESSION['errors']['patient_gender'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['errors']['patient_gender'] ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_dob" class="form-label">Date of Birth *</label>
                                    <input type="date" 
                                           class="form-control <?= isset($_SESSION['errors']['patient_dob']) ? 'is-invalid' : '' ?>" 
                                           id="patient_dob" 
                                           name="patient_dob" 
                                           value="<?= $_SESSION['old']['patient_dob'] ?? '' ?>"
                                           max="<?= date('Y-m-d') ?>">
                                    <?php if (isset($_SESSION['errors']['patient_dob'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['errors']['patient_dob'] ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="patient_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" 
                                           class="form-control <?= isset($_SESSION['errors']['patient_phone']) ? 'is-invalid' : '' ?>" 
                                           id="patient_phone" 
                                           name="patient_phone" 
                                           value="<?= $_SESSION['old']['patient_phone'] ?? '' ?>"
                                           placeholder="08xx-xxxx-xxxx">
                                    <?php if (isset($_SESSION['errors']['patient_phone'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['errors']['patient_phone'] ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="patient_address" class="form-label">Address *</label>
                                <textarea class="form-control <?= isset($_SESSION['errors']['patient_address']) ? 'is-invalid' : '' ?>" 
                                          id="patient_address" 
                                          name="patient_address" 
                                          rows="2"
                                          placeholder="Full address"><?= $_SESSION['old']['patient_address'] ?? '' ?></textarea>
                                <?php if (isset($_SESSION['errors']['patient_address'])): ?>
                                <div class="invalid-feedback">
                                    <?= $_SESSION['errors']['patient_address'] ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-calendar-event"></i> Appointment Schedule</h5>

                        <hr class="luxury-divider">
                        <div class="section-title">
                            <i class="bi bi-calendar-event"></i> Appointment Schedule
                        </div>

                        <!-- Schedule Date -->
                        <div class="mb-4">
                            <label for="schedule_date" class="form-label">
                                <i class="bi bi-calendar3"></i> Appointment Date *
                            </label>
                            <input type="date" 
                                   class="form-control <?= isset($_SESSION['errors']['schedule_date']) ? 'is-invalid' : '' ?>" 
                                   id="schedule_date" 
                                   name="schedule_date" 
                                   value="<?= $_SESSION['old']['schedule_date'] ?? '' ?>"
                                   min="<?= date('Y-m-d') ?>" 
                                   required>
                            <?php if (isset($_SESSION['errors']['schedule_date'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['schedule_date'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Schedule Time -->
                        <div class="mb-4">
                            <label for="schedule_time" class="form-label">
                                <i class="bi bi-clock"></i> Appointment Time *
                            </label>
                            <select class="form-select <?= isset($_SESSION['errors']['schedule_time']) ? 'is-invalid' : '' ?>" 
                                    id="schedule_time" 
                                    name="schedule_time" 
                                    required>
                                <option value="">Select Time</option>
                                <option value="08:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '08:00' ? 'selected' : '' ?>>08:00 AM</option>
                                <option value="09:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '09:00' ? 'selected' : '' ?>>09:00 AM</option>
                                <option value="10:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '10:00' ? 'selected' : '' ?>>10:00 AM</option>
                                <option value="11:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '11:00' ? 'selected' : '' ?>>11:00 AM</option>
                                <option value="13:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '13:00' ? 'selected' : '' ?>>01:00 PM</option>
                                <option value="14:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '14:00' ? 'selected' : '' ?>>02:00 PM</option>
                                <option value="15:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '15:00' ? 'selected' : '' ?>>03:00 PM</option>
                                <option value="16:00" <?= ($_SESSION['old']['schedule_time'] ?? '') == '16:00' ? 'selected' : '' ?>>04:00 PM</option>
                            </select>
                            <?php if (isset($_SESSION['errors']['schedule_time'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['schedule_time'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <i class="bi bi-journal-text"></i> Additional Notes
                            </label>
                            <textarea class="form-control <?= isset($_SESSION['errors']['notes']) ? 'is-invalid' : '' ?>" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="Enter appointment notes or special instructions..."><?= $_SESSION['old']['notes'] ?? '' ?></textarea>
                            <?php if (isset($_SESSION['errors']['notes'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['notes'] ?>
                            </div>
                            <?php else: ?>
                            <small style="color: #888;">Add any relevant notes for the doctor</small>
                            <?php endif; ?>
                        </div>

                        <hr class="luxury-divider">

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="<?= url('appointments/receptionist') ?>" class="btn btn-secondary-luxury">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary-luxury">
                                <i class="bi bi-send-fill"></i> Create Appointment
                            </button>
                        </div>
                    </form>                    <?php 
                    // Clear old input and errors
                    unset($_SESSION['old']);
                    unset($_SESSION['errors']);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle between existing patient and new patient form
function togglePatientForm() {
    const existingForm = document.getElementById('existing_patient_form');
    const newForm = document.getElementById('new_patient_form');
    const existingRadio = document.getElementById('existing_patient');
    const patientSelect = document.getElementById('patient_id');
    const newPatientInputs = newForm.querySelectorAll('input, select, textarea');
    
    if (existingRadio.checked) {
        // Show existing patient dropdown
        existingForm.style.display = 'block';
        newForm.style.display = 'none';
        patientSelect.required = true;
        
        // Remove required from new patient fields
        newPatientInputs.forEach(input => {
            input.required = false;
        });
    } else {
        // Show new patient form
        existingForm.style.display = 'none';
        newForm.style.display = 'block';
        patientSelect.required = false;
        patientSelect.value = '';
        
        // Add required to new patient fields
        document.getElementById('patient_name').required = true;
        document.getElementById('patient_gender').required = true;
        document.getElementById('patient_dob').required = true;
        document.getElementById('patient_phone').required = true;
        document.getElementById('patient_address').required = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePatientForm();
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
