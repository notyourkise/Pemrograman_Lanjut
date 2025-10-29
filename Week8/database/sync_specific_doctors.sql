-- =====================================================
-- SYNC SPECIFIC DOCTORS WITH USERS
-- =====================================================
-- Purpose: Create proper mapping between existing users and doctors
-- Run this to fix the issue where all doctors show the same appointments
-- =====================================================

-- STEP 1: Check current users with role='doctor'
SELECT id, username, full_name, role 
FROM users 
WHERE role = 'doctor'
ORDER BY id;

-- STEP 2: Check current doctors
SELECT id, name, specialization 
FROM doctors
ORDER BY id;

-- =====================================================
-- STEP 3: CREATE MAPPING BASED ON YOUR DATA
-- =====================================================
-- Adjust these based on your actual data from STEP 1 and STEP 2

-- Example: If you have these users and doctors:
-- Users: dr.andi (id=8), dr.sari (id=9)
-- Doctors: Dr. Andi (id=1), Dr. Sari (id=2)

-- Option A: If your doctors table has user_id column
-- UPDATE doctors SET user_id = 8 WHERE name LIKE '%Andi%';
-- UPDATE doctors SET user_id = 9 WHERE name LIKE '%Sari%';

-- Option B: If your doctors table doesn't have user_id column
-- You need to add it first (see add_user_id_to_doctors.sql)
-- Then run the UPDATE statements above

-- =====================================================
-- TEMPORARY FIX: Rename doctors to match usernames
-- =====================================================
-- This makes the automatic name-based matching work

-- Get list of doctor users
SELECT 
    u.id AS user_id,
    u.username,
    u.full_name,
    d.id AS doctor_id,
    d.name AS current_doctor_name,
    CONCAT('Dr. ', UPPER(SUBSTRING(SUBSTRING_INDEX(u.username, '.', -1), 1, 1)), 
           LOWER(SUBSTRING(SUBSTRING_INDEX(u.username, '.', -1), 2))) AS suggested_name
FROM users u
LEFT JOIN doctors d ON d.name LIKE CONCAT('%', SUBSTRING_INDEX(u.username, '.', -1), '%')
WHERE u.role = 'doctor';

-- Example updates (adjust based on your data):
-- UPDATE doctors SET name = 'Dr. Andi' WHERE id = 1;
-- UPDATE doctors SET name = 'Dr. Sari' WHERE id = 2;
-- UPDATE doctors SET name = 'Dr. Budi' WHERE id = 3;
-- UPDATE doctors SET name = 'Dr. John' WHERE id = 4;
-- UPDATE doctors SET name = 'Dr. Sarah' WHERE id = 5;

-- =====================================================
-- VERIFICATION
-- =====================================================
-- After updating, verify the mapping works:

SELECT 
    u.id AS user_id,
    u.username,
    u.full_name,
    d.id AS doctor_id,
    d.name AS doctor_name,
    CASE 
        WHEN d.id IS NOT NULL THEN '✅ MAPPED'
        ELSE '❌ NOT MAPPED'
    END AS status
FROM users u
LEFT JOIN doctors d ON (
    d.name = u.full_name OR
    d.name LIKE CONCAT('%', SUBSTRING_INDEX(u.username, '.', -1), '%')
)
WHERE u.role = 'doctor'
ORDER BY u.id;

-- =====================================================
-- QUICK FIX FOR SPECIFIC DOCTORS
-- =====================================================

-- Find user IDs
-- SELECT id, username, full_name FROM users WHERE username IN ('dr.andi', 'dr.sari', 'dr.budi');

-- Find doctor IDs  
-- SELECT id, name FROM doctors WHERE name LIKE '%Andi%' OR name LIKE '%Sari%' OR name LIKE '%Budi%';

-- Manual mapping (replace IDs with actual values from your database):
-- User dr.andi (user_id = ?) should map to Doctor with 'Andi' in name (doctor_id = ?)
-- User dr.sari (user_id = ?) should map to Doctor with 'Sari' in name (doctor_id = ?)

-- To see appointments per doctor:
SELECT 
    d.id AS doctor_id,
    d.name AS doctor_name,
    COUNT(a.id) AS appointment_count
FROM doctors d
LEFT JOIN appointments a ON a.doctor_id = d.id
GROUP BY d.id, d.name
ORDER BY d.id;

-- =====================================================
-- NOTES:
-- =====================================================
-- 1. The current system matches doctors by name
-- 2. For proper fix, add user_id column to doctors table
-- 3. Or ensure doctor names match user full_names exactly
-- 4. Without proper mapping, all doctors will see same data
-- =====================================================
