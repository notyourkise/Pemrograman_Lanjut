# Modul Praktikum 4 — OOP Lanjutan (Inheritance, Abstract, Interface, Trait, Static, Autoload)

Durasi: 1 × 120 menit • Prasyarat: Minggu 3 (OOP Dasar)

---

## I. Tujuan

- Memahami inheritance (pewarisan) antar class.
- Menggunakan abstract class & abstract method.
- Mengimplementasikan interface.
- Memakai trait untuk reuse perilaku.
- Memahami properti & method `static`.
- Menggunakan autoload sederhana (`spl_autoload_register`).
- Menerapkan namespace untuk struktur rapi.

---

## II. Ringkasan Konsep

- Inheritance: class Anak mewarisi properti & method dari class Induk (`extends`).
- Abstract Class: tidak bisa di-instansiasi langsung, bisa punya method abstract (harus diimplementasi di turunan) + method biasa.
- Interface: hanya deklarasi method; class bisa mengimplementasikan banyak interface.
- Trait: potongan kode (method / properti) untuk disisipkan ke beberapa class (avoid duplicate code).
- Static: milik class, bukan object; akses via `NamaClass::methodStatic()`.
- Autoload: otomatis memanggil file class saat dibutuhkan tanpa banyak `require`.
- Namespace: mencegah bentrok nama dan mengelompokkan kode.

---

## III. Struktur Folder (Disarankan)

```
Week4/
├─ Week4.md
└─ src/
   ├─ Core/
   │  └─ Autoloader.php
   ├─ Models/
   │  ├─ Person.php
   │  └─ Mahasiswa.php
   ├─ Traits/
   │  └─ CanIntroduce.php
   ├─ Interfaces/
   │  └─ HasIdentity.php
   └─ index.php
```

---

## IV. Langkah Implementasi

### 1. Autoloader Sederhana (`Core/Autoloader.php`)

Registrasi fungsi untuk memetakan namespace ke folder `src`.

### 2. Interface (`Interfaces/HasIdentity.php`)

```php
<?php
namespace App\Interfaces;
interface HasIdentity {
    public function getId(): string;
}
```

### 3. Trait (`Traits/CanIntroduce.php`)

```php
<?php
namespace App\Traits;
trait CanIntroduce {
    public function introduce(): string {
        return "Halo, saya " . ($this->nama ?? 'Anonim');
    }
}
```

### 4. Abstract Class & Inheritance (`Models/Person.php`)

```php
<?php
namespace App\Models;
use App\Interfaces\HasIdentity;
use App\Traits\CanIntroduce;

abstract class Person implements HasIdentity {
    use CanIntroduce; // Trait

    protected string $nama;
    protected string $id;
    protected static int $jumlah = 0; // static counter

    public function __construct(string $id, string $nama) {
        $this->id = $id;
        $this->nama = $nama;
        static::$jumlah++;
    }

    // abstract method
    abstract public function getRole(): string;

    public function getId(): string { return $this->id; }
    public function getNama(): string { return $this->nama; }

    public static function getJumlah(): int { return static::$jumlah; }
}
```

### 5. Class Turunan (`Models/Mahasiswa.php`)

```php
<?php
namespace App\Models;
class Mahasiswa extends Person {
    private string $nim;
    private string $jurusan;

    public function __construct(string $id, string $nama, string $nim, string $jurusan) {
        parent::__construct($id, $nama); // panggil constructor Person
        $this->nim = $nim;
        $this->jurusan = $jurusan;
    }

    public function getRole(): string { return 'Mahasiswa'; }

    public function deskripsi(): string {
        return $this->nim . ' - ' . $this->getNama() . ' (' . $this->jurusan . ')';
    }
}
```

### 6. Index / Demo (`src/index.php`)

- Buat beberapa object `Mahasiswa`.
- Tampilkan hasil trait (`introduce()`), abstract method implementasi (`getRole()`), dan static counter.

### 7. Static Property & Method

Gunakan `Person::getJumlah()` setelah membuat object untuk menampilkan jumlah instance.

---

## V. Contoh Alur Eksekusi

1. `require Autoloader`.
2. Daftarkan autoload.
3. Instansiasi beberapa Mahasiswa.
4. Loop tampilkan: deskripsi + peran + perkenalan.
5. Tampilkan total instance via static method.

---

## VI. Tugas Praktikum

1. Tambah class `Dosen` yang extends `Person` (punya properti `nidn` & `keahlian`).
2. Implementasikan method `deskripsi()` pada `Dosen` menampilkan: `NIDN - Nama (Keahlian)`.
3. Buat interface baru `HasContact` dengan method `getEmail(): string` lalu implement di `Dosen` & `Mahasiswa` (email sederhana: lower(nama)@kampus.ac.id, spasi menjadi titik).
4. Buat trait `CanTeach` dengan method `teach($mataKuliah)` (return string). Gunakan di `Dosen` saja.
5. Tampilkan di index:
   - Daftar semua Person (Mahasiswa & Dosen) dengan role & email.
   - Panggil `teach()` untuk objek `Dosen`.
   - Total instance Person.
6. (Bonus) Tambah class `AsistenDosen` extends `Mahasiswa` + implement interface tambahan buatan kamu sendiri.

---

## VII. Checklist

- [ ] Autoloader bekerja (tidak banyak require manual).
- [ ] Interface `HasIdentity` diimplementasi.
- [ ] Trait `CanIntroduce` berfungsi.
- [ ] Abstract class `Person` tidak di-instansiasi langsung.
- [ ] Class turunan override / implement method abstract.
- [ ] Static counter bertambah sesuai instance.
- [ ] Tugas Dosen + HasContact + CanTeach selesai.

---

## VIII. Troubleshooting

- Class not found → cek namespace & path; pastikan autoloader memetakan benar.
- Trait error → pastikan `use TraitName;` di dalam class.
- Cannot instantiate abstract class → memang tidak boleh, instansiasi class turunan.
- Interface method belum diimplementasi → pastikan semua method interface ada di class.
- Static counter tidak naik → cek apakah increment di constructor.

---

## IX. Arah Minggu Depan

Integrasi konsep OOP dengan penyimpanan data (database) atau pattern lanjutan.
