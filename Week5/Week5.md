# Modul Praktikum 5 — CRUD + MySQL (MVC, Studi Kasus Rumah Sakit)

Durasi: 1 × 120 menit • Prasyarat: Minggu 2–4 (MVC dasar + OOP) • Lingkungan: XAMPP (Apache + MySQL), PHP ≥ 7.4

---

## I. Tujuan

- Menyiapkan database MySQL dan menghubungkan PHP via mysqli (prepared statements).
- Memahami alur MVC untuk CRUD entitas utama (Pasien).
- Menerapkan prepared statements, validasi, dan escaping output.
- Menyusun struktur proyek rapi (public, app/{controllers, models, views, core}).

---

## II. Studi Kasus & Skema Data (Rumah Sakit)

Minimal 4 entitas saling terhubung:

- departments (1 — n) doctors
- doctors (n — n) patients melalui appointments
- patients (1 — n) appointments

Tabel utama untuk Minggu 5: patients. Tabel lain (departments, doctors, appointments) disiapkan untuk Minggu 6.

File SQL: `Week5/database/hospital.sql` (buat DB `materi_rs`, tabel + sample data + foreign key).

---

## III. Struktur Proyek (Direkomendasikan)

```
Week5/
├─ Week5.md
├─ public/
│  └─ index.php           # Front controller + router sangat sederhana
├─ app/
│  ├─ config.php          # Konfigurasi DB
│  ├─ core/
│  │  ├─ Autoloader.php   # Autoload class app/*
│  │  ├─ Controller.php   # Base controller (helper render)
│  │  └─ Database.php     # Koneksi mysqli (singleton)
│  ├─ models/
│  │  └─ Patient.php      # Model CRUD pasien (mysqli prepared)
│  ├─ controllers/
│  │  └─ PatientsController.php
│  └─ views/
│     └─ patients/
│        ├─ index.php     # List + tombol tambah/aksi
│        ├─ create.php    # Form tambah
│        └─ edit.php      # Form edit
└─ database/
   └─ hospital.sql        # Skema + seed
```

---

## IV. Langkah Persiapan

1. Import Database

- Buka phpMyAdmin → Import → pilih `Week5/database/hospital.sql` → Go.
- Atau gunakan CLI (opsional):
  - Buat DB: `CREATE DATABASE IF NOT EXISTS materi_rs CHARACTER SET utf8mb4;`
  - Jalankan isi file `hospital.sql`.

2. Konfigurasi Koneksi

- Atur kredensial di `Week5/app/config.php` (host, dbname, user, pass).

3. Jalankan Aplikasi

- Akses via browser: `http://localhost/MATERI-ASDOS/Week5/public/`

---

## V. Implementasi Inti (Ringkas)

1. Koneksi mysqli (`app/core/Database.php`)

- Gunakan singleton agar hanya satu koneksi:
  - `new mysqli(host, user, pass, db)` lalu `set_charset('utf8mb4')`
  - Aktifkan report error: `mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)`

2. Model Pasien (`app/models/Patient.php`)

- Method: `all()`, `find($id)`, `create($data)`, `update($id, $data)`, `delete($id)`
- Gunakan prepared statement mysqli dan `bind_param` sesuai tipe.

3. Controller (`app/controllers/PatientsController.php`)

- Aksi: `index`, `create (GET/POST)`, `edit (GET/POST)`, `delete (POST)`
- Validasi sederhana: `name` wajib, `dob` opsional format `YYYY-MM-DD`, `gender` in {M, F}, `phone` opsional.
- Kirim `$errors` dan `$old` ke view jika validasi gagal.

4. View (`app/views/patients/*.php`)

- Tabel daftar pasien dan form tambah/edit.
- Escape output: `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
- Tombol aksi menuju `?c=patients&a=edit&id=...` / `?c=patients&a=delete&id=...`.

5. Router Sederhana (`public/index.php`)

- Parameter query: `c` (controller), `a` (action). Default: `patients/index`.
- Autoloader app dan pemetaan nama class.

---

## VI. Demo Alur

1. Buka daftar pasien → klik Tambah → isi dan simpan → kembali ke list.
2. Edit satu pasien → ubah `phone` → simpan → validasi tetap dijalankan.
3. Hapus pasien → data hilang dari list.

---

## VII. Tugas Praktikum (Wajib)

1. Tambahkan validasi berikut di create/edit pasien:

- `name`: 3–100 karakter.
- `phone`: hanya digit, `+`, `-`, dan spasi; maksimum 20 karakter.
- `dob`: jika diisi, harus tanggal valid dan tidak di masa depan.

2. Tambahkan kolom baru di pasien: `address TEXT` (nullable). Update form & list.

3. Tambahkan pagination di daftar pasien (10 per halaman). Parameter `page` di query string.

4. Tambahkan pencarian by nama (parameter `q`). Integrasikan dengan pagination.

5. Bonus: Soft delete (kolom `deleted_at` NULL/DATE). Filter list hanya yang belum dihapus.

---

## VIII. Checklist

- [ ] Koneksi database berhasil (tidak error saat load halaman list pasien).
- [ ] Tambah pasien menyimpan data ke tabel `patients`.
- [ ] Edit pasien mengubah data yang ada.
- [ ] Hapus pasien menghapus baris (atau isi `deleted_at`).
- [ ] Semua input tervalidasi dan output di-escape.
- [ ] Struktur folder MVC sesuai dan rapi.

---

## IX. Troubleshooting

- Error "Class 'mysqli' not found" atau koneksi gagal: pastikan ekstensi `mysqli` aktif di php.ini (XAMPP → Apache → Config → php.ini → pastikan `extension=mysqli` tidak di-comment), lalu restart Apache.
- Error koneksi: cek host/user/password/database di `app/config.php`.
- Halaman putih: cek `error_reporting`/`display_errors` saat dev; periksa `storage/logs` (opsional).
- Tabel tidak ada: pastikan sudah import `hospital.sql`.
- URL 404: akses via `public/index.php` dan gunakan query `?c=patients&a=index` jika router default tidak jalan.

---

## X. Arah Minggu Depan

Minggu 6: Implementasi relasi penuh (departments, doctors, appointments), dropdown FK, join untuk list, validasi referensial, filtering dan pagination, serta transaksi sederhana (booking jadwal dokter).
