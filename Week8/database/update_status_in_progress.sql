-- Week 8: Add 'in_progress' status to appointments
-- Update status enum to include in_progress state

ALTER TABLE appointments 
MODIFY COLUMN status ENUM(
    'pending_doctor',
    'scheduled',
    'in_progress',
    'pending_cancel',
    'cancelled',
    'completed'
) NOT NULL DEFAULT 'pending_doctor';
