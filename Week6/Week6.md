# Week 6 — Pondasi Lanjutan (Repository, Paginator, Relasi)

Dokumen ini menjelaskan apa saja cakupan Minggu 6 dan apa yang sengaja ditunda untuk Minggu 7 (Validasi & Sanitasi + CSRF), supaya materi mingguan terjaga rapi dan bertahap.

## Ringkasan

- Tujuan: merapikan arsitektur dari Week5 dan menambah fitur relasi 1–N (Pasien → Janji Temu) agar siap untuk topik keamanan minggu depan.
- Teknologi: PHP (mysqli), DB yang sama `hospital`, router query string (`?c=...&a=...`).

## Cakupan Minggu 6 (yang tersedia)

- Repository Pattern: `PatientRepository`, `AppointmentRepository` (akses DB terpusat, pakai prepared statements).
- Paginator: hitung total halaman/offset dan dipakai di listing Pasien & Janji Temu.
- Flash Message: helper `Flash` untuk pesan sukses/gagal.
- Relasi 1–N: CRUD Janji Temu (Appointments) terhubung ke Pasien, wajib pilih Dokter.
- UX/Antarmuka: layout form 2 kolom, input full-width, breadcrumb dapat diklik, sort di tabel Pasien (ID/Nama/DOB).
- Helper transaksi tersedia (begin/commit/rollback) namun belum dipakai sebagai materi (disiapkan untuk praktik lanjutan).

## Bukan Bagian Minggu 6 (disimpan untuk Minggu 7)

- CSRF Token (generate + verifikasi).
- Standarisasi sanitasi input terstruktur (wrapper/filter_input terpadu).
- Uji payload berbahaya (XSS/CSRF) dan dokumentasi mitigasi.
- Validasi bisnis lanjutan (contoh: deteksi bentrok jadwal per dokter, rate limiting, dsb.).

Dengan demikian, Week 6 hanya menjadi pondasi rapi dan fungsional—tanpa materi keamanan Week 7.

## Cara Menjalankan (Ringkas)

- URL dasar: `http://localhost/MATERI-ASDOS/Week6/public/`
- Pasien: `?c=patients&a=index`
- Janji Temu: `?c=appointments&a=index`
- Pastikan DB `hospital` berisi minimal data `doctors` agar dropdown dokter memiliki pilihan.

## Catatan untuk Praktikum Minggu 7

- Week 6 ini sengaja belum ada CSRF dan sanitasi lanjutan. Pada Week 7, mahasiswa akan:
  - Menambahkan token CSRF di semua form POST dan memverifikasinya di controller.
  - Menstandarisasi sanitasi & validasi input (server-side) dengan aturan yang terdokumentasi.
  - Mencoba payload XSS/CSRF dan menulis laporan uji + mitigasi.

> Jika diperlukan, materi Dokter (CRUD Dokter) dapat ditambahkan di Week 6 sebagai latihan kecil, namun bukan fokus utama. Fokus utama tetap pondasi arsitektur dan relasi 1–N.# Modul Praktikum 6 — CRUD Relasional (MVC + MySQL Lanjutan)

Durasi: 1 × 120 menit • Prasyarat: Minggu 5 (CRUD Pasien + PDO)

---

## I. Tujuan

- Menerapkan relasi antar entitas (FK) pada aplikasi MVC.
- Membuat CRUD untuk Doctors dan Appointments dengan referensi ke Patients & Departments.
- Menggunakan JOIN untuk menampilkan data relasional dengan efisien.
- Menambah fitur pagination + pencarian pada list relasional.
- Memahami transaksi sederhana saat membuat appointment (opsional).

---

## II. Entitas & Relasi

- departments (1 — n) doctors
- doctors (n — n) patients via appointments
- patients (1 — n) appointments

Kita akan menambahkan:

- CRUD Doctors (dengan dropdown Department)
- CRUD Appointments (dropdown Patient & Doctor, jadwal datetime, catatan)
- List Appointments menampilkan JOIN: patient.name, doctor.name, department.name, schedule

---

## III. Rencana Implementasi

1. Model Tambahan

- `Doctor` (all/find/create/update/delete, allByDepartment optional)
- `Department` (all/find, read-only di modul ini)
- `Appointment` (all/find/create/update/delete) dengan validasi jadwal.

2. Controller & View

- `DoctorsController` (index, create, edit, delete)
- `AppointmentsController` (index, create, edit, delete)
- View daftar + form dengan dropdown terisi dari model terkait.

3. Query JOIN

- Appointments index: `SELECT a.id, a.schedule, p.name AS patient, d.name AS doctor, dept.name AS department FROM appointments a JOIN patients p ON ... JOIN doctors d ON ... JOIN departments dept ON ...`

4. Validasi

- Doctor: `name` wajib; `department_id` wajib dan harus ada di tabel departments.
- Appointment: `patient_id` & `doctor_id` harus valid; `schedule` tanggal-waktu valid dan tidak di masa lalu (opsional kebijakan); hindari jadwal tabrakan (bonus).

5. Transaksi (Opsional)

- Saat membuat appointment, bungkus dalam transaksi jika ada beberapa langkah (cek konflik jadwal, insert, log audit).

---

## IV. Tugas Praktikum

1. Implementasikan CRUD Doctors lengkap (list, create, edit, delete) dengan dropdown departments.
2. Implementasikan CRUD Appointments lengkap. Di form, gunakan dropdown untuk patient dan doctor.
3. Tampilkan list appointments dengan kolom: Tanggal, Pasien, Dokter, Department, Catatan.
4. Tambahkan pencarian appointments berdasarkan nama pasien/dokter atau rentang tanggal.
5. Pagination untuk list appointments (10 per halaman).
6. Bonus: Cek konflik jadwal dokter (tidak boleh ada appointment bentrok di interval 30 menit yang sama).

---

## V. Checklist

- [ ] Dropdown FK terisi benar dari database.
- [ ] Data yang dihapus tidak melanggar FK (gunakan RESTRICT/CASCADE sesuai desain).
- [ ] JOIN menampilkan nama relasi dengan benar.
- [ ] Validasi server-side berjalan.
- [ ] Pagination & search bekerja.

---

## VI. Troubleshooting

- Error FK saat delete doctor: pastikan hapus appointment terkait dulu atau ubah kebijakan ON DELETE.
- Dropdown kosong: cek query `all()` pada model referensi.
- List lambat: pastikan kolom yang sering difilter/di-join memiliki index yang sesuai (mis. `appointments.schedule`).

---

## VII. Catatan Lanjutan

- Pertimbangkan pattern Repository/Service untuk memisahkan query dari controller jika kode mulai panjang.
- Bisa tambah autentikasi sederhana (admin) bila dibutuhkan.
