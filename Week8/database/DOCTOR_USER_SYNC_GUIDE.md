# 🔄 DOCTOR-USER SYNCHRONIZATION GUIDE

## 📋 Konsep Dasar

Setiap dokter di tabel `doctors` **HARUS** punya user account di tabel `users` untuk bisa login ke sistem.

### Relationship Structure:

```
users table (login accounts)
    ↓ (one-to-one)
doctors table (doctor profiles)
```

### Database Schema:

- **users.id** → primary key
- **doctors.user_id** → foreign key ke users.id
- **Constraint**: `FOREIGN KEY (user_id) REFERENCES users(id)`

---

## ✅ Aturan Wajib

1. **Setiap dokter WAJIB punya `user_id`**
   - Tidak boleh NULL untuk dokter aktif
   - Satu dokter = satu user account
2. **User account untuk dokter WAJIB punya:**

   - `role` = 'doctor'
   - `is_active` = TRUE
   - Password yang valid (default: "password")

3. **Username convention:**

   - Format: `dr.[lastname]` (lowercase)
   - Contoh: Dr. Budi Santoso → username: `dr.budi`

4. **Email harus unik dan valid**
   - Format: `dr.username@hospital.com`

---

## 🚀 Cara Menambah Dokter Baru

### Step 1: Buat User Account dulu

```sql
INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES (
    'dr.budi',                                                           -- username
    'dr.budi@hospital.com',                                              -- email
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',    -- password hash untuk "password"
    'Dr. Budi Santoso',                                                  -- full name
    'doctor',                                                            -- role WAJIB 'doctor'
    TRUE                                                                 -- is_active
);
```

### Step 2: Catat user_id yang baru dibuat

```sql
SELECT id FROM users WHERE username = 'dr.budi';
-- Misal hasilnya: id = 6
```

### Step 3: Insert ke tabel doctors dengan user_id

```sql
INSERT INTO doctors (user_id, name, specialization, phone, email, department_id)
VALUES (
    6,                              -- user_id dari step 2
    'Dr. Budi Santoso',             -- nama dokter
    'Orthopedic Surgeon',           -- spesialisasi
    '081234567893',                 -- telepon
    'dr.budi@hospital.com',         -- email (sama dengan user)
    1                               -- department_id
);
```

### 🎯 Shortcut: Buat Sekaligus

```sql
-- Step 1: Insert user
INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES ('dr.budi', 'dr.budi@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Santoso', 'doctor', TRUE);

-- Step 2: Insert doctor menggunakan LAST_INSERT_ID()
INSERT INTO doctors (user_id, name, specialization, phone, email, department_id)
VALUES (LAST_INSERT_ID(), 'Dr. Budi Santoso', 'Orthopedic Surgeon', '081234567893', 'dr.budi@hospital.com', 1);
```

---

## 🔍 Cara Cek Sinkronisasi

### 1. Lihat semua dokter dan status user-nya:

```sql
SELECT
    d.id AS doctor_id,
    d.name AS doctor_name,
    d.user_id,
    u.username,
    u.role,
    u.is_active,
    CASE
        WHEN d.user_id IS NULL THEN '❌ NO USER ACCOUNT'
        WHEN u.id IS NULL THEN '⚠️ USER ID NOT FOUND'
        ELSE '✅ LINKED'
    END AS status
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
WHERE d.deleted_at IS NULL
ORDER BY d.id;
```

### 2. Cari dokter yang belum punya user:

```sql
SELECT * FROM doctors
WHERE user_id IS NULL
  AND deleted_at IS NULL;
```

### 3. Verify login credentials:

```sql
SELECT
    u.username,
    u.full_name,
    u.role,
    d.name AS doctor_name,
    d.specialization
FROM users u
INNER JOIN doctors d ON u.id = d.user_id
WHERE u.role = 'doctor'
  AND u.is_active = TRUE;
```

---

## 🛠️ Troubleshooting

### ❌ Problem: "Doctor profile not found" saat login

**Penyebab:** User account ada, tapi tidak ter-link ke tabel doctors

**Solusi 1:** Link existing doctor ke user

```sql
-- Cari user_id
SELECT id, username FROM users WHERE username = 'dr.budi';
-- Misal: id = 8

-- Update doctor dengan user_id tersebut
UPDATE doctors
SET user_id = 8
WHERE name = 'Dr. Budi Santoso'
  AND deleted_at IS NULL;
```

**Solusi 2:** Buat doctor record baru

