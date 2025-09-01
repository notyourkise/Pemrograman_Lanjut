# Modul Praktikum 2 — OOP, Class, Form & Validasi

Durasi: 1 × 120 menit • Prasyarat: Modul Praktikum 1

---

## I. Tujuan Praktikum

- Mahasiswa memahami konsep dasar OOP (Class, Object, Properti, Method).
- Mahasiswa mampu membuat struktur proyek yang terorganisir.
- Mahasiswa mampu membuat Class sederhana.
- Mahasiswa mampu membuat form HTML dan menangani inputnya (`$_POST`).
- Mahasiswa mampu melakukan validasi dasar pada input form.

---

## II. Dasar Teori (Ringkas)

- OOP mengorganisir kode dalam objek (properti + method) yang dibuat dari cetak biru bernama Class.
- Form HTML mengumpulkan input pengguna. Data dikirim ke server (umumnya `POST`) dan dibaca di PHP via `$_POST`.
- Validasi input (mis. wajib isi, format) mencegah data tidak valid masuk ke proses selanjutnya.

---

## III. Langkah-Langkah Praktikum

Catatan: Kita menggunakan folder `Week2` (bukan `simak_sederhana`). Semua path di bawah relatif terhadap `C:\xampp\htdocs\MATERI-ASDOS\Week2`.

### A. Buat Struktur Proyek

Buat struktur direktori berikut:

```
Week2/
├─ index.php
├─ controllers/
│  └─ mahasiswa_controller.php
├─ models/
│  └─ Mahasiswa.php
└─ views/
   └─ mahasiswa_view.php
```

### B. Buat Class Mahasiswa (`models/Mahasiswa.php`)

```php
<?php
class Mahasiswa {
    public $nim;
    public $nama;
    public $jurusan;
}
```

### C. Buat View Tabel & Form (`views/mahasiswa.view.php`)

```php
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Mahasiswa</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 24px auto; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f5f5f5; text-align: left; }
    .error { color: red; font-style: italic; margin: 4px 0 8px; }
    form > div { margin-bottom: 12px; }
    input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
    button { padding: 8px 14px; cursor: pointer; }
  </style>
</head>
<body>
  <h1>Data Mahasiswa</h1>

  <table>
    <thead>
      <tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>Jurusan</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($mahasiswa)) : ?>
        <?php foreach ($mahasiswa as $mhs) : ?>
          <tr>
            <td><?= htmlspecialchars($mhs['nim']) ?></td>
            <td><?= htmlspecialchars($mhs['nama']) ?></td>
            <td><?= htmlspecialchars($mhs['jurusan']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr><td colspan="3" style="text-align:center;">Belum ada data.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <h2>Tambah Mahasiswa Baru</h2>
  <form method="POST">
    <div>
      <label>NIM</label>
      <input type="text" name="nim" value="<?= isset($_POST['nim']) ? htmlspecialchars($_POST['nim']) : '' ?>">
      <?php if (isset($errors['nim'])) : ?>
        <p class="error"><?= $errors['nim']; ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label>Nama</label>
      <input type="text" name="nama" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
      <?php if (isset($errors['nama'])) : ?>
        <p class="error"><?= $errors['nama']; ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label>Jurusan</label>
      <input type="text" name="jurusan" value="<?= isset($_POST['jurusan']) ? htmlspecialchars($_POST['jurusan']) : '' ?>">
      <?php if (isset($errors['jurusan'])) : ?>
        <p class="error"><?= $errors['jurusan']; ?></p>
      <?php endif; ?>
    </div>

    <button type="submit">Simpan</button>
  </form>
</body>
</html>
```

### D. Buat Controller (`controllers/mahasiswa_controller.php`)

```php
<?php
// Data statis sementara
$mahasiswa = [
    ["nim" => "2101001", "nama" => "Agus", "jurusan" => "Informatika"],
    ["nim" => "2101002", "nama" => "Ikhsan", "jurusan" => "Sistem Informasi"],
];

// Pastikan $errors selalu tersedia
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    // Validasi sederhana
    if ($nim === '') {
        $errors['nim'] = 'NIM wajib diisi.';
    }
    if ($nama === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    }
    if ($jurusan === '') {
        $errors['jurusan'] = 'Jurusan wajib diisi.';
    }

    // Jika tidak ada error, tambahkan data ke array (simulasi)
    if (empty($errors)) {
        $mahasiswa[] = [
            'nim' => $nim,
            'nama' => $nama,
            'jurusan' => $jurusan,
        ];
    }
}

// Muat view
require __DIR__ . '/../views/mahasiswa.view.php';
```

### E. Buat Titik Masuk (`index.php`)

```php
<?php
require __DIR__ . '/controllers/mahasiswa_controller.php';
```

### F. Menjalankan Aplikasi

- Jalankan Apache (XAMPP).
- Akses di browser: `http://localhost/MATERI-ASDOS/Week2/`.
- Coba submit form dalam keadaan kosong (harus muncul pesan error). Isi dengan benar (data baru muncul di tabel, sementara di memori).

---

## IV. Tugas (Studi Kasus): Validasi Form Tambah Mahasiswa

Lanjutkan di `controllers/mahasiswa_controller.php` dan `views/mahasiswa.view.php` (sudah dicontohkan di atas), pastikan:

1. Menghapus `echo/var_dump` dummy (sudah tidak digunakan di versi ini).
2. Menginisialisasi `$errors = []` setiap request.
3. Mem-validasi `nim`, `nama`, `jurusan` wajib diisi. Tampilkan pesan error di bawah input terkait.
4. Jika valid, tambahkan data baru ke array `$mahasiswa` dan tampilkan di tabel.
5. Bonus: tambahkan validasi panjang minimal (mis. NIM ≥ 5 karakter) dan trim spasi.

---

## V. Checklist Keberhasilan

- [ ] Struktur folder `controllers/`, `models/`, `views/` dan `index.php` dibuat di `Week2/`.
- [ ] Kelas `Mahasiswa` terdefinisi di `models/Mahasiswa.php`.
- [ ] Halaman menampilkan tabel data awal.
- [ ] Form dapat mengirim data via POST dan menampilkan error jika input kosong.
- [ ] Data valid akan tampil pada tabel setelah submit.

---

## VI. Troubleshooting Singkat

- Halaman putih/teks PHP mentah: pastikan mengakses via `http://localhost/...`, bukan buka file langsung.
- Include path error: gunakan `__DIR__` seperti pada contoh untuk path relatif yang aman.
- Perubahan tidak terlihat: simpan file, CTRL+F5 untuk hard refresh.
- Karakter spesial tampil aneh: gunakan `htmlspecialchars()` saat echo nilai dari input.
