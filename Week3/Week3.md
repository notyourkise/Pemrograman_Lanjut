# Modul Praktikum 3 — OOP Dasar (Class & Object Fundamental)

Durasi: 1 × 120 menit • Prasyarat: Minggu 1 & 2 (PHP dasar + form & validasi sederhana)

---

## I. Tujuan

- Memahami konsep: Class, Object, Properti, Method.
- Membuat dan menggunakan object (instansiasi).
- Menggunakan constructor (`__construct`).
- Mengenal visibility: `public`, `private`, `protected`.
- Membuat method yang mengembalikan nilai.
- Menyimpan banyak object dalam array dan menampilkannya.

---

## II. Dasar Teori (Ringkas)

Class = cetak biru. Object = instance nyata.  
Properti = data. Method = perilaku.  
Constructor (`__construct`) dieksekusi saat object dibuat.  
Visibility:

- public: bisa diakses dari mana saja.
- private: hanya di dalam class itu.
- protected: di class itu & turunannya.

Getter dipakai untuk mengambil nilai properti yang disembunyikan (private).

---

## III. Langkah-Langkah Praktikum (Gunakan folder `Week3`)

Buat file: `Week3/index.php`. Kerjakan bertahap (jangan langsung final).

### 1. Class Sederhana

```php
<?php
class Mahasiswa {
  public $nim;
  public $nama;
  public $jurusan;
}

$m1 = new Mahasiswa();
$m1->nim = '10231063';
$m1->nama = 'Haikal';
$m1->jurusan = 'Sistem Informasi';

echo $m1->nama;
```

Penjelasan:

- Class `Mahasiswa` adalah blueprint: mendefinisikan struktur data (nim, nama, jurusan).
- Object `$m1` adalah hasil konkret dari class.
- Akses properti dengan tanda panah `->`.

Kenapa seperti ini?

- Memisahkan definisi (class) dari data aktual (object) membuat kode dapat digunakan ulang (reuse).
- Lebih rapi dibandingkan array asosiatif karena IDE bisa bantu auto-complete & validasi.

Kapan digunakan?

- Saat data punya bentuk tetap dan akan dibuat banyak instance (misal banyak mahasiswa, produk, buku, dsb).
- Langsung butuh menampung data tanpa aturan khusus (belum perlu validasi/enkapsulasi).

Catatan:

- Semua properti masih `public`, artinya bebas diubah dari luar (belum aman, tapi mudah untuk belajar dasar).

### 2. Tambahkan Constructor

```php
class Mahasiswa {
  public $nim;
  public $nama;
  public $jurusan;

  public function __construct($nim, $nama, $jurusan) {
    $this->nim = $nim;
    $this->nama = $nama;
    $this->jurusan = $jurusan;
  }
}

$m1 = new Mahasiswa('10231063', 'Haikal', 'Sistem Informasi');
$m2 = new Mahasiswa('10231073', 'Pangeran', 'Sistem Informasi');
```

Penjelasan:

- Method `__construct` otomatis dipanggil saat `new Mahasiswa(...)`.
- `__construct` memastikan object selalu dalam keadaan lengkap (nilai langsung terisi).

Kenapa pakai constructor?

- Mencegah lupa mengisi properti (dibanding cara manual `$m1->nim = ...`).
- Memudahkan inisialisasi awal (bisa juga untuk validasi ringan atau normalisasi data).

Kapan digunakan?

- Hampir selalu ketika object butuh nilai awal yang wajib.
- Saat ingin memastikan object tidak pernah “kosong”/invalid.

Catatan:

- Urutan parameter penting; bisa diperjelas dengan dokumentasi atau tipe (nanti di OOP lanjut bisa pakai named arguments di PHP 8+).

### 3. Method (Perilaku)

```php
class Mahasiswa {
  public $nim;
  public $nama;
  public $jurusan;

  public function __construct($nim, $nama, $jurusan) {
    $this->nim = $nim;
    $this->nama = $nama;
    $this->jurusan = $jurusan;
  }

  public function deskripsi() {
    return "{$this->nim} - {$this->nama} ({$this->jurusan})";
  }
}

$m1 = new Mahasiswa('10231063', 'Haikal', 'Sistem Informasi');
echo $m1->deskripsi();
```

