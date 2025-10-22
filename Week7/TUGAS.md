# Tugas Minggu 7 - Design Patterns dan Best Practices OOP

## Deskripsi Tugas (Format Paragraf untuk PPT)

Lanjutkan proyek sistem informasi yang telah dibuat di Minggu 6 dengan menerapkan Design Patterns dan best practices OOP yang telah dipelajari. Implementasikan Builder Pattern melalui class Validator yang support method chaining untuk validasi form, dimana setiap method (required, minLength, maxLength, email, phone, date, dll) harus return $this agar bisa di-chain secara fluent, tambahkan minimal lima aturan validasi tambahan selain yang sudah ada di contoh (seperti numeric, between, in, unique, confirmed) dan terapkan Validator di minimal dua controller dengan minimal lima aturan validasi per form. Implementasikan Factory Pattern dengan membuat class AppointmentFactory yang memiliki method create untuk generate single appointment dengan overrides support, createMany untuk batch generation, createForPatient untuk generate appointments untuk patient spesifik, createForDoctor untuk doctor spesifik, dan createInDateRange untuk appointments dalam range tanggal tertentu, kemudian buat seeding script seed-appointments.php yang menggunakan AppointmentFactory untuk populate database dengan minimal 100 appointment data yang realistis. Extend Repository Pattern yang sudah ada di Week 6 dengan menambahkan fitur Recycle Bin untuk appointments dimana AppointmentRepository harus memiliki method countDeleted untuk hitung jumlah soft-deleted records, getDeleted dengan pagination support, restore untuk set deleted_at kembali NULL, dan forceDelete untuk permanent deletion, kemudian buat AppointmentsController dengan action recycle untuk display deleted records, restore untuk recovery, dan forceDelete untuk permanent removal, serta buat view appointments/recycle.php dengan table list, restore button, dan force delete button yang include confirmation modal. Implementasikan Helper Classes dengan menggunakan Sanitizer untuk clean input di semua controller action (cleanArray untuk $\_POST data) dan escape output di semua view (escape untuk display data), serta implementasikan proper type sanitization seperti int untuk ID dan page number, email untuk email fields, dan url untuk website fields. Sebagai bonus implementasikan CSRF Protection dengan menambahkan Csrf::field() di semua form dan Csrf::verifyOrFail() di semua POST/PUT/DELETE controller actions, kemudian buat file csrf-test.html untuk testing apakah form submission dari external source di-reject dengan HTTP 403 error. Dokumentasikan implementasi dalam laporan berformat PDF atau Markdown yang berisi penjelasan Builder Pattern dengan screenshot Validator code dan contoh penggunaan di controller menunjukkan method chaining, penjelasan Factory Pattern dengan screenshot AppointmentFactory code dan hasil running seed script menunjukkan 100+ records ter-insert, penjelasan Repository Extension dengan screenshot code dan halaman Recycle Bin yang functional, penjelasan penggunaan Sanitizer di input cleaning dan output escaping dengan screenshot before/after, dan bonus screenshot CSRF test yang show rejection dari external form. Kumpulkan berkas ZIP berisi kode aplikasi lengkap dengan folder factories dan semua helper classes, dump SQL database yang sudah include deleted_at column di table appointments, file seed-appointments.php yang ready to run, file csrf-test.html untuk CSRF testing, dan laporan lengkap dengan screenshots yang menunjukkan implementasi semua Design Patterns. Penilaian terdiri dari Validator Extension dengan Builder Pattern 35 poin (lima aturan baru implemented, chainable, tested di 2+ controllers), AppointmentFactory dengan Factory Pattern 25 poin (semua 5 methods working, seed script success generate 100+ realistic data), Repository Extension untuk Recycle Bin 20 poin (all CRUD soft-delete methods, controller actions, view UI complete), Sanitizer implementation 10 poin (input cleaned di controllers, output escaped di views), dan BONUS CSRF Protection 10 poin (all forms protected, verification works, test file shows rejection).

---

## Checklist Singkat

### 1. Builder Pattern - Validator (35%)

✅ Extend Validator.php dengan 5 aturan baru:

- numeric() - validasi angka
- between($min, $max) - validasi range
- in($values) - validasi dalam array
- unique($table, $column) - validasi uniqueness
- confirmed() - validasi field confirmation
  ✅ Implement method chaining (return $this)
  ✅ Terapkan di min. 2 controllers dengan 5+ aturan
  ✅ Screenshot code & usage example

### 2. Factory Pattern - AppointmentFactory (25%)

✅ Buat app/factories/AppointmentFactory.php
✅ Method create($overrides = [])
✅ Method createMany($count)
✅ Method createForPatient($patientId, $count)
✅ Method createForDoctor($doctorId, $count)
✅ Method createInDateRange($start, $end, $count)
✅ Buat public/seed-appointments.php
✅ Generate minimal 100 appointments
✅ Screenshot seed results

### 3. Repository Extension - Recycle Bin (20%)

✅ Extend AppointmentRepository:

