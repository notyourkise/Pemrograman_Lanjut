-- =====================================================
-- ADD SOFT DELETE SUPPORT TO DOCTORS TABLE
-- =====================================================
-- Purpose: Add deleted_at column for soft delete functionality
-- This is OPTIONAL - only run if you want soft delete feature
-- =====================================================

-- Check if column already exists
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'doctors' 
  AND COLUMN_NAME = 'deleted_at';

-- If column doesn't exist, add it
ALTER TABLE doctors 
ADD COLUMN deleted_at DATETIME NULL AFTER updated_at;

-- Add index for better query performance
ALTER TABLE doctors
ADD INDEX idx_deleted_at (deleted_at);

-- Verify column was added
DESCRIBE doctors;

-- =====================================================
-- USAGE AFTER ADDING COLUMN:
-- =====================================================

-- Soft delete a doctor (instead of DELETE)
-- UPDATE doctors SET deleted_at = NOW() WHERE id = [doctor_id];

-- Restore a soft-deleted doctor
-- UPDATE doctors SET deleted_at = NULL WHERE id = [doctor_id];

-- Query only active doctors (not deleted)
-- SELECT * FROM doctors WHERE deleted_at IS NULL;

-- Query only deleted doctors
-- SELECT * FROM doctors WHERE deleted_at IS NOT NULL;

-- =====================================================
-- NOTES:
-- =====================================================
-- 1. This is optional - system works fine without soft delete
-- 2. Soft delete allows you to "delete" records without losing data
-- 3. Useful for audit trail and data recovery
-- 4. Current system already works without this column
-- =====================================================
