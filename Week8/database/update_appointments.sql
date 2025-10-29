-- Week 8: Update Appointments Table for Workflow
-- Add status column and other necessary fields

-- Add status column with workflow states
ALTER TABLE appointments 
ADD COLUMN status ENUM(
    'pending_doctor',     -- Waiting for doctor approval
    'scheduled',          -- Approved by doctor
    'pending_cancel',     -- Doctor requested cancellation
    'cancelled',          -- Cancelled (by doctor/receptionist)
    'completed'           -- Appointment completed
) NOT NULL DEFAULT 'pending_doctor' AFTER notes;

-- Add created_by (receptionist who created the appointment)
ALTER TABLE appointments 
ADD COLUMN created_by INT(11) NULL AFTER status,
ADD FOREIGN KEY (created_by) REFERENCES users(id);

-- Add cancelled_by (who requested/approved cancellation)
ALTER TABLE appointments 
ADD COLUMN cancelled_by INT(11) NULL AFTER created_by,
ADD FOREIGN KEY (cancelled_by) REFERENCES users(id);

-- Add cancel_reason
ALTER TABLE appointments 
ADD COLUMN cancel_reason TEXT NULL AFTER cancelled_by;

-- Add deleted_at for soft delete
ALTER TABLE appointments 
ADD COLUMN deleted_at DATETIME NULL AFTER updated_at;

-- Add index for status
ALTER TABLE appointments 
ADD INDEX idx_status (status);

-- Add index for deleted_at
ALTER TABLE appointments 
ADD INDEX idx_deleted_at (deleted_at);
