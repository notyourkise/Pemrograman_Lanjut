-- Week 8: Check and fix doctor-user relationships
-- Run this to see which doctors don't have user_id

-- Check all doctors and their user relationships
SELECT 
    d.id as doctor_id,
    d.name as doctor_name,
    d.user_id,
    u.username,
    u.full_name as user_full_name,
    u.role
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
WHERE d.deleted_at IS NULL;

-- If Dr. Budi exists without user_id, you need to:
-- 1. Create a user account for Dr. Budi, OR
-- 2. Link existing Dr. Budi to an existing user

-- Example: Create user for Dr. Budi (if doctor exists but no user)
-- INSERT INTO users (username, email, password, full_name, role, is_active) 
-- VALUES 
-- ('dr.budi', 'dr.budi@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Prakoso', 'doctor', TRUE);

-- Example: Link Dr. Budi to newly created user (get the user ID from previous INSERT)
-- UPDATE doctors SET user_id = LAST_INSERT_ID() WHERE name = 'Dr. Budi' OR name LIKE '%Budi%';

-- OR if you know the doctor name, update directly:
-- UPDATE doctors d
-- SET d.user_id = (SELECT id FROM users WHERE username = 'dr.budi' LIMIT 1)
-- WHERE d.name LIKE '%Budi%' AND d.deleted_at IS NULL;