```sql
-- Dapatkan user_id
SET @user_id = (SELECT id FROM users WHERE username = 'dr.budi');

-- Insert doctor baru
INSERT INTO doctors (user_id, name, specialization, phone, email, department_id)
VALUES (@user_id, 'Dr. Budi Santoso', 'Orthopedic Surgeon', '081234567893', 'dr.budi@hospital.com', 1);
```

### ❌ Problem: Doctor ada tapi gak bisa login

**Penyebab:** Doctor punya user_id tapi user tidak aktif atau tidak ada

**Cek:**

```sql
SELECT
    d.name,
    d.user_id,
    u.username,
    u.is_active,
    u.role
FROM doctors d
LEFT JOIN users u ON d.user_id = u.id
WHERE d.name LIKE '%Budi%';
```

**Fix:**

```sql
-- Activate user
UPDATE users SET is_active = TRUE WHERE id = [user_id];

-- Atau buat user baru kalau tidak ada
INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES ('dr.budi', 'dr.budi@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Santoso', 'doctor', TRUE);

UPDATE doctors SET user_id = LAST_INSERT_ID() WHERE name = 'Dr. Budi Santoso';
```

---

## 🔐 Default Credentials

Semua doctor accounts menggunakan password default yang sama:

```
Password: password
Password Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
```

### Existing Doctor Accounts:

| Username   | Password | Full Name         | Specialization     |
| ---------- | -------- | ----------------- | ------------------ |
| dr.john    | password | Dr. John Smith    | Cardiologist       |
| dr.sarah   | password | Dr. Sarah Johnson | Neurologist        |
| dr.michael | password | Dr. Michael Brown | Pediatrician       |
| dr.budi    | password | Dr. Budi Santoso  | Orthopedic Surgeon |

---

## 📝 Best Practices

### 1. Selalu buat user dulu, baru doctor

```sql
-- ✅ GOOD: Create user first
INSERT INTO users (...) VALUES (...);
INSERT INTO doctors (user_id, ...) VALUES (LAST_INSERT_ID(), ...);

-- ❌ BAD: Create doctor without user
INSERT INTO doctors (user_id, name, ...) VALUES (NULL, 'Dr. Someone', ...);
```

### 2. Gunakan LAST_INSERT_ID() untuk auto-link

```sql
-- Setelah INSERT INTO users
INSERT INTO doctors (user_id, ...) VALUES (LAST_INSERT_ID(), ...);
```

### 3. Selalu verify setelah menambah dokter baru

```sql
SELECT d.name, u.username, u.role
FROM doctors d
JOIN users u ON d.user_id = u.id
WHERE d.id = LAST_INSERT_ID();
```

### 4. Jangan hapus user kalau masih ada doctor yang link

```sql
-- ❌ BAD: Hapus user langsung
DELETE FROM users WHERE id = 6;

-- ✅ GOOD: Soft delete atau unlink dulu
UPDATE doctors SET deleted_at = NOW() WHERE user_id = 6;
-- Baru hapus user kalau perlu
```

### 5. Gunakan transaction untuk data integrity

```sql
START TRANSACTION;

INSERT INTO users (username, email, password, full_name, role, is_active)
VALUES ('dr.ani', 'dr.ani@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Ani Wijaya', 'doctor', TRUE);

INSERT INTO doctors (user_id, name, specialization, phone, email, department_id)
VALUES (LAST_INSERT_ID(), 'Dr. Ani Wijaya', 'Dermatologist', '081234567894', 'dr.ani@hospital.com', 2);

COMMIT;
```

---

## 🎓 Quick Reference

### Username Convention:

- Format: `dr.[lastname_lowercase]`
- Examples: `dr.budi`, `dr.ani`, `dr.citra`

### Email Convention:

- Format: `dr.[username]@hospital.com`
- Examples: `dr.budi@hospital.com`

### Password:

- Default: `password`
- Hash: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`

### Role:

- WAJIB: `doctor` (lowercase)

---

## 📦 Files yang Tersedia

1. **hospital.sql** - Database schema lengkap dengan 4 dokter ter-sync
2. **sync_doctors_with_users.sql** - Script untuk sinkronisasi dokter existing
3. **quick_fix_dr_budi.sql** - Quick fix untuk Dr. Budi spesifik
4. **fix_doctor_user_mapping.sql** - Diagnostic tool

---

## 🔄 Maintenance Checklist

- [ ] Setiap tambah dokter baru, buat user account-nya
- [ ] Verify user_id tidak NULL di tabel doctors
- [ ] Test login dengan credentials doctor baru
- [ ] Update password default setelah first login (recommended)
- [ ] Check synchronization status secara berkala

---

**Created:** October 29, 2025  
**System:** Hospital Management System Week 8  
**Author:** GitHub Copilot
