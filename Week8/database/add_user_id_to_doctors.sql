-- =====================================================
-- ADD user_id COLUMN TO DOCTORS TABLE
-- =====================================================
-- Purpose: Link doctors to users for login functionality
-- This allows each doctor to have their own user account
-- =====================================================

-- Step 1: Check current structure of doctors table
DESCRIBE doctors;

-- Step 2: Add user_id column if it doesn't exist
ALTER TABLE doctors
ADD COLUMN user_id INT NULL AFTER id;

-- Step 3: Add foreign key constraint to users table
ALTER TABLE doctors
ADD CONSTRAINT fk_doctors_users 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Step 4: Add index for better query performance
ALTER TABLE doctors
ADD INDEX idx_user_id (user_id);

-- Step 5: Verify column was added
DESCRIBE doctors;

-- Step 6: Check if there are existing doctors
SELECT id, name, specialization, user_id FROM doctors;

-- =====================================================
-- NEXT STEPS AFTER ADDING COLUMN:
-- =====================================================
-- 1. Create user accounts for existing doctors using:
--    database/create_user_for_existing_doctor.sql
--
-- 2. Link doctors to users using:
--    database/sync_doctors_with_users.sql
--
-- 3. Update AppointmentsController::getDoctorsList() to filter by user_id
-- =====================================================

-- Example: Link existing doctor to user
-- UPDATE doctors SET user_id = 3 WHERE id = 1;  -- Link doctor 1 to user 3
-- UPDATE doctors SET user_id = 4 WHERE id = 2;  -- Link doctor 2 to user 4

-- =====================================================
-- ROLLBACK (if needed):
-- =====================================================
-- Remove foreign key
-- ALTER TABLE doctors DROP FOREIGN KEY fk_doctors_users;
-- 
-- Remove index
-- ALTER TABLE doctors DROP INDEX idx_user_id;
-- 
-- Remove column
-- ALTER TABLE doctors DROP COLUMN user_id;
-- =====================================================
