<?php

/**
 * Week 8: Appointment Model
 * Domain model for appointments with workflow states
 */

class Appointment
{
    private $id;
    private $doctorId;
    private $patientId;
    private $schedule;
    private $notes;
    private $status;
    private $createdBy;
    private $cancelledBy;
    private $cancelReason;
    private $cancelReviewedBy;
    private $cancelReviewedAt;
    private $cancelReviewStatus;
    private $createdAt;
    private $updatedAt;
    private $deletedAt;
    
    // Display names (from JOIN queries)
    private $doctorName;
    private $patientName;
    private $createdByName;
    private $cancelReviewedByName;

    // Workflow status constants
    const STATUS_PENDING_DOCTOR = 'pending_doctor';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PENDING_CANCEL = 'pending_cancel';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->doctorId = $data['doctor_id'];
        $this->patientId = $data['patient_id'];
        $this->schedule = $data['schedule'];
        $this->notes = $data['notes'] ?? null;
        $this->status = $data['status'] ?? self::STATUS_PENDING_DOCTOR;
        $this->createdBy = $data['created_by'] ?? null;
        $this->cancelledBy = $data['cancelled_by'] ?? null;
        $this->cancelReason = $data['cancel_reason'] ?? null;
        $this->cancelReviewedBy = $data['cancel_reviewed_by'] ?? null;
        $this->cancelReviewedAt = $data['cancel_reviewed_at'] ?? null;
        $this->cancelReviewStatus = $data['cancel_review_status'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->deletedAt = $data['deleted_at'] ?? null;
        
        // Display names from JOIN
        $this->doctorName = $data['doctor_name_display'] ?? $data['doctor_name'] ?? null;
        $this->patientName = $data['patient_name_display'] ?? $data['patient_name'] ?? null;
        $this->createdByName = $data['created_by_name_display'] ?? $data['created_by_name'] ?? null;
        $this->cancelReviewedByName = $data['cancel_reviewed_by_name'] ?? null;
    }

    /**
     * Create instance from database row
     */
    public static function fromArray($data)
    {
        return new self($data);
    }

    // Getters
    public function getId() { return $this->id; }
    public function getDoctorId() { return $this->doctorId; }
    public function getPatientId() { return $this->patientId; }
    public function getSchedule() { return $this->schedule; }
    public function getNotes() { return $this->notes; }
    public function getStatus() { return $this->status; }
    public function getCreatedBy() { return $this->createdBy; }
    public function getCancelledBy() { return $this->cancelledBy; }
    public function getCancelReason() { return $this->cancelReason; }
    public function getCancelReviewedBy() { return $this->cancelReviewedBy; }
    public function getCancelReviewedAt() { return $this->cancelReviewedAt; }
    public function getCancelReviewStatus() { return $this->cancelReviewStatus; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getDeletedAt() { return $this->deletedAt; }
    
    // Display name getters
    public function getDoctorName() { return $this->doctorName; }
    public function getPatientName() { return $this->patientName; }
    public function getCreatedByName() { return $this->createdByName; }
    public function getCancelReviewedByName() { return $this->cancelReviewedByName; }
    
    /**
     * Check if cancellation was reviewed
     */
    public function hasCancelReview()
    {
        return $this->cancelReviewStatus !== null;
    }
    
    /**
     * Check if cancellation was approved
     */
    public function isCancelApproved()
    {
        return $this->cancelReviewStatus === 'approved';
    }
    
    /**
     * Check if cancellation was rejected
     */
    public function isCancelRejected()
    {
        return $this->cancelReviewStatus === 'rejected';
    }
    
    /**
     * Get cancel review status label
     */
    public function getCancelReviewLabel()
    {
        if (!$this->hasCancelReview()) {
            return 'Pending Review';
        }
        
        return $this->isCancelApproved() ? 'Approved' : 'Rejected';
    }
    
    /**
     * Get formatted cancel reviewed date
     */
    public function getFormattedCancelReviewedAt($format = 'M d, Y H:i')
    {
        if (!$this->cancelReviewedAt) {
            return '-';
        }
        return date($format, strtotime($this->cancelReviewedAt));
    }

    // Status helpers
    public function isPendingDoctor()
    {
        return $this->status === self::STATUS_PENDING_DOCTOR;
    }

    public function isScheduled()
    {
        return $this->status === self::STATUS_SCHEDULED;
    }
    
    public function isInProgress()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isPendingCancel()
    {
        return $this->status === self::STATUS_PENDING_CANCEL;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get status label with color
     */
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_PENDING_DOCTOR => 'Pending Doctor Approval',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_PENDING_CANCEL => 'Pending Cancellation',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            self::STATUS_PENDING_DOCTOR => 'warning',
            self::STATUS_SCHEDULED => 'success',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_PENDING_CANCEL => 'warning',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_COMPLETED => 'info'
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Format schedule date
     */
    public function getFormattedSchedule($format = 'M d, Y H:i')
    {
        return date($format, strtotime($this->schedule));
    }

    /**
     * Check if appointment is in the past
     */
    public function isPast()
    {
        return strtotime($this->schedule) < time();
    }

    /**
     * Check if appointment is today
     */
    public function isToday()
    {
        return date('Y-m-d', strtotime($this->schedule)) === date('Y-m-d');
    }

    /**
     * Check if doctor can approve this appointment
     */
    public function canDoctorApprove()
    {
        return $this->isPendingDoctor() && !$this->isPast();
    }

    /**
     * Check if doctor can request cancellation
     */
    public function canDoctorRequestCancel()
    {
        return $this->isScheduled() && !$this->isPast();
    }

    /**
     * Check if receptionist can approve cancellation
     */
    public function canReceptionistApproveCancel()
    {
        return $this->isPendingCancel();
    }
    
    /**
     * Check if doctor can update status (start/complete)
     */
    public function canDoctorUpdateStatus()
    {
        return ($this->isScheduled() || $this->isInProgress()) && !$this->isPast();
    }
    
    /**
     * Check if appointment can be started (change to in_progress)
     */
    public function canBeStarted()
    {
        return $this->isScheduled() && $this->isToday();
    }
    
    /**
     * Check if appointment can be completed
     */
    public function canBeCompleted()
    {
        return $this->isInProgress() || ($this->isScheduled() && !$this->isPast());
    }
}
