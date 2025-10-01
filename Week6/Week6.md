# Modul Praktikum 6 — CRUD Relasional (MVC + MySQL Lanjutan)

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