- countDeleted()
- getDeleted($limit, $offset)
- restore($id)
- forceDelete($id)
  ✅ AppointmentsController actions: recycle(), restore(), forceDelete()
  ✅ View app/views/appointments/recycle.php
  ✅ Pagination + confirmation modal
  ✅ Screenshot Recycle Bin UI

### 4. Helper Classes - Sanitizer (10%)

✅ Sanitizer::cleanArray() di semua controller POST
✅ Sanitizer::escape() di semua view output
✅ Sanitizer::int() untuk ID & page number
✅ Sanitizer::email() untuk email fields
✅ Screenshot before/after sanitization

### 5. BONUS: CSRF Protection (10%)

✅ Csrf::field() di semua forms
✅ Csrf::verifyOrFail() di semua POST actions
✅ Buat csrf-test.html untuk testing
✅ Test rejection dengan HTTP 403
✅ Screenshot test results

---

## Deliverables

1. **Kode Aplikasi** (Week7 folder lengkap)

   - app/helpers/ (Validator.php, Sanitizer.php, Csrf.php)
   - app/factories/ (AppointmentFactory.php)
   - app/repositories/ (AppointmentRepository.php extended)
   - app/controllers/ (AppointmentsController.php updated)
   - app/views/appointments/ (recycle.php)
   - public/seed-appointments.php

2. **Dump SQL** (hospital.sql dengan deleted_at column)

3. **File Testing** (csrf-test.html untuk bonus)

4. **Laporan** (PDF/Markdown) berisi:

   - **Builder Pattern (35%):**

     - Screenshot Validator code dengan 5 aturan baru
     - Screenshot usage di controller dengan method chaining
     - Screenshot validation error messages

   - **Factory Pattern (25%):**

     - Screenshot AppointmentFactory code (semua 5 methods)
     - Screenshot seed-appointments.php code
     - Screenshot terminal output saat running seed (100+ records)
     - Screenshot database records hasil seeding

   - **Repository Extension (20%):**

     - Screenshot AppointmentRepository methods
     - Screenshot AppointmentsController actions
     - Screenshot Recycle Bin UI (list + restore + delete)
     - Screenshot restore action working

   - **Sanitizer Implementation (10%):**

     - Screenshot input cleaning di controller
     - Screenshot output escaping di view
     - Screenshot before/after comparison

   - **BONUS: CSRF (10%):**
     - Screenshot form dengan Csrf::field()
     - Screenshot controller dengan verifyOrFail()
     - Screenshot csrf-test.html
     - Screenshot rejection (HTTP 403)

5. **Screenshot Summary (Minimal 8 screenshot):**
   1. Validator code dengan 5 aturan baru
   2. Validator usage dengan method chaining
   3. AppointmentFactory code complete
   4. Seed script terminal output (100+ records)
   5. Recycle Bin halaman UI
   6. Sanitizer usage di controller & view
   7. CSRF form field (inspect element)
   8. CSRF test rejection (403 error)

---

## Rubrik Penilaian

| Komponen                                  | Bobot | Kriteria Detail                                                                                                                                          |
| ----------------------------------------- | ----- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Validator Extension (Builder Pattern)** | 35%   | - 5 aturan baru implemented (7% each)<br>- Method chaining works correctly<br>- Applied in 2+ controllers<br>- Code documented<br>- Screenshots complete |
| **AppointmentFactory (Factory Pattern)**  | 25%   | - All 5 methods implemented (5% each)<br>- Seed script generates 100+ records<br>- Realistic data<br>- Code documented<br>- Screenshots complete         |
| **Recycle Bin (Repository Extension)**    | 20%   | - Repository methods complete (10%)<br>- Controller actions working (5%)<br>- View UI functional (5%)<br>- Screenshots complete                          |
| **Sanitizer Implementation**              | 10%   | - Input cleaning in controllers (5%)<br>- Output escaping in views (5%)<br>- Consistent usage                                                            |
| **BONUS: CSRF Protection**                | 10%   | - All forms protected (5%)<br>- All actions verified (5%)<br>- Test passed with rejection                                                                |

**Total: 100% + 10% Bonus = 110%**

---

**Deadline:** Sesuai jadwal praktikum  
**Format Pengumpulan:** ZIP berisi semua file + laporan PDF/MD  
**Nama File:** NIM_Nama_Week7_OOP.zip

---

## Tips Pengerjaan

1. **Mulai dari Builder Pattern** - Extend Validator dulu karena paling mudah
2. **Factory Pattern** - Paling fun, lihat contoh PatientFactory
3. **Repository Extension** - Copy pattern dari PatientRepository
4. **Test Incremental** - Test setiap pattern setelah selesai
5. **Screenshot Early** - Ambil screenshot sambil coding
6. **Code Comments** - Tambahkan comments untuk memudahkan review
7. **Commit Often** - Gunakan git untuk backup progress

---

## Catatan Penting

⚠️ **Focus pada OOP**, bukan hanya fitur working  
⚠️ **Design Patterns** harus properly implemented  
⚠️ **Code Quality** matters - DRY, SOLID, clean code  
⚠️ **Documentation** - Jelaskan WHY, bukan hanya WHAT  
⚠️ **Screenshots** - Must show code AND results  
**Nilai:** 100 poin (breakdown di atas)
