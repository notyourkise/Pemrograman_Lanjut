-- =====================================================
-- CREATE USER ACCOUNT FOR EXISTING DOCTOR
-- =====================================================
-- Purpose: Membuat user account untuk dokter yang sudah ada di tabel doctors
--          tapi belum punya user_id (belum bisa login)
-- 
-- Usage: Ganti 'dr.hasan' dengan username yang sesuai
-- =====================================================

-- STEP 1: CEK DOKTER YANG BELUM PUNYA USER
-- =====================================================
-- Lihat dokter mana saja yang belum ter-link ke user account

SELECT 
    d.id AS doctor_id,
    d.name AS doctor_name,
    d.specialization,
    d.email,
    d.user_id,
    CASE 
        WHEN d.user_id IS NULL THEN '❌ BELUM PUNYA USER'
        ELSE '✅ SUDAH PUNYA USER'
    END AS status
FROM doctors d
WHERE d.deleted_at IS NULL
ORDER BY d.id;

-- =====================================================
-- STEP 2: TEMPLATE - CREATE USER UNTUK 1 DOKTER
-- =====================================================
-- Ganti nilai-nilai sesuai dengan data dokter yang mau dibuat user-nya

-- Contoh untuk Dr. Hasan:
INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES (
    'dr.hasan',                                                          -- username (lowercase, no spaces)
    'dr.hasan@hospital.com',                                              -- email (unique)
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',    -- password hash untuk "password"
    'Dr. Hasan',                                                          -- full name (bisa pakai spasi & title)
    'doctor',                                                             -- role WAJIB 'doctor'
    TRUE                                                                  -- is_active = TRUE
);

-- Link user yang baru dibuat ke doctor record
UPDATE doctors
SET user_id = (SELECT id FROM users WHERE username = 'dr.hasan' LIMIT 1)
WHERE name LIKE '%Hasan%'                                                 -- Sesuaikan dengan nama dokter
  AND deleted_at IS NULL
  AND user_id IS NULL;                                                    -- Hanya update yang belum punya user_id

-- =====================================================
-- STEP 3: VERIFY - CEK APAKAH SUDAH TER-LINK
-- =====================================================

SELECT 
    d.id AS doctor_id,
    d.name AS doctor_name,
    d.user_id,
    u.id AS user_id_actual,
    u.username,
    u.role,
    u.is_active,
    CASE 
        WHEN d.user_id IS NOT NULL AND u.id IS NOT NULL THEN '✅ SUCCESS'
        ELSE '❌ FAILED'
    END AS result
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
WHERE d.name LIKE '%Hasan%'
  AND d.deleted_at IS NULL;

-- =====================================================
-- ALTERNATIF: BULK CREATE UNTUK BANYAK DOKTER
-- =====================================================
-- Jika ada banyak dokter yang belum punya user, gunakan cara ini:

-- Example 1: Dr. Hasan
INSERT INTO users (username, email, password, full_name, role, is_active)
SELECT 'dr.hasan', 'dr.hasan@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', d.name, 'doctor', TRUE
FROM doctors d
WHERE d.name LIKE '%Hasan%' AND d.deleted_at IS NULL AND d.user_id IS NULL
  AND NOT EXISTS (SELECT 1 FROM users WHERE username = 'dr.hasan')
LIMIT 1;

UPDATE doctors SET user_id = (SELECT id FROM users WHERE username = 'dr.hasan') 
WHERE name LIKE '%Hasan%' AND deleted_at IS NULL AND user_id IS NULL;

-- Example 2: Dr. Ahmad
-- INSERT INTO users (username, email, password, full_name, role, is_active)
-- SELECT 'dr.ahmad', 'dr.ahmad@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', d.name, 'doctor', TRUE
-- FROM doctors d
-- WHERE d.name LIKE '%Ahmad%' AND d.deleted_at IS NULL AND d.user_id IS NULL
--   AND NOT EXISTS (SELECT 1 FROM users WHERE username = 'dr.ahmad')
-- LIMIT 1;

-- UPDATE doctors SET user_id = (SELECT id FROM users WHERE username = 'dr.ahmad') 
-- WHERE name LIKE '%Ahmad%' AND deleted_at IS NULL AND user_id IS NULL;

-- Example 3: Dr. Siti
-- INSERT INTO users (username, email, password, full_name, role, is_active)
-- SELECT 'dr.siti', 'dr.siti@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', d.name, 'doctor', TRUE
-- FROM doctors d
-- WHERE d.name LIKE '%Siti%' AND d.deleted_at IS NULL AND d.user_id IS NULL
--   AND NOT EXISTS (SELECT 1 FROM users WHERE username = 'dr.siti')
-- LIMIT 1;

-- UPDATE doctors SET user_id = (SELECT id FROM users WHERE username = 'dr.siti') 
-- WHERE name LIKE '%Siti%' AND deleted_at IS NULL AND user_id IS NULL;

-- =====================================================
-- STEP 4: FINAL CHECK - SEMUA DOKTER AKTIF
-- =====================================================

SELECT 
    COUNT(*) AS total_doctors,
    SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) AS doctors_with_user,
    SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) AS doctors_without_user
FROM doctors
WHERE deleted_at IS NULL;

-- Detail dokter yang belum punya user (kalau masih ada)
SELECT 
    d.id,
    d.name,
    d.specialization,
    d.email,
    'Create user with username: ' || LOWER(CONCAT('dr.', SUBSTRING_INDEX(d.name, ' ', -1))) AS suggestion
FROM doctors d
WHERE d.deleted_at IS NULL
  AND d.user_id IS NULL;

-- =====================================================
-- NOTES:
-- =====================================================
-- 1. Password default semua dokter: "password"
-- 2. Password hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- 3. Username format: dr.[lastname] (lowercase, no spaces)
-- 4. Role WAJIB: 'doctor'
-- 5. Email format: dr.username@hospital.com
-- 6. Setelah create, WAJIB verify dengan STEP 3
-- 7. Login credentials: username = dr.hasan, password = password
-- =====================================================
