<?php

/**
 * Week 8: Appointments Controller
 * Handles appointment workflow for doctors and receptionists
 */

class AppointmentsController extends Controller
{
    private $appointmentRepo;
    private $middleware;

    public function __construct()
    {
        parent::__construct();
        $this->appointmentRepo = new AppointmentRepository();
        $this->middleware = new AuthMiddleware();
    }

    /**
     * Doctor: View appointments dashboard
     */
    public function doctorIndex()
    {
        $this->middleware->requireDoctor();
        
        $user = Auth::getInstance()->user();
        $doctorId = $this->getDoctorIdFromUser($user->getId());
        
        if (!$doctorId) {
            Flash::set('error', 'Doctor profile not found. Your account (' . $user->getUsername() . ') is not linked to a doctor record. Please contact administrator.');
            $this->redirect(url('dashboard'));
        }
        
        // Pagination settings
        $perPage = 5;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        
        // Get appointments by status with pagination
        $pendingApprovals = $this->appointmentRepo->findByDoctorPaginated($doctorId, Appointment::STATUS_PENDING_DOCTOR, $page, $perPage);
        $totalPending = $this->appointmentRepo->countByDoctor($doctorId, Appointment::STATUS_PENDING_DOCTOR);
        
        $scheduledPage = isset($_GET['scheduled_page']) ? max(1, intval($_GET['scheduled_page'])) : 1;
        $scheduled = $this->appointmentRepo->findByDoctorPaginated($doctorId, Appointment::STATUS_SCHEDULED, $scheduledPage, $perPage);
        $totalScheduled = $this->appointmentRepo->countByDoctor($doctorId, Appointment::STATUS_SCHEDULED);
        
        $cancelPage = isset($_GET['cancel_page']) ? max(1, intval($_GET['cancel_page'])) : 1;
        $pendingCancels = $this->appointmentRepo->findByDoctorPaginated($doctorId, Appointment::STATUS_PENDING_CANCEL, $cancelPage, $perPage);
        $totalCancels = $this->appointmentRepo->countByDoctor($doctorId, Appointment::STATUS_PENDING_CANCEL);
        
        // Get reviewed cancellation requests (history)
        $reviewedPage = isset($_GET['reviewed_page']) ? max(1, intval($_GET['reviewed_page'])) : 1;
        $reviewedCancels = $this->appointmentRepo->getReviewedCancellationsForDoctor($doctorId, $reviewedPage, $perPage);
        $totalReviewed = $this->appointmentRepo->countReviewedCancellationsForDoctor($doctorId);
        
        // Get statistics
        $stats = $this->appointmentRepo->countByStatusForDoctor($doctorId);
        
        $this->view('appointments/doctor_index', [
            'title' => 'My Appointments',
            'pendingApprovals' => $pendingApprovals,
            'scheduled' => $scheduled,
            'pendingCancels' => $pendingCancels,
            'reviewedCancels' => $reviewedCancels,
            'stats' => $stats,
            'pagination' => [
                'pending' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalPending / $perPage),
                    'total_items' => $totalPending,
                    'per_page' => $perPage
                ],
                'scheduled' => [
                    'current_page' => $scheduledPage,
                    'total_pages' => ceil($totalScheduled / $perPage),
                    'total_items' => $totalScheduled,
                    'per_page' => $perPage
                ],
                'cancels' => [
                    'current_page' => $cancelPage,
                    'total_pages' => ceil($totalCancels / $perPage),
                    'total_items' => $totalCancels,
                    'per_page' => $perPage
                ],
                'reviewed' => [
                    'current_page' => $reviewedPage,
                    'total_pages' => ceil($totalReviewed / $perPage),
                    'total_items' => $totalReviewed,
                    'per_page' => $perPage
                ]
            ]
        ]);
    }

    /**
     * Doctor: Approve appointment
     */
    public function doctorApprove($id)
    {
        $this->middleware->requireDoctor();
        
        $user = Auth::getInstance()->user();
        $doctorId = $this->getDoctorIdFromUser($user->getId());
        
        if (!$doctorId) {
            Flash::set('error', 'Doctor profile not found. Please contact admin.');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            Flash::set('error', 'Appointment not found');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if ($appointment->getDoctorId() != $doctorId) {
            Flash::set('error', 'This appointment is not assigned to you');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if (!$appointment->canDoctorApprove()) {
            Flash::set('error', 'Cannot approve this appointment. Current status: ' . $appointment->getStatus());
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if ($this->appointmentRepo->approveByDoctor($id)) {
            Flash::set('success', 'Appointment approved successfully');
        } else {
            Flash::set('error', 'Failed to approve appointment');
        }
        
        $this->redirect(url('appointments/doctor'));
    }

    /**
     * Doctor: Reject appointment
     */
    public function doctorReject($id)
    {
        $this->middleware->requireDoctor();
        
        $user = Auth::getInstance()->user();
        $doctorId = $this->getDoctorIdFromUser($user->getId());
        
        if (!$doctorId) {
            Flash::set('error', 'Doctor profile not found. Please contact admin.');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            Flash::set('error', 'Appointment not found');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if ($appointment->getDoctorId() != $doctorId) {
            Flash::set('error', 'This appointment is not assigned to you');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if (!$appointment->canDoctorApprove()) {
            Flash::set('error', 'Cannot reject this appointment. Current status: ' . $appointment->getStatus());
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        $reason = $_POST['reason'] ?? 'Rejected by doctor';
        
        if ($this->appointmentRepo->rejectByDoctor($id, $reason)) {
            Flash::set('success', 'Appointment rejected');
        } else {
            Flash::set('error', 'Failed to reject appointment');
        }
        
        $this->redirect(url('appointments/doctor'));
    }

    /**
     * Doctor: Request cancellation
     */
    public function doctorRequestCancel($id)
    {
        $this->middleware->requireDoctor();
        
        $user = Auth::getInstance()->user();
        $doctorId = $this->getDoctorIdFromUser($user->getId());
        
        if (!$doctorId) {
            Flash::set('error', 'Doctor profile not found. Please contact admin.');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            Flash::set('error', 'Appointment not found');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if ($appointment->getDoctorId() != $doctorId) {
            Flash::set('error', 'This appointment is not assigned to you');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if (!$appointment->canDoctorRequestCancel()) {
            Flash::set('error', 'Cannot request cancellation. Current status: ' . $appointment->getStatus());
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        $reason = $_POST['reason'] ?? null;
        
        if (empty($reason)) {
            Flash::set('error', 'Cancellation reason is required');
            $this->redirect(url('appointments/doctor'));
            return;
        }
        
        if ($this->appointmentRepo->requestCancellation($id, $user->getId(), $reason)) {
            Flash::set('success', 'Cancellation request sent to receptionist');
        } else {
            Flash::set('error', 'Failed to request cancellation');
        }
        
        $this->redirect(url('appointments/doctor'));
    }

    /**
     * Doctor: Start appointment (change status to in_progress)
     */
    public function doctorStartAppointment($id)
    {
        $this->middleware->requireDoctor();
        
        $user = Auth::getInstance()->user();
        $doctorId = $this->getDoctorIdFromUser($user->getId());
        
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment || $appointment->getDoctorId() != $doctorId) {
            Flash::set('error', 'Appointment not found');
            $this->redirect(url('appointments/doctor'));
        }
        
        if (!$appointment->canBeStarted()) {
            Flash::set('error', 'Cannot start this appointment');
            $this->redirect(url('appointments/doctor'));
        }
        
        if ($this->appointmentRepo->startAppointment($id)) {
            Flash::set('success', 'Appointment started - Status changed to In Progress');
        } else {
            Flash::set('error', 'Failed to start appointment');
        }
        
        $this->redirect(url('appointments/doctor'));
    }
    
    /**
     * Doctor: Complete appointment
     */
    public function doctorCompleteAppointment($id)
    {
        $this->middleware->requireDoctor();
        
        $user = Auth::getInstance()->user();
        $doctorId = $this->getDoctorIdFromUser($user->getId());
        
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment || $appointment->getDoctorId() != $doctorId) {
            Flash::set('error', 'Appointment not found');
            $this->redirect(url('appointments/doctor'));
        }
        
        if (!$appointment->canBeCompleted()) {
            Flash::set('error', 'Cannot complete this appointment');
            $this->redirect(url('appointments/doctor'));
        }
        
        if ($this->appointmentRepo->completeAppointment($id)) {
            Flash::set('success', 'Appointment completed successfully');
        } else {
            Flash::set('error', 'Failed to complete appointment');
        }
        
        $this->redirect(url('appointments/doctor'));
    }

    /**
     * Receptionist: View all appointments
     */
    public function receptionistIndex()
    {
        $this->middleware->requireRole('receptionist');
        
        // Pagination settings
        $perPage = 5;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        
        // Get filters from query string
        $filters = [];
        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (isset($_GET['doctor_id']) && !empty($_GET['doctor_id'])) {
            $filters['doctor_id'] = $_GET['doctor_id'];
        }
        if (isset($_GET['date']) && !empty($_GET['date'])) {
            $filters['date'] = $_GET['date'];
        }
        
        // Get pending cancellation requests (without pagination)
        $pendingCancels = $this->appointmentRepo->findAll(['status' => Appointment::STATUS_PENDING_CANCEL]);
        
        // Get all appointments with filters and pagination
        $allAppointments = $this->appointmentRepo->findAllPaginated($filters, $page, $perPage);
        $totalAppointments = $this->appointmentRepo->countAll($filters);
        
        // Get doctors list for filter
        $doctors = $this->getDoctorsList();
        
        $this->view('appointments/receptionist_index', [
            'title' => 'Appointments Management',
            'pendingCancels' => $pendingCancels,
            'appointments' => $allAppointments,
            'doctors' => $doctors,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalAppointments / $perPage),
                'total_items' => $totalAppointments,
                'per_page' => $perPage
            ],
            'filters' => $filters
        ]);
    }
    
    /**
     * Receptionist: Show create appointment form
     */
    public function receptionistCreate()
    {
        $this->middleware->requireRole('receptionist');
        
        // Get doctors and patients for dropdowns
        $doctors = $this->getDoctorsList();
        $patients = $this->getPatientsList();
        
        $this->view('appointments/receptionist_create', [
            'title' => 'Create Appointment',
            'doctors' => $doctors,
            'patients' => $patients
        ]);
    }
    
    /**
     * Receptionist: Store new appointment
     */
    public function receptionistStore()
    {
        $this->middleware->requireRole('receptionist');
        
        // Validate input
        $errors = [];
        
        if (empty($_POST['doctor_id'])) {
            $errors['doctor_id'] = 'Doctor is required';
        }
        
        // Check patient type (existing or new)
        $patientType = $_POST['patient_type'] ?? 'new';
        $patientId = null;
        
        if ($patientType === 'existing') {
            // Validate existing patient
            if (empty($_POST['patient_id'])) {
                $errors['patient_id'] = 'Please select a patient';
            } else {
                $patientId = $_POST['patient_id'];
            }
        } else {
            // Validate new patient data
            if (empty($_POST['patient_name'])) {
                $errors['patient_name'] = 'Patient name is required';
            }
            if (empty($_POST['patient_gender'])) {
                $errors['patient_gender'] = 'Gender is required';
            }
            if (empty($_POST['patient_dob'])) {
                $errors['patient_dob'] = 'Date of birth is required';
            } elseif (strtotime($_POST['patient_dob']) >= strtotime(date('Y-m-d'))) {
                $errors['patient_dob'] = 'Invalid date of birth';
            }
            if (empty($_POST['patient_phone'])) {
                $errors['patient_phone'] = 'Phone number is required';
            } elseif (!preg_match('/^[0-9\-\+\(\)]*$/', $_POST['patient_phone'])) {
                $errors['patient_phone'] = 'Invalid phone number format';
            }
            if (empty($_POST['patient_address'])) {
                $errors['patient_address'] = 'Address is required';
            }
        }
        
        if (empty($_POST['schedule_date'])) {
            $errors['schedule_date'] = 'Appointment date is required';
        } elseif (strtotime($_POST['schedule_date']) < strtotime(date('Y-m-d'))) {
            $errors['schedule_date'] = 'Appointment date cannot be in the past';
        }
        
        if (empty($_POST['schedule_time'])) {
            $errors['schedule_time'] = 'Appointment time is required';
        }
        
        // If there are errors, redirect back
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect(url('appointments/receptionist/create'));
        }
        
        // Create new patient if needed
        if ($patientType === 'new') {
            try {
                $patientRepo = new PatientRepository();
                $patient = $patientRepo->create([
                    'name' => $_POST['patient_name'],
                    'gender' => $_POST['patient_gender'],
                    'dob' => $_POST['patient_dob'],
                    'phone' => $_POST['patient_phone'],
                    'address' => $_POST['patient_address']
                ]);
                $patientId = $patient->getId();
                Flash::set('info', 'New patient registered: ' . htmlspecialchars($_POST['patient_name']));
            } catch (Exception $e) {
                Flash::set('error', 'Failed to register patient: ' . $e->getMessage());
                $_SESSION['old'] = $_POST;
                $this->redirect(url('appointments/receptionist/create'));
            }
        }
        
        // Combine date and time
        $schedule = $_POST['schedule_date'] . ' ' . $_POST['schedule_time'] . ':00';
        
        // Create appointment
        try {
            $appointment = $this->appointmentRepo->create([
                'doctor_id' => $_POST['doctor_id'],
                'patient_id' => $patientId,
                'schedule' => $schedule,
                'notes' => $_POST['notes'] ?? null,
                'created_by' => Auth::getInstance()->user()->getId()
            ]);
            
            Flash::set('success', 'Appointment created successfully! Waiting for doctor approval.');
            $this->redirect(url('appointments/receptionist'));
        } catch (Exception $e) {
            Flash::set('error', 'Failed to create appointment. Please try again');
            $this->redirect(url('appointments/receptionist/create'));
        }
    }

    /**
     * Receptionist: Approve cancellation
     */
    public function receptionistApproveCancel($id)
    {
        $this->middleware->requireRole('receptionist');
        
        $user = Auth::getInstance()->user();
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            Flash::set('error', 'Appointment not found');
            $this->redirect(url('appointments/receptionist'));
        }
        
        if (!$appointment->canReceptionistApproveCancel()) {
            Flash::set('error', 'Cannot approve this cancellation');
            $this->redirect(url('appointments/receptionist'));
        }
        
        if ($this->appointmentRepo->approveCancellation($id, $user->getId())) {
            Flash::set('success', 'Cancellation approved. Doctor has been notified.');
        } else {
            Flash::set('error', 'Failed to approve cancellation');
        }
        
        $this->redirect(url('appointments/receptionist'));
    }

    /**
     * Receptionist: Reject cancellation
     */
    public function receptionistRejectCancel($id)
    {
        $this->middleware->requireRole('receptionist');
        
        $user = Auth::getInstance()->user();
        $appointment = $this->appointmentRepo->findById($id);
        
        if (!$appointment) {
            Flash::set('error', 'Appointment not found');
            $this->redirect(url('appointments/receptionist'));
        }
        
        if (!$appointment->canReceptionistApproveCancel()) {
            Flash::set('error', 'Cannot reject this cancellation');
            $this->redirect(url('appointments/receptionist'));
        }
        
        if ($this->appointmentRepo->rejectCancellation($id, $user->getId())) {
            Flash::set('success', 'Cancellation rejected. Appointment remains scheduled. Doctor has been notified.');
        } else {
            Flash::set('error', 'Failed to reject cancellation');
        }
        
        $this->redirect(url('appointments/receptionist'));
    }

    /**
     * Helper: Get doctor ID from user ID
     */
    private function getDoctorIdFromUser($userId)
    {
        // Since doctors table doesn't have user_id column,
        // we need to match based on username or name
        
        // Get user info
        $userSql = "SELECT full_name, username FROM users WHERE id = ?";
        $userStmt = Database::getInstance()->getConnection()->prepare($userSql);
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch();
        
        if (!$user) {
            return null;
        }
        
        // Strategy 1: Try exact full name match
        $doctorSql = "SELECT id FROM doctors WHERE name = ? LIMIT 1";
        $doctorStmt = Database::getInstance()->getConnection()->prepare($doctorSql);
        $doctorStmt->execute([$user['full_name']]);
        $doctor = $doctorStmt->fetch();
        
        if ($doctor) {
            return $doctor['id'];
        }
        
        // Strategy 2: Extract username pattern (e.g., dr.andi -> Andi)
        // Remove 'dr.' prefix and capitalize
        $username = strtolower($user['username']);
        if (strpos($username, 'dr.') === 0) {
            $doctorName = ucfirst(substr($username, 3)); // Remove 'dr.' and capitalize
            
            // Try to find doctor with this name pattern
            $doctorSql = "SELECT id FROM doctors WHERE name LIKE ? LIMIT 1";
            $doctorStmt = Database::getInstance()->getConnection()->prepare($doctorSql);
            $doctorStmt->execute(['%' . $doctorName . '%']);
            $doctor = $doctorStmt->fetch();
            
            if ($doctor) {
                return $doctor['id'];
            }
        }
        
        // Strategy 3: Try partial match with any word from full name (min 3 chars)
        $nameParts = explode(' ', trim($user['full_name']));
        foreach ($nameParts as $namePart) {
            $namePart = trim($namePart);
            if (strlen($namePart) > 2 && strtolower($namePart) !== 'dr') {
                $doctorSql = "SELECT id FROM doctors WHERE name LIKE ? LIMIT 1";
                $doctorStmt = Database::getInstance()->getConnection()->prepare($doctorSql);
                $doctorStmt->execute(['%' . $namePart . '%']);
                $doctor = $doctorStmt->fetch();
                
                if ($doctor) {
                    return $doctor['id'];
                }
            }
        }
        
        // NO FALLBACK - Return null if no match found
        // This will trigger the error message in doctorIndex()
        return null;
    }
    
    /**
     * Helper: Get doctors list
     */
    private function getDoctorsList()
    {
        // Get doctors from users table (only users with role='doctor')
        // Return doctor_id from doctors table for appointment insertion
        // If user doesn't have doctor record, we create a virtual entry with user's info
        $sql = "SELECT 
                    COALESCE(d.id, u.id) as id,
                    u.full_name as name,
                    COALESCE(d.specialization, 'General') as specialization,
                    u.username,
                    u.email
                FROM users u
                LEFT JOIN doctors d ON d.name LIKE CONCAT('%', SUBSTRING_INDEX(u.full_name, ' ', -1), '%')
                WHERE u.role = 'doctor'
                  AND u.is_active = TRUE
                GROUP BY u.id
                ORDER BY u.full_name ASC";
        
        $stmt = Database::getInstance()->getConnection()->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Helper: Get patients list
     */
    private function getPatientsList()
    {
        $sql = "SELECT id, name, phone, address 
                FROM patients 
                ORDER BY name ASC";
        
        $stmt = Database::getInstance()->getConnection()->query($sql);
        return $stmt->fetchAll();
    }
}
