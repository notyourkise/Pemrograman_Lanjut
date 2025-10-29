-- Week 8: Seed Data for Appointment Workflow Testing
-- Creates sample appointments with different statuses

-- Clear existing appointments for clean testing
TRUNCATE TABLE appointments;

-- Get user IDs (assuming user id 1 = admin, user id 2 = doctor)
-- We'll use user id 1 (admin/receptionist) as created_by

-- 1. PENDING_DOCTOR: Appointments waiting for doctor approval (created by receptionist)
INSERT INTO appointments (doctor_id, patient_id, schedule, notes, status, created_by, created_at) VALUES
(1, 1, '2025-10-30 09:00:00', 'Regular checkup - Please review', 'pending_doctor', 1, NOW()),
(1, 55, '2025-10-30 10:30:00', 'Follow-up consultation', 'pending_doctor', 1, NOW()),
(1, 49, '2025-10-31 14:00:00', 'New patient initial consultation', 'pending_doctor', 1, NOW());

-- 2. SCHEDULED: Appointments already approved by doctor
INSERT INTO appointments (doctor_id, patient_id, schedule, notes, status, created_by, created_at) VALUES
(1, 51, '2025-10-29 15:00:00', 'Today appointment - Blood pressure check', 'scheduled', 1, '2025-10-25 10:00:00'),
(1, 1, '2025-10-30 11:00:00', 'Scheduled consultation', 'scheduled', 1, '2025-10-26 14:00:00'),
(1, 55, '2025-11-01 09:00:00', 'Next week appointment', 'scheduled', 1, '2025-10-27 09:00:00'),
(1, 49, '2025-11-05 10:00:00', 'Regular checkup scheduled', 'scheduled', 1, '2025-10-27 11:00:00');

-- 3. PENDING_CANCEL: Doctor requested cancellation, waiting for receptionist approval
INSERT INTO appointments (doctor_id, patient_id, schedule, notes, status, created_by, cancelled_by, cancel_reason, created_at) VALUES
(1, 51, '2025-11-02 13:00:00', 'Patient rescheduling request', 'pending_cancel', 1, 2, 'Patient requested to reschedule due to work conflict', '2025-10-27 10:00:00'),
(1, 1, '2025-11-03 16:00:00', 'Emergency schedule conflict', 'pending_cancel', 1, 2, 'Doctor has emergency surgery scheduled at same time', '2025-10-28 08:00:00');

-- 4. CANCELLED: Already cancelled appointments
INSERT INTO appointments (doctor_id, patient_id, schedule, notes, status, created_by, cancelled_by, cancel_reason, created_at) VALUES
(1, 55, '2025-10-28 10:00:00', 'Patient cancelled', 'cancelled', 1, 2, 'Patient no-show', '2025-10-20 10:00:00'),
(1, 49, '2025-10-27 14:00:00', 'Rejected by doctor', 'cancelled', 1, 2, 'Doctor not available at requested time', '2025-10-23 15:00:00');

-- 5. COMPLETED: Past completed appointments
INSERT INTO appointments (doctor_id, patient_id, schedule, notes, status, created_by, created_at) VALUES
(1, 1, '2025-10-25 09:00:00', 'Completed checkup', 'completed', 1, '2025-10-20 10:00:00'),
(1, 51, '2025-10-26 11:00:00', 'Completed consultation', 'completed', 1, '2025-10-21 14:00:00');

-- Also add some appointments for other doctors
INSERT INTO appointments (doctor_id, patient_id, schedule, notes, status, created_by, created_at) VALUES
(2, 55, '2025-10-30 09:00:00', 'Dr. Sari consultation', 'scheduled', 1, NOW()),
(3, 49, '2025-10-30 10:00:00', 'Dr. Budi checkup', 'pending_doctor', 1, NOW()),
(4, 51, '2025-10-31 11:00:00', 'Dr. Rina appointment', 'scheduled', 1, NOW());

-- Summary of seeded data:
-- Doctor ID 1 (Dr. Andi):
--   - 3 Pending Doctor Approval
--   - 4 Scheduled
--   - 2 Pending Cancellation
--   - 2 Cancelled
--   - 2 Completed
-- Total: 13 appointments for Dr. Andi (doctor user id 2)
-- Plus 3 appointments for other doctors
