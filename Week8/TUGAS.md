# TUGAS WEEK 8: Authentication & Authorization

## 📋 Overview

**Tujuan:** Menambahkan fitur authentication dan role-based authorization ke dalam Hospital Management System dengan menerapkan konsep OOP yang sudah dipelajari.

**Deadline:** 1 minggu dari praktikum

**Bobot:** 100 points

**Mode:** Individu

---

## 🎯 Objectives

Mahasiswa akan:

1. Mengimplementasikan sistem login/logout
2. Membuat role-based dashboard berbeda untuk setiap role
3. Menambahkan authorization checks ke existing controllers
4. Implementasi audit logging untuk track user actions

---

## 📝 Requirements

### 1. Authentication System (25 points)

**Tasks:**

✅ **Login Page** (8 points)

- Form login dengan username/email dan password
- Validasi input (tidak boleh kosong)
- Flash message untuk error/success
- Remember me checkbox (optional bonus)

✅ **Logout Functionality** (5 points)

- Logout button di navbar
- Clear session
- Redirect ke login page
- Flash message "Logged out successfully"

✅ **Registration Page** (12 points)

- Form register dengan: username, email, full_name, password, password_confirmation
- Validasi:
  - Username minimal 3 karakter
  - Email valid format
  - Password minimal 8 karakter
  - Password confirmation harus match
  - Username dan email unique
- Auto-assign role 'receptionist' untuk user baru
- Auto-login setelah registrasi berhasil

**Contoh Output:**

```
Login successful! Welcome back, Dr. John Smith
```

---

### 2. Role-Based Dashboard (20 points)

**Tasks:**

Buat dashboard yang berbeda untuk setiap role:

✅ **Admin Dashboard** (7 points)

- Statistics: Total users by role
- Quick actions: Create user, view audit logs
- User management table (list 10 latest users)

✅ **Doctor Dashboard** (7 points)

- Statistics: Total appointments (my appointments only)
- Quick actions: View my schedule
- Today's appointments table

✅ **Receptionist Dashboard** (6 points)

- Statistics: Total patients, total appointments
- Quick actions: Create appointment, create patient
- Latest appointments table

**Contoh:**

```
Admin melihat:
- Total Users: Admin (2), Doctor (5), Receptionist (10)
- Can manage all users

Doctor melihat:
- My Appointments Today: 5
- Next appointment: 09:00 - Ahmad Fauzi

Receptionist melihat:
- Total Patients: 150
- Latest appointment: Dr. John - 10:30
```

---

### 3. Authorization Implementation (30 points)

**Tasks:**

Tambahkan authorization checks ke existing controllers:

✅ **PatientsController** (10 points)

- `index()` - Require permission: `patients.view`
- `create()` - Require permission: `patients.create`
- `update()` - Require permission: `patients.update`
- `delete()` - Require role: `admin` only

✅ **AppointmentsController** (10 points)

- `index()` - Require permission: `appointments.view`
- `create()` - Require permission: `appointments.create`
- `update()` - Require permission: `appointments.update`
- `delete()` - Require role: `admin` only

✅ **View Protection** (10 points)

- Hide "Delete" button jika bukan admin
- Hide "Add User" button jika bukan admin
- Show menu items sesuai permission
- Display current user info di navbar

**Contoh Implementation:**

```php
// PatientsController.php
public function delete($id)
{
    // ✅ Only admin can delete
    $this->middleware->requireAdmin();

    // Delete logic...
}
```

```php
<!-- patients/index.php -->
<?php if ($auth->hasRole('admin')): ?>
    <a href="/patients/delete/<?= $patient['id'] ?>" class="btn btn-danger">Delete</a>
<?php endif; ?>
```

---

### 4. Audit Logging (25 points)

**Tasks:**

Buat sistem untuk track user actions:

✅ **AuditLog Model & Repository** (10 points)

- Create `AuditLog` model dengan properties:

  - user_id
  - action (CREATE, UPDATE, DELETE, LOGIN, LOGOUT)
  - table_name
  - record_id
  - old_values (JSON)
  - new_values (JSON)
  - ip_address
  - user_agent
  - created_at

- Create `AuditLogRepository` dengan methods:
  - `log($userId, $action, $tableName, $recordId, $oldValues, $newValues)`
  - `getByUser($userId)`
  - `getByAction($action)`
  - `getRecent($limit = 50)`

✅ **Implement Logging** (10 points)

- Log user LOGIN/LOGOUT
- Log patient CREATE/UPDATE/DELETE
- Log appointment CREATE/UPDATE/DELETE
- Log user CREATE/UPDATE/DELETE (admin only)

✅ **Audit Logs View** (5 points)

- Page `/audit-logs` (admin only)
- Table dengan kolom: timestamp, user, action, table, details
- Filter by user/action/date
- Pagination (10 records per page)

