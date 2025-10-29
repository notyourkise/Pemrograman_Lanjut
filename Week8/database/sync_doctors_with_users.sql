-- =====================================================
-- SYNC DOCTORS WITH USERS
-- =====================================================
-- Purpose: Ensure every doctor in doctors table has a corresponding user account
-- This script will create user accounts for doctors who don't have one yet
-- 
-- Default password for all doctor accounts: "password"
-- Password hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- =====================================================

-- STEP 1: Check which doctors don't have user accounts
SELECT 
    d.id AS doctor_id,
    d.name AS doctor_name,
    d.email AS doctor_email,
    d.user_id,
    u.username,
    u.full_name AS user_full_name,
    CASE 
        WHEN d.user_id IS NULL THEN '❌ NO USER ACCOUNT'
        WHEN u.id IS NULL THEN '⚠️ USER ID NOT FOUND'
        ELSE '✅ LINKED'
    END AS status
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
WHERE d.deleted_at IS NULL
ORDER BY d.id;

-- =====================================================
-- STEP 2: Create user accounts for doctors without one
-- =====================================================

-- Example: Create user for Dr. Budi (if exists in doctors table)
-- First check if Dr. Budi exists:
SELECT * FROM doctors WHERE name LIKE '%Budi%' AND deleted_at IS NULL;

-- If Dr. Budi exists but has no user_id, create user account:
INSERT INTO users (username, email, password, full_name, role, is_active)
SELECT 
    'dr.budi' AS username,
    d.email AS email,
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' AS password,
    d.name AS full_name,
    'doctor' AS role,
    TRUE AS is_active
FROM doctors d
WHERE d.name LIKE '%Budi%' 
  AND d.deleted_at IS NULL
  AND d.user_id IS NULL
  AND NOT EXISTS (SELECT 1 FROM users WHERE username = 'dr.budi')
LIMIT 1;

-- Link the newly created user to the doctor:
UPDATE doctors d
SET user_id = (SELECT id FROM users WHERE username = 'dr.budi' LIMIT 1)
WHERE d.name LIKE '%Budi%' 
  AND d.deleted_at IS NULL
  AND d.user_id IS NULL;

-- =====================================================
-- STEP 3: Generic template for creating more doctor users
-- =====================================================

-- For each doctor without user_id, you can use this template:
-- 
-- 1. INSERT user account:
-- INSERT INTO users (username, email, password, full_name, role, is_active)
-- VALUES ('dr.username', 'doctor@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Full Name', 'doctor', TRUE);
--
-- 2. UPDATE doctor with new user_id:
-- UPDATE doctors SET user_id = LAST_INSERT_ID() WHERE id = [doctor_id];

-- =====================================================
-- STEP 4: Verify all doctors are now linked
-- =====================================================

SELECT 
    d.id AS doctor_id,
    d.name AS doctor_name,
    d.user_id,
    u.username,
    u.role,
    u.is_active,
    CASE 
        WHEN d.user_id IS NOT NULL AND u.id IS NOT NULL THEN '✅ SYNCED'
        ELSE '❌ NOT SYNCED'
    END AS sync_status
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
WHERE d.deleted_at IS NULL
ORDER BY d.id;

-- =====================================================
-- BONUS: Auto-generate usernames from doctor names
-- =====================================================
-- Use this query to see suggested usernames for doctors without users:

SELECT 
    d.id,
    d.name,
    CONCAT('dr.', 
           LOWER(
               REPLACE(
                   TRIM(
                       SUBSTRING_INDEX(d.name, ' ', -1)
                   ), 
                   '.', 
                   ''
               )
           )
    ) AS suggested_username,
    d.email,
    d.specialization
FROM doctors d
WHERE d.deleted_at IS NULL
  AND d.user_id IS NULL;

-- =====================================================
-- EXAMPLE: Create accounts for multiple doctors at once
-- =====================================================

-- Doctor 1: Dr. Budi Santoso
INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES ('dr.budi', 'dr.budi@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Santoso', 'doctor', TRUE);
UPDATE doctors SET user_id = LAST_INSERT_ID() WHERE name = 'Dr. Budi Santoso' AND deleted_at IS NULL;

-- Doctor 2: Dr. Ani Wijaya
-- INSERT INTO users (username, email, password, full_name, role, is_active)
-- VALUES ('dr.ani', 'dr.ani@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Ani Wijaya', 'doctor', TRUE);
-- UPDATE doctors SET user_id = LAST_INSERT_ID() WHERE name = 'Dr. Ani Wijaya' AND deleted_at IS NULL;

-- Doctor 3: Dr. Citra Dewi
-- INSERT INTO users (username, email, password, full_name, role, is_active)
-- VALUES ('dr.citra', 'dr.citra@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Citra Dewi', 'doctor', TRUE);
-- UPDATE doctors SET user_id = LAST_INSERT_ID() WHERE name = 'Dr. Citra Dewi' AND deleted_at IS NULL;

-- =====================================================
-- IMPORTANT NOTES:
-- =====================================================
-- 1. All doctor accounts use password: "password"
-- 2. Username format: dr.[lastname] (lowercase, no spaces)
-- 3. Role must be: 'doctor'
-- 4. is_active should be TRUE
-- 5. Run STEP 1 first to check which doctors need accounts
-- 6. Run STEP 2-3 to create missing accounts
-- 7. Run STEP 4 to verify synchronization
-- =====================================================