Penjelasan:

- Method `deskripsi()` menambahkan perilaku (fungsi) yang terkait langsung dengan data object.
- Menghindari penulisan echo string berulang di banyak tempat.

Kenapa dibuat method?

- Single Source of Truth: Format output tersentralisasi. Jika format berubah, cukup ubah di satu tempat.
- Abstraksi: Pemanggil tidak perlu tahu cara menyusun string.

Kapan digunakan?

- Saat ada operasi/aksi yang logis “milik” data tersebut (misal hitung total, format tampilan, validasi internal).
- Ketika mulai muncul pola copy–paste format output.

Catatan:

- Return string (bukan echo) memberi fleksibilitas: bisa ditampilkan, digabung, atau dipakai di tempat lain.

### 4. Visibility & Getter

```php
class Mahasiswa {
  private $nim;
  private $nama;
  private $jurusan;

  public function __construct($nim, $nama, $jurusan) {
    $this->nim = $nim;
    $this->nama = $nama;
    $this->jurusan = $jurusan;
  }

  public function getNama() { return $this->nama; }
  public function deskripsi() { return "{$this->nim} - {$this->nama} ({$this->jurusan})"; }
}

$m = new Mahasiswa('10231063', 'Haikal', 'Sistem Informasi');
echo $m->getNama();
```

Penjelasan:

- Properti diubah menjadi `private` agar tidak bisa diakses langsung dari luar class.
- `getNama()` adalah getter: pintu resmi untuk membaca nilai.
- Method `deskripsi()` tetap bisa mengakses karena masih di dalam class.

Kenapa pakai `private`?

- Enkapsulasi: Lindungi data dari perubahan sembarangan (misal `$m->nim = ''`).
- Membuka jalan untuk validasi/aturan tanpa mengubah cara pemakaian luar secara drastis (bisa tambah setter nanti bila perlu).

Kapan gunakan `private`?

- Saat data penting tidak boleh diubah bebas.
- Saat ingin menjaga konsistensi internal object.
- Hampir jadi kebiasaan umum: mulai dengan private, buka akses via getter/setter hanya jika dibutuhkan.

Kenapa tidak selalu public?

- Public = tidak ada kontrol → sulit debug kalau ada perubahan liar.
- Sulit menambah validasi di masa depan tanpa resiko breaking changes.

Catatan lanjutan:

- Jika nanti butuh ubah nama saat ditampilkan (misal kapitalisasi), cukup modifikasi getter.
- Jangan buat getter/setter otomatis kalau belum perlu (avoid over-engineering).

### 5. Array of Objects & Loop

```php
$daftar = [
  new Mahasiswa('10231063', 'Haikal', 'Sistem Informasi'),
  new Mahasiswa('10231073', 'Pangeran', 'Sistem Informasi')
];

foreach ($daftar as $m) {
  echo $m->deskripsi() . "<br>";
}
```

Penjelasan:

- Banyak object disimpan dalam array numerik biasa.
- `foreach` memudahkan iterasi setiap object untuk ditampilkan.

Kenapa bentuk array?

- Struktur paling sederhana untuk kumpulan sejenis.
- Belum perlu struktur kompleks (belum ada pencarian spesifik, filter berat, dsb.).

Kapan digunakan?

- Saat jumlah data masih kecil–menengah dan operasi hanya iterasi sederhana.
- Ketika belum butuh database / collection khusus.

Catatan:

- Jika nanti butuh cari cepat by `nim`, bisa pertimbangkan associative array (`$map[$nim] = $obj`).
- Atau naik level ke penyimpanan persistent (database) minggu-minggu berikut.

### 6.Form Tambah Mahasiswa (Masih Memory Saja)