**Contoh Log Entry:**

```
2025-10-28 10:30:15 | admin | CREATE | patients | ID:101 |
Created patient: Ahmad Fauzi (081234567890)
```

---

## 🎨 Bonus Features (20 points extra)

Implementasikan salah satu atau lebih:

### Bonus 1: Password Change (5 points)

- User bisa ganti password di profile page
- Validasi old password
- New password minimal 8 karakter
- Confirmation harus match

### Bonus 2: User Profile Page (5 points)

- Show user details
- Edit full name, email
- Upload profile picture
- Show last login, registration date

### Bonus 3: Session Timeout (5 points)

- Auto-logout setelah 30 menit inactive
- Show warning 5 menit sebelum timeout
- Extend session on activity

### Bonus 4: Email Verification (5 points)

- Send verification email saat register
- User harus verify email sebelum bisa login
- Resend verification link

---

## 📦 Deliverables

Submit ke GitHub dengan struktur:

```
Week8-NPM/
├── database/
│   └── hospital.sql (with sample data)
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── PatientsController.php (updated)
│   │   └── AppointmentsController.php (updated)
│   ├── models/
│   │   ├── User.php
│   │   └── AuditLog.php
│   ├── repositories/
│   │   ├── UserRepository.php
│   │   └── AuditLogRepository.php
│   ├── helpers/
│   │   └── Auth.php
│   ├── middleware/
│   │   └── AuthMiddleware.php
│   └── views/
│       ├── auth/ (login, register)
│       ├── dashboard/ (index)
│       └── audit-logs/ (index)
├── public/
│   └── index.php (updated routing)
└── README.md (dokumentasi)
```

**README.md harus berisi:**

- Cara install & setup
- Credentials untuk testing
- Screenshot fitur utama
- Penjelasan design patterns yang digunakan
- Bonus features (jika ada)

---

## ✅ Grading Rubric

| Kriteria                 | Points | Deskripsi                                      |
| ------------------------ | ------ | ---------------------------------------------- |
| **Authentication**       | 25     | Login, logout, register working                |
| **Role-Based Dashboard** | 20     | Different dashboard per role                   |
| **Authorization**        | 30     | Middleware, permission checks, view protection |
| **Audit Logging**        | 25     | Log creation, storage, viewing                 |
| **Code Quality**         | 10     | Clean code, OOP principles, comments           |
| **Documentation**        | 10     | README, screenshots, clear instructions        |
| **Bonus**                | +20    | Extra features implemented                     |
| **Total**                | 120    | (100 + 20 bonus)                               |

---

## 🔍 Checklist

Sebelum submit, pastikan:

- [ ] Database dapat di-import tanpa error
- [ ] Login dengan 3 role berbeda (admin, doctor, receptionist) working
- [ ] Logout working dan clear session
- [ ] Registration working dengan validasi
- [ ] Dashboard berbeda untuk setiap role
- [ ] Authorization checks di semua controller methods
- [ ] Button/menu hide/show sesuai role
- [ ] Audit logs tercatat untuk semua actions
- [ ] Audit logs page accessible untuk admin
- [ ] README.md lengkap dengan screenshots
- [ ] Code rapi, ada comments, follow OOP principles
- [ ] Tidak ada error/warning di console
- [ ] Tested di browser (Chrome/Firefox)

---

## 💡 Tips

1. **Mulai dari Authentication:**

   - Setup database dulu
   - Implement login/logout
   - Test dengan sample users
   - Baru lanjut ke features lain

2. **Test Each Role:**

   - Login sebagai admin → test admin features
   - Login sebagai doctor → test doctor features
   - Login sebagai receptionist → test receptionist features

3. **Use Middleware Consistently:**

   - Setiap controller method harus ada auth check
   - Jangan lupa `requireAuth()` atau `requireRole()`

4. **Log Everything Important:**

   - User actions (CRUD)
   - Login/logout
   - Failed login attempts
   - Permission denials

5. **Security First:**
   - Always validate input
   - Use prepared statements
   - Hash passwords
   - Check permissions

---

## 📞 Support

Jika ada pertanyaan:

- Slack channel: `#week8-authentication`
- Office hours: Senin & Rabu 13:00-15:00
- Email: dosen@university.ac.id

---

## 🚀 Extra Challenge

Buat video demo (max 5 menit) yang menunjukkan:

1. Login dengan 3 role berbeda
2. Masing-masing dashboard
3. Authorization working (try to access restricted page)
4. Audit logs tercatat
5. Bonus features (jika ada)

Upload ke YouTube (unlisted) dan masukkan link di README.md

**Reward:** +10 bonus points

---

**Good luck! 🎉**

Tunjukkan bahwa Anda memahami OOP principles dan security best practices!
