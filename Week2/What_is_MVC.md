# Apa itu MVC (Model–View–Controller)

MVC adalah pola arsitektur untuk memisahkan kode menjadi tiga bagian agar rapi, mudah dirawat, dan mudah dikembangkan.

- Model: merepresentasikan data dan aturan bisnis (mis. kelas `Mahasiswa`).
- View: menampilkan UI/HTML kepada pengguna (mis. `views/mahasiswa_view.php`).
- Controller: penghubung input pengguna dengan Model dan View (mis. `controllers/mahasiswa_controller.php`).

Di materi Week2:
- `models/Mahasiswa.php` = Model sederhana (menyimpan properti `nim`, `nama`, `jurusan`).
- `views/mahasiswa_view.php` = View untuk tabel + form, memakai `htmlspecialchars()` untuk escape output dan menampilkan error per field.
- `controllers/mahasiswa_controller.php` = Controller yang menerima `POST`, melakukan trimming & validasi (wajib isi, opsional panjang minimal), lalu mengirim data/hasil validasi ke View.
- `index.php` memanggil Controller sebagai entry point aplikasi.

## Kegunaan MVC
- Pemisahan tanggung jawab (separation of concerns) → file lebih fokus, mudah dibaca dan di maintenance.
- Skalabilitas lebih baik → mudah menambah fitur (mis. kolom baru, validasi baru) tanpa mengganggu bagian lain.
- Reusabilitas → Model dapat dipakai ulang di banyak Controller/View.
- Testability → logika (Model/Controller) bisa diuji tanpa terikat tampilan.
- Kolaborasi tim → backend dan frontend bisa bekerja paralel (Controller/Model vs View).

## Kapan Harus Menggunakan MVC
- Aplikasi web dengan banyak halaman/fitur CRUD, form, dan validasi server-side.
- Proyek tim yang butuh struktur jelas sejak awal.
- Ingin menambah/ubah UI (View) tanpa menyentuh logika inti (Model/Controller).
- Ingin menulis unit/integration test untuk logika aplikasi.

Tidak wajib (boleh lebih sederhana) jika:
- Skrip kecil/sekali pakai, satu file PHP cukup, perubahan jarang.
- Halaman statis tanpa interaksi kompleks.

## Kekurangan MVC
- Boilerplate lebih banyak (tiga lapisan, lebih banyak file & include).
- Kurva belajar: butuh disiplin memisahkan logika dari tampilan.
- Alur terasa lebih panjang (index → controller → model → view) sehingga debugging pemula bisa lebih lama.
- Berpotensi over-engineering untuk kebutuhan sangat sederhana.

## Kenapa Web Dev Menggunakan MVC
- Terbukti di banyak framework populer (Laravel, CodeIgniter, Symfony) dan memudahkan scaling tim.
- Kode lebih maintainable untuk jangka panjang.
- Memfasilitasi praktik baik: validasi terpusat, sanitasi output di View, penggunaan path aman dengan `__DIR__`.

## Alur Singkat pada Contoh Week2
1. User membuka `index.php` → memuat `controllers/mahasiswa_controller.php`.
2. Controller membaca `$_POST`, melakukan `trim()` & validasi wajib isi (bonus: panjang minimal NIM ≥ 5).
3. Jika valid, data baru ditambahkan ke array `$mahasiswa`; jika tidak valid, `$errors` diisi.
4. Controller me-load `views/mahasiswa_view.php`, meneruskan `$mahasiswa` dan `$errors` untuk ditampilkan (tabel + pesan error di bawah input).

## Praktik Baik (sesuai Week2)
- Gunakan `__DIR__` pada `require` untuk include path relatif yang aman.
- Escape output user dengan `htmlspecialchars()` di View.
- Simpan aturan bisnis & validasi di Controller/Model, bukan di View.
- Inisialisasi `$errors = []` setiap request agar View selalu konsisten.

## Langkah Lanjut
- Pindahkan data dari array statis ke database (Model menangani query & mapping data).
- Tambah routing agar tiap aksi/halaman punya controller/route sendiri.
- Tambah layer service/repository bila logika bisnis tumbuh dan kompleks.