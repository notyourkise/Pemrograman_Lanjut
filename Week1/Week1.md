# Modul Praktikum 1 — Pemrograman Lanjut

## Pengenalan & Dasar-Dasar PHP

Durasi: 1 × 120 menit

---

## I. Tujuan Praktikum

- Mahasiswa mampu memastikan lingkungan pengembangan XAMPP berjalan dengan baik.
- Mahasiswa memahami sintaks dasar PHP: tag pembuka/penutup, komentar, dan cara menampilkan output (echo).
- Mahasiswa mampu mendeklarasikan dan menggunakan variabel (string, integer).
- Mahasiswa mampu melakukan operasi aritmatika dasar.

---

## II. Dasar Teori (Ringkas)

PHP (Hypertext Preprocessor) adalah bahasa skrip sisi server. Kode PHP dieksekusi di server (Apache), lalu hasilnya dikirim ke browser sebagai HTML.

- Setiap blok kode PHP diawali `<?php` dan diakhiri `?>`.
- `echo` digunakan untuk menampilkan teks atau nilai variabel.
- Variabel diawali tanda dolar `$`, misal: `$nama = "Agus";`.
- Variabel dapat digunakan untuk operasi matematika sederhana seperti penjumlahan (`+`) dan pengurangan (`-`).

---

## III. Peralatan & Bahan

- PC/Laptop dengan XAMPP.
- Web Browser (Chrome/Firefox/Edge).
- Teks Editor (VS Code).

---

## IV. Langkah-Langkah Praktikum

### A. Verifikasi Server

1. Buka XAMPP Control Panel, klik Start pada modul Apache dan MySQL.
2. Buka browser dan akses `http://localhost`. Pastikan dashboard XAMPP tampil.

Catatan: Jika Apache gagal start (port 80/443 dipakai), matikan aplikasi yang memakai port tersebut (mis. IIS/Skype/VM) atau ubah port Apache di `httpd.conf`/`httpd-ssl.conf`.

### B. Membuat Folder & File Latihan

1. Buka folder `htdocs` (contoh: `C:\xampp\htdocs`).
2. Buat folder baru bernama `dasar_php`.
3. Di dalam `dasar_php`, buat file baru bernama `latihan.php`.

### C. Menulis Kode PHP Pertama

Salin kode berikut ke dalam `latihan.php` untuk berkenalan dengan variabel, komentar, dan `echo`.

```php
<?php
		// Ini adalah komentar satu baris

		/*
			Ini adalah komentar
			untuk banyak baris
		*/

		// 1. Membuat variabel
		$nama_depan = "Agus";
		$nama_belakang = "Ikhsyan";
		$angka_satu = 10;
		$angka_dua = 5;

		// 2. Menggabungkan string dan menampilkan variabel
		echo "<h1>Halo, selamat datang " . $nama_depan . " " . $nama_belakang . "!</h1>";
		echo "<hr>";

		// 3. Melakukan operasi aritmatika
		$hasil_tambah = $angka_satu + $angka_dua;
		echo "Hasil penjumlahan " . $angka_satu . " + " . $angka_dua . " adalah: " . $hasil_tambah;
		echo "<br>"; // Tag <br> untuk pindah baris

		$hasil_kurang = $angka_satu - $angka_dua;
		echo "Hasil pengurangan " . $angka_satu . " - " . $angka_dua . " adalah: " . $hasil_kurang;
?>
```

### D. Menjalankan File

1. Buka browser dan akses `http://localhost/dasar_php/latihan.php`.
2. Amati output. Ubah nilai variabel pada kode, simpan, lalu refresh browser untuk melihat perubahan.

---

## V. Tugas (Studi Kasus): Biodata Sederhana & Perhitungan Usia

1. Buat file baru `biodata.php` di dalam folder `dasar_php`.
2. Buat variabel berikut:
   - `$nama_lengkap` (isi nama Anda)
   - `$nim` (isi NIM Anda)
   - `$jurusan` (isi jurusan Anda)
   - `$tahun_lahir` (format 4 digit, contoh: `2003`)
   - `$tahun_sekarang` (contoh: `2025`)
3. Buat variabel `$umur = $tahun_sekarang - $tahun_lahir;`.
4. Tampilkan semua informasi ke browser dengan format rapi menggunakan HTML (`<h1>`, `<p>` atau `<br>`).

Contoh struktur kode `biodata.php`:

```php
<?php
	$nama_lengkap = "Nama Anda";
	$nim = "NIM Anda";
	$jurusan = "Jurusan Anda";
	$tahun_lahir = 2003;
	$tahun_sekarang = 2025; // opsional: (int) date('Y')

	$umur = $tahun_sekarang - $tahun_lahir;

	echo "<h1>Biodata Saya</h1>";
	echo "<p>Nama: $nama_lengkap</p>";
	echo "<p>NIM: $nim</p>";
	echo "<p>Jurusan: $jurusan</p>";
	echo "<p>Umur: $umur tahun</p>";
?>
```

Contoh output yang diharapkan:

```html
<h1>Biodata Saya</h1>
<p>Nama: Nama Kamu</p>
<p>NIM: Nim Kamu</p>
<p>Jurusan: Sistem Informasi</p>
<p>Umur: Umur Kamu</p>
```

### Cara Menjalankan & Pengumpulan

- Akses di browser: `http://localhost/dasar_php/biodata.php`.
- Ambil screenshot hasil output dan lampirkan dalam laporan.

---

## Checklist Keberhasilan Praktikum

- [ ] XAMPP (Apache & MySQL) berjalan, dashboard `http://localhost` dapat diakses.
- [ ] Folder `dasar_php` dan file `latihan.php` dibuat di `htdocs`.
- [ ] `latihan.php` menampilkan salam dan hasil operasi aritmatika.
- [ ] `biodata.php` menampilkan nama, NIM, jurusan, dan umur hasil perhitungan.
- [ ] Screenshot output `biodata.php` siap dikumpulkan.

---

## Troubleshooting Singkat

- Halaman kosong/teks PHP tampil mentah: pastikan file disimpan sebagai `.php` di dalam `htdocs` dan diakses via `http://localhost/...`, bukan buka file langsung.
- Error Apache port in use: hentikan layanan yang memakai port 80/443 atau ubah port Apache di konfigurasi XAMPP.
- Perubahan tidak terlihat: simpan file dan refresh (Ctrl+R). Cek cache browser jika perlu (Ctrl+F5).
