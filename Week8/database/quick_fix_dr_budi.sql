-- Quick Fix: Create Dr. Budi user and link to doctor record
-- Run this in phpMyAdmin

-- Step 1: Check if Dr. Budi exists in doctors table
SELECT * FROM doctors WHERE name LIKE '%Budi%' AND deleted_at IS NULL;

-- Step 2: Check if user 'dr.budi' already exists
SELECT * FROM users WHERE username = 'dr.budi';

-- Step 3: Create user account for Dr. Budi (if not exists)
-- Password: 'password' (same as other demo accounts)
INSERT INTO users (username, email, password, full_name, role, is_active) 
VALUES ('dr.budi', 'dr.budi@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Prakoso', 'doctor', TRUE);

-- Step 4: Link Dr. Budi doctor record to the user account
UPDATE doctors 
SET user_id = (SELECT id FROM users WHERE username = 'dr.budi' LIMIT 1)
WHERE name LIKE '%Budi%' AND deleted_at IS NULL;

-- Step 5: Verify the link
SELECT 
    d.id as doctor_id,
    d.name as doctor_name,
    d.user_id,
    u.username,
    u.full_name,
    u.role
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
WHERE d.name LIKE '%Budi%' AND d.deleted_at IS NULL;

-- If Dr. Budi doesn't exist in doctors table, create both user and doctor:
/*
-- Create user
INSERT INTO users (username, email, password, full_name, role, is_active) 
VALUES ('dr.budi', 'dr.budi@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Prakoso', 'doctor', TRUE);

-- Create doctor record
INSERT INTO doctors (user_id, name, specialization, phone, email, department_id)
VALUES (
    (SELECT id FROM users WHERE username = 'dr.budi' LIMIT 1),
    'Dr. Budi Prakoso',
    'General Practitioner',
    '081234567893',
    'dr.budi@hospital.com',
    1  -- Cardiology department (change as needed)
);
*/

-- After running this, you can login with:
-- Username: dr.budi
-- Password: password