```php
<?php
class Mahasiswa {
  private $nim;
  private $nama;
  private $jurusan;
  public function __construct($nim, $nama, $jurusan) {
    $this->nim = $nim;
    $this->nama = $nama;
    $this->jurusan = $jurusan;
  }
  public function deskripsi() {
    return "{$this->nim} - {$this->nama} ({$this->jurusan})";
  }
}

$mahasiswa = [
  new Mahasiswa('10231063','Haikal','Sistem Informasi'),
  new Mahasiswa('10231073','Pangeran','Sistem Informasi'),
];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nim = trim($_POST['nim'] ?? '');
  $nama = trim($_POST['nama'] ?? '');
  $jurusan = trim($_POST['jurusan'] ?? '');

  if ($nim === '') $errors['nim'] = 'NIM wajib.';
  if ($nama === '') $errors['nama'] = 'Nama wajib.';
  if ($jurusan === '') $errors['jurusan'] = 'Jurusan wajib.';

  if (empty($errors)) {
    $mahasiswa[] = new Mahasiswa($nim, $nama, $jurusan);
  }
}
?>
<h3>Data Mahasiswa</h3>
<?php foreach ($mahasiswa as $m) : ?>
  <?= $m->deskripsi(); ?><br>
<?php endforeach; ?>

<h3>Tambah Mahasiswa</h3>
<form method="POST">
  NIM: <input name="nim"> <?= $errors['nim'] ?? '' ?><br>
  Nama: <input name="nama"> <?= $errors['nama'] ?? '' ?><br>
  Jurusan: <input name="jurusan"> <?= $errors['jurusan'] ?? '' ?><br>
  <button type="submit">Simpan</button>
</form>
```

Penjelasan:

- Simulasi CRUD sederhana tanpa database: data berada di array selama request berjalan.
- Validasi dasar dilakukan sebelum object baru dibuat.
- Memperlihatkan integrasi OOP (class) dengan alur form procedural sederhana.

Kenapa tetap berguna meski tidak persistent?

- Fokus ke pola: input → validasi → instansiasi object → tampilkan.
- Memudahkan transisi nanti ke penyimpanan database (logik intinya sama, hanya media penyimpanan berubah).

Kapan pola ini digunakan?

- Saat prototyping fitur.
- Saat ingin mendemonstrasikan konsep object tanpa distraksi query SQL.

Keterbatasan:

- Data hilang saat refresh (stateless). Nanti bisa pakai session / database.
- Tidak aman untuk produksi (belum ada sanitasi lanjutan, CSRF, dsb.).

Catatan peningkatan (untuk minggu-minggu lanjut):

- Pisahkan logika ke Controller dan View (MVC dasar).
- Tambah sanitasi `htmlspecialchars` saat output untuk hindari XSS.
- Tambah pengecekan duplikasi NIM.

Ringkas Evolusi Konsep (Leveling):

1. Public property → Mudah, tapi rawan.
2. Constructor → Konsistensi object.
3. Method perilaku → Abstraksi & reuse.
4. Private + getter → Enkapsulasi & kontrol.
5. Array of objects → Koleksi sederhana.
6. Form + validasi → Integrasi ke alur aplikasi nyata.

Mindset yang dibangun:

- Pikirkan “siapa yang boleh ubah data?” (enkapsulasi).
- “Bagaimana agar tidak copy–paste?” (abstraksi via method).
- “Bagaimana menjaga format konsisten?” (centralized method / getter).
- “Bagaimana nanti kalau ditambah fitur?” (desain minim perubahan eksternal).

---

## IV. Tugas

1. Buat class MataKuliah (kode, nama, sks) + method ringkas().
2. Buat class KRS yang menyimpan array MataKuliah dan method:
   - tambah(MataKuliah $mk)
   - totalSks()
   - daftar()
3. Tampilkan:
   - Daftar mata kuliah (kode - nama (sks))
   - Total SKS
4. (Bonus) Validasi: tidak boleh tambah jika total SKS > 24.

---

## V. Checklist

- [ ] Bisa menjelaskan perbedaan class & object.
- [ ] Constructor bekerja.
- [ ] Private properti tidak langsung diakses.
- [ ] Method mengembalikan string deskripsi.
- [ ] Array object berhasil ditampilkan.
- [ ] Tugas KRS selesai.

---

## VI. Troubleshooting

- Cannot access private property → gunakan getter / ubah visibility.
- Undefined variable → pastikan urutan definisi.
- Output kosong → echo hasil method.
- Form tidak menambah data → cek METHOD dan name input.

---

## VII. Arah Minggu Depan

Minggu 4: Inheritance, abstract class, interface, trait, static, autoload sederhana.
