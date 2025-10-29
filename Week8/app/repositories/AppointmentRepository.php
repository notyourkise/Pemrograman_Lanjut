<?php

/**
 * Week 8: Appointment Repository
 * Data access layer for appointments with workflow
 */

class AppointmentRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find appointment by ID
     */
    public function findById($id)
    {
        $sql = "SELECT a.*, 
                       d.name as doctor_name, 
                       p.name as patient_name
                FROM appointments a
                LEFT JOIN doctors d ON a.doctor_id = d.id
                LEFT JOIN patients p ON a.patient_id = p.id
                WHERE a.id = ? AND a.deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch()) {
            return Appointment::fromArray($row);
        }
        
        return null;
    }

    /**
     * Get appointments for doctor with optional status filter
     */
    public function findByDoctor($doctorId, $status = null)
    {
        $sql = "SELECT a.*, 
                       d.name as doctor_name, 
                       p.name as patient_name,
                       u.full_name as created_by_name,
                       r.full_name as cancel_reviewed_by_name
                FROM appointments a
                LEFT JOIN doctors d ON a.doctor_id = d.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.created_by = u.id
                LEFT JOIN users r ON a.cancel_reviewed_by = r.id
                WHERE a.doctor_id = ? AND a.deleted_at IS NULL";
        
        $params = [$doctorId];
        
        if ($status) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY 
                  CASE 
                    WHEN a.schedule >= NOW() THEN 0 
                    ELSE 1 
                  END,
                  a.schedule ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $appointments = [];
        while ($row = $stmt->fetch()) {
            $row['doctor_name_display'] = $row['doctor_name'];
            $row['patient_name_display'] = $row['patient_name'];
            $row['created_by_name_display'] = $row['created_by_name'];
            $appointments[] = Appointment::fromArray($row);
        }
        
        return $appointments;
    }

    /**
     * Get all appointments (for receptionist)
     */
    public function findAll($filters = [])
    {
        $sql = "SELECT a.*, 
                       d.name as doctor_name, 
                       p.name as patient_name,
                       u.full_name as created_by_name
                FROM appointments a
                LEFT JOIN doctors d ON a.doctor_id = d.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.deleted_at IS NULL";
        
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (isset($filters['doctor_id'])) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $filters['doctor_id'];
        }
        
        if (isset($filters['date'])) {
            $sql .= " AND DATE(a.schedule) = ?";
            $params[] = $filters['date'];
        }
        
        $sql .= " ORDER BY a.schedule ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $appointments = [];
        while ($row = $stmt->fetch()) {
            $row['doctor_name_display'] = $row['doctor_name'];
            $row['patient_name_display'] = $row['patient_name'];
            $row['created_by_name_display'] = $row['created_by_name'] ?? 'System';
            $appointments[] = Appointment::fromArray($row);
        }
        
        return $appointments;
    }

    /**
     * Create new appointment (by receptionist)
     */
    public function create($data)
    {
        $sql = "INSERT INTO appointments 
                (doctor_id, patient_id, schedule, notes, status, created_by) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['doctor_id'],
            $data['patient_id'],
            $data['schedule'],
            $data['notes'] ?? null,
            Appointment::STATUS_PENDING_DOCTOR,
            $data['created_by']
        ]);
        
        return $this->findById($this->db->lastInsertId());
    }

    /**
     * Doctor approves appointment
     */
    public function approveByDoctor($id)
    {
        $sql = "UPDATE appointments 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND status = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            Appointment::STATUS_SCHEDULED,
            $id,
            Appointment::STATUS_PENDING_DOCTOR
        ]);
    }

    /**
     * Doctor rejects appointment
     */
    public function rejectByDoctor($id, $reason = null)
    {
        $sql = "UPDATE appointments 
                SET status = ?, cancel_reason = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND status = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            Appointment::STATUS_CANCELLED,
            $reason,
            $id,
            Appointment::STATUS_PENDING_DOCTOR
        ]);
    }

    /**
     * Doctor requests cancellation
     */
    public function requestCancellation($appointmentId, $cancelledByUserId, $reason)
    {
        // First, get the appointment to check doctor_id
        $appointment = $this->findById($appointmentId);
        
        if (!$appointment) {
            return false;
        }
        
        $sql = "UPDATE appointments 
                SET status = ?, cancelled_by = ?, cancel_reason = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND status = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            Appointment::STATUS_PENDING_CANCEL,
            $cancelledByUserId,
            $reason,
            $appointmentId,
            Appointment::STATUS_SCHEDULED
        ]);
    }

    /**
     * Receptionist approves cancellation request
     */
    public function approveCancellation($id, $reviewedByUserId)
    {
        $sql = "UPDATE appointments 
                SET status = ?, 
                    cancel_reviewed_by = ?, 
                    cancel_reviewed_at = CURRENT_TIMESTAMP,
                    cancel_review_status = 'approved',
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND status = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            Appointment::STATUS_CANCELLED,
            $reviewedByUserId,
            $id,
            Appointment::STATUS_PENDING_CANCEL
        ]);
    }

    /**
     * Receptionist rejects cancellation request (back to scheduled)
     */
    public function rejectCancellation($id, $reviewedByUserId)
    {
        $sql = "UPDATE appointments 
                SET status = ?, 
                    cancelled_by = NULL, 
                    cancel_reason = NULL,
                    cancel_reviewed_by = ?, 
                    cancel_reviewed_at = CURRENT_TIMESTAMP,
                    cancel_review_status = 'rejected',
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND status = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            Appointment::STATUS_SCHEDULED,
            $reviewedByUserId,
            $id,
            Appointment::STATUS_PENDING_CANCEL
        ]);
    }

    /**
     * Get today's appointments for doctor
     */
    public function getTodayForDoctor($doctorId)
    {
        return $this->findByDoctor($doctorId, Appointment::STATUS_SCHEDULED);
    }

    /**
     * Count appointments by status for doctor
     */
    public function countByStatusForDoctor($doctorId)
    {
        $sql = "SELECT status, COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? AND deleted_at IS NULL 
                GROUP BY status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$doctorId]);
        
        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    }

    /**
     * Get paginated appointments by doctor with optional status filter
     */
    public function findByDoctorPaginated($doctorId, $status = null, $page = 1, $perPage = 5)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT a.*, 
                       d.name as doctor_name, 
                       p.name as patient_name,
                       u.full_name as created_by_name,
                       r.full_name as cancel_reviewed_by_name
                FROM appointments a
                LEFT JOIN doctors d ON a.doctor_id = d.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.created_by = u.id
                LEFT JOIN users r ON a.cancel_reviewed_by = r.id
                WHERE a.doctor_id = ? AND a.deleted_at IS NULL";
        
        $params = [$doctorId];
        
        if ($status) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY 
                  CASE 
                    WHEN a.schedule >= NOW() THEN 0 
                    ELSE 1 
                  END,
                  a.schedule ASC 
                  LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $appointments = [];
        while ($row = $stmt->fetch()) {
            $row['doctor_name_display'] = $row['doctor_name'];
            $row['patient_name_display'] = $row['patient_name'];
            $row['created_by_name_display'] = $row['created_by_name'];
            $appointments[] = Appointment::fromArray($row);
        }
        
        return $appointments;
    }

    /**
     * Count appointments by doctor with optional status filter
     */
    public function countByDoctor($doctorId, $status = null)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? AND deleted_at IS NULL";
        
        $params = [$doctorId];
        
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        
        return $row['count'];
    }

    /**
     * Get paginated appointments (for receptionist)
     */
    public function findAllPaginated($filters = [], $page = 1, $perPage = 5)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT a.*, 
                       d.name as doctor_name, 
                       p.name as patient_name,
                       u.full_name as created_by_name
                FROM appointments a
                LEFT JOIN doctors d ON a.doctor_id = d.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.deleted_at IS NULL";
        
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (isset($filters['doctor_id'])) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $filters['doctor_id'];
        }
        
        if (isset($filters['date'])) {
            $sql .= " AND DATE(a.schedule) = ?";
            $params[] = $filters['date'];
        }
        
        // Smart sorting: upcoming appointments first (sorted by nearest), past appointments last (sorted by oldest first)
        $sql .= " ORDER BY 
                  CASE WHEN a.schedule >= NOW() THEN 0 ELSE 1 END,
                  a.schedule ASC 
                  LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $appointments = [];
        while ($row = $stmt->fetch()) {
            $row['doctor_name_display'] = $row['doctor_name'];
            $row['patient_name_display'] = $row['patient_name'];
            $row['created_by_name_display'] = $row['created_by_name'] ?? 'System';
            $appointments[] = Appointment::fromArray($row);
        }
        
        return $appointments;
    }

    /**
     * Count all appointments with filters
     */
    public function countAll($filters = [])
    {
        $sql = "SELECT COUNT(*) as count 
                FROM appointments a
                WHERE a.deleted_at IS NULL";
        
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (isset($filters['doctor_id'])) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $filters['doctor_id'];
        }
        
        if (isset($filters['date'])) {
            $sql .= " AND DATE(a.schedule) = ?";
            $params[] = $filters['date'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        
        return $row['count'];
    }
    
    /**
     * Get reviewed cancellation requests for doctor (both approved and rejected)
     */
    public function getReviewedCancellationsForDoctor($doctorId, $page = 1, $perPage = 5)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT a.*, 
                       d.name as doctor_name, 
                       p.name as patient_name,
                       u.full_name as created_by_name,
                       r.full_name as cancel_reviewed_by_name
                FROM appointments a
                LEFT JOIN doctors d ON a.doctor_id = d.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.created_by = u.id
                LEFT JOIN users r ON a.cancel_reviewed_by = r.id
                WHERE a.doctor_id = ? 
                  AND a.deleted_at IS NULL
                  AND a.cancel_review_status IS NOT NULL
                ORDER BY a.cancel_reviewed_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$doctorId, $perPage, $offset]);
        
        $appointments = [];
        while ($row = $stmt->fetch()) {
            $row['doctor_name_display'] = $row['doctor_name'];
            $row['patient_name_display'] = $row['patient_name'];
            $row['created_by_name_display'] = $row['created_by_name'];
            $appointments[] = Appointment::fromArray($row);
        }
        
        return $appointments;
    }
    
    /**
     * Count reviewed cancellations for doctor
     */
    public function countReviewedCancellationsForDoctor($doctorId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? 
                  AND deleted_at IS NULL
                  AND cancel_review_status IS NOT NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$doctorId]);
        $row = $stmt->fetch();
        
        return $row['count'];
    }
    
    /**
     * Doctor: Update appointment status to in_progress
     */
    public function startAppointment($id)
    {
        $sql = "UPDATE appointments 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND (status = ? OR status = ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            Appointment::STATUS_IN_PROGRESS,
            $id,
            Appointment::STATUS_SCHEDULED,
            Appointment::STATUS_IN_PROGRESS
        ]);
    }
    
    /**
     * Doctor: Update appointment status to completed
     */
    public function completeAppointment($id)
    {
        $sql = "UPDATE appointments 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND (status = ? OR status = ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            Appointment::STATUS_COMPLETED,
            $id,
            Appointment::STATUS_IN_PROGRESS,
            Appointment::STATUS_SCHEDULED
        ]);
    }
}
