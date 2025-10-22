# 📘 Week 7 — Keamanan Form: CSRF Protection, Validasi & Sanitasi

**Tema:** Mengamankan aplikasi web dari serangan berbasis form dengan implementasi CSRF token, validasi server-side terstruktur, dan sanitasi input/output.

---

## 🎯 Tujuan Pembelajaran

Setelah mengikuti praktikum ini, mahasiswa mampu:

1. Memahami dan mencegah serangan **CSRF (Cross-Site Request Forgery)**
2. Memahami dan mencegah serangan **XSS (Cross-Site Scripting)**
3. Memahami perbedaan **SQL Injection** dan cara mencegahnya dengan prepared statements
4. Mengimplementasikan **validasi server-side** yang terstruktur dan reusable
5. Menerapkan **sanitasi input** dan **escape output** di semua tampilan
6. Menggunakan **whitelist** untuk parameter yang sensitif (sorting, filtering)

---

## 📚 Materi Pokok

### 1. CSRF (Cross-Site Request Forgery)

**Apa itu CSRF?**  
Serangan yang memaksa user terautentikasi menjalankan aksi tidak diinginkan di aplikasi web.

**Cara Mencegah:**

- Generate token unik di session
- Sisipkan token sebagai hidden field di setiap form POST
- Verifikasi token di server sebelum memproses request

**Implementasi:**

```php
// Generate token
Csrf::generate(); // disimpan di $_SESSION['csrf_token']

// Di form (view)
<?= Csrf::field() ?>
// Output: <input type="hidden" name="csrf_token" value="abc123...">

// Verifikasi di controller
Csrf::verifyOrFail($_POST['csrf_token'] ?? null);
```

---

### 2. XSS (Cross-Site Scripting)

**Apa itu XSS?**  
Serangan yang menyisipkan script berbahaya ke halaman web yang dilihat user lain.

**Cara Mencegah:**

- **ESCAPE semua output** dengan `htmlspecialchars()`
- Jangan langsung echo `$_POST` atau `$_GET` tanpa escape
- Gunakan Content Security Policy (CSP) jika diperlukan

**Implementasi:**

```php
// SALAH ❌
<h1>Selamat datang <?= $_GET['name'] ?></h1>

// BENAR ✅
<h1>Selamat datang <?= htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8') ?></h1>

// Atau pakai helper
<h1>Selamat datang <?= Sanitizer::escape($_GET['name']) ?></h1>
```

---

### 3. SQL Injection

**Apa itu SQL Injection?**  
Serangan yang menyisipkan query SQL berbahaya melalui input user.

**Cara Mencegah:**

- **Gunakan prepared statements** (sudah diterapkan di Week 6)
- Jangan pernah concat string SQL dengan input user

**Implementasi (sudah benar):**

```php
// SALAH ❌
$sql = "SELECT * FROM patients WHERE name = '" . $_GET['q'] . "'";

// BENAR ✅ (prepared statement)
$stmt = $db->prepare("SELECT * FROM patients WHERE name LIKE ?");
$like = '%' . $_GET['q'] . '%';
$stmt->bind_param('s', $like);
```

---

### 4. Validasi Server-Side Terstruktur

**Mengapa perlu?**

- Client-side validation (HTML5, JavaScript) mudah dilewati
- Server-side adalah sumber kebenaran
- Mencegah data invalid masuk ke database

**Implementasi dengan Validator Class:**

```php
$validator = new Validator();
$validator
    ->required('name', $name, 'Nama')
    ->minLength('name', $name, 3, 'Nama')
    ->maxLength('name', $name, 100, 'Nama')
    ->date('dob', $dob, 'Tanggal Lahir')
    ->notFutureDate('dob', $dob, 'Tanggal Lahir')
    ->phone('phone', $phone, 'Telepon')
    ->unique('phone', $phone, 'patients', 'phone', 'Nomor Telepon');

if ($validator->fails()) {
    $errors = $validator->getErrors();
    // Tampilkan pesan error
}
```

---

### 5. Whitelist untuk Sorting & Filtering

**Mengapa perlu?**  
User bisa manipulasi parameter `sort` untuk melakukan SQL injection atau error.

**Implementasi:**

```php
// Whitelist kolom yang boleh di-sort
$allowedSort = ['id', 'name', 'gender', 'dob', 'created_at'];
$sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'id';

// Validasi direction
$dir = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
```

---

## ⚙️ Langkah Praktikum

### Setup Awal

1. **Copy project Week 6 ke Week 7**

   ```powershell
   robocopy "Week6" "Week7" /E
   ```

2. **Buat folder helpers**

   ```
   Week7/app/helpers/
   ```

3. **Buat 3 helper class:**
   - `Csrf.php` - CSRF protection
   - `Validator.php` - Validasi server-side
   - `Sanitizer.php` - Sanitasi input/output

### Langkah 1: Implementasi CSRF Protection

1. **Tambahkan `<?= Csrf::field() ?>` di SEMUA form POST:**

   - `patients/create.php`
   - `patients/edit.php`
   - `patients/index.php` (modal delete)
   - `appointments/create.php`
   - `appointments/edit.php`
   - `appointments/index.php` (modal delete)

2. **Tambahkan verifikasi di SEMUA controller action POST:**
   ```php
   public function create(): void {
       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           // Verifikasi CSRF token
           Csrf::verifyOrFail($_POST['csrf_token'] ?? null);

           // ...proses data
       }
   }
   ```

### Langkah 2: Implementasi Validasi Terstruktur

1. **Ganti validasi manual dengan Validator class:**

   ```php
   // SEBELUM (Week 6)
   if($name === '' || strlen($name) < 3 || strlen($name) > 100) {
       $errors['name'] = 'Nama wajib 3-100 karakter.';
   }

   // SESUDAH (Week 7)
   $validator = new Validator();
   $validator
       ->required('name', $name, 'Nama')
       ->minLength('name', $name, 3, 'Nama')
       ->maxLength('name', $name, 100, 'Nama');

   $errors = $validator->getErrors();
   ```

### Langkah 3: Escape Output di Views

1. **Pastikan SEMUA output menggunakan `htmlspecialchars()`:**

   ```php
   // SUDAH BENAR ✅ (Week 6 sudah escape)
   <?= htmlspecialchars($patient['name'], ENT_QUOTES, 'UTF-8') ?>

   // Atau pakai helper Sanitizer
   <?= Sanitizer::escape($patient['name']) ?>
   ```

### Langkah 4: Tambah Fitur Recycle Bin

1. **Update PatientRepository:**

   - Method `countDeleted()` - hitung pasien terhapus
   - Method `getDeleted()` - ambil daftar pasien terhapus
   - Method `restore($id)` - restore pasien

2. **Tambah method di PatientsController:**

   - `recycle()` - tampilkan daftar terhapus
   - `restore()` - pulihkan pasien
   - `forceDelete()` - hapus permanen

3. **Buat view `patients/recycle.php`**

### Langkah 5: Whitelist Sorting

1. **Update index() di semua controller:**
   ```php
   $allowedSort = ['id', 'name', 'gender', 'dob', 'created_at'];
   $sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'id';
   $dir = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
   ```

---

## 🧠 Tugas Minggu 7

### Wajib (100%)

1. **Implementasi CSRF Protection (25%)**

   - Tambahkan `Csrf::field()` di semua form POST (min. 4 form)
   - Tambahkan `Csrf::verifyOrFail()` di semua controller POST action
   - Screenshot form dengan inspect element menunjukkan hidden CSRF token

2. **Implementasi Validator Class (25%)**

   - Gunakan Validator class di min. 2 controller (Patients & Appointments)
   - Min. 5 aturan validasi berbeda (required, minLength, date, unique, phone, dll)
   - Screenshot pesan error validasi saat submit form invalid

3. **Escape Output (15%)**

   - Pastikan semua output di view menggunakan `htmlspecialchars()` atau `Sanitizer::escape()`
   - Tidak boleh ada `<?= $_POST['...'] ?>` atau `<?= $_GET['...'] ?>` tanpa escape

4. **Whitelist Sorting (10%)**

   - Implementasi whitelist kolom sort di semua halaman list
   - Validasi direction (ASC/DESC only)

5. **Recycle Bin (15%)**

   - Halaman recycle bin untuk melihat data terhapus
   - Fitur restore (pulihkan) data
   - Fitur force delete (hapus permanen)

6. **Laporan Testing Keamanan (10%)**
   - Test 3 payload berbahaya dan dokumentasikan hasilnya:
     1. **XSS Test:** Input `<script>alert('XSS')</script>` di field nama → harus tampil sebagai teks biasa
     2. **CSRF Test:** Submit form dari luar aplikasi tanpa token → harus ditolak 403
     3. **SQL Injection Test:** Input `' OR '1'='1` di pencarian → tidak bocor data
   - Screenshot before/after mitigasi

---

## 📝 Deliverables

1. **Kode aplikasi lengkap** (Week 7 dengan semua fitur keamanan)
2. **Dump SQL terbaru** (jika ada perubahan skema)
3. **Laporan PDF/Markdown** berisi:
   - Penjelasan implementasi CSRF (kode + screenshot)
   - Penjelasan Validator class (kode + contoh error message)
   - Hasil test case keamanan (3 payload + screenshot)
   - Screenshot Recycle Bin
4. **4-6 Screenshot:**
   - Form dengan CSRF token (inspect element)
   - Pesan validasi error
   - Test XSS gagal (script tampil sebagai teks)
   - Halaman Recycle Bin
   - Form restore/force delete

---

## 📊 Rubrik Penilaian (100 poin)

| Aspek             | Bobot | Kriteria                                                                      |
| ----------------- | ----- | ----------------------------------------------------------------------------- |
| CSRF Protection   | 25    | Token di semua form POST, verifikasi di controller, regenerate setelah submit |
| Validator Class   | 25    | Min. 2 controller, 5+ aturan, error message jelas                             |
| Output Escaping   | 15    | Semua output di-escape, tidak ada XSS vulnerability                           |
| Whitelist Sorting | 10    | Kolom sort di-whitelist, direction divalidasi                                 |
| Recycle Bin       | 15    | List, restore, force delete berfungsi dengan CSRF                             |
| Laporan Testing   | 10    | 3 test case keamanan terdokumentasi dengan screenshot                         |

---

## 🧪 Test Case Keamanan

### Test 1: XSS Prevention

**Payload:**

```html
<script>
  alert("XSS Attack!");
</script>
```

**Langkah:**

1. Buka form tambah pasien
2. Input payload di field "Nama"
3. Submit form
4. Lihat di halaman list

**Ekspektasi:**

- ✅ Script ditampilkan sebagai teks biasa: `<script>alert('XSS Attack!')</script>`
- ❌ **BUKAN** muncul popup alert

**Dokumentasi:**

- Screenshot form input dengan payload
- Screenshot hasil tampilan (script sebagai teks)

---

### Test 2: CSRF Protection

**Payload:**  
Form HTML external tanpa CSRF token

**Langkah:**

1. Buat file `csrf-test.html` di luar aplikasi:
   ```html
   <form
     method="POST"
     action="http://localhost/MATERI-ASDOS/Week7/public/?c=patients&a=create"
   >
     <input name="name" value="Hacker" />
     <input name="gender" value="M" />
     <button>Submit</button>
   </form>
   ```
2. Buka file, klik Submit

**Ekspektasi:**

- ✅ Request ditolak dengan status 403
- ✅ Pesan: "CSRF token verification failed"
- ❌ **BUKAN** data tersimpan ke database

**Dokumentasi:**

- Screenshot form external
- Screenshot error 403

---

### Test 3: SQL Injection Prevention

**Payload:**

```
' OR '1'='1
```

**Langkah:**

1. Buka halaman list pasien
2. Input payload di search box
3. Submit

**Ekspektasi:**

- ✅ Query tetap aman (prepared statements)
- ✅ Hasil pencarian kosong atau tidak bocor semua data
- ❌ **BUKAN** tampil semua data pasien

**Dokumentasi:**

- Screenshot search dengan payload
- Screenshot hasil (tidak bocor data)

---

## 🔧 Troubleshooting

### 1. Session sudah dimulai

**Error:**

```
Warning: session_start(): Session already started
```

**Solusi:**

```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

### 2. CSRF token tidak cocok setelah refresh

**Penyebab:**  
Token di-regenerate sebelum form selesai disubmit

**Solusi:**  
Regenerate token **HANYA** setelah operasi berhasil, bukan di setiap page load.

### 3. Validator tidak recognize Database::getConnection()

**Solusi:**  
Pastikan file `Database.php` sudah di-require di `public/index.php` sebelum controller dipanggil.

---

## 📚 Referensi

- [OWASP CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [PHP htmlspecialchars()](https://www.php.net/manual/en/function.htmlspecialchars.php)
- [PHP Prepared Statements](https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php)

---

## 💡 Tips

1. **CSRF token wajib di SEMUA form POST**, termasuk modal delete
2. **Escape output di SEMUA view**, jangan skip satu pun
3. **Prepared statements sudah aman** dari SQL injection, jangan concat string SQL
4. **Whitelist > Blacklist** untuk validasi parameter
5. **Server-side validation** adalah yang utama, client-side hanya UX enhancement

---

## 📦 Struktur File Week 7

```
Week7/
├── app/
│   ├── config.php
│   ├── controllers/
│   │   ├── PatientsController.php (UPDATED - CSRF + Validator)
│   │   └── AppointmentsController.php (UPDATED - CSRF + Validator)
│   ├── core/
│   │   ├── Autoloader.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Flash.php
│   │   └── Paginator.php
│   ├── helpers/              (NEW)
│   │   ├── Csrf.php          (NEW)
│   │   ├── Validator.php     (NEW)
│   │   └── Sanitizer.php     (NEW)
│   ├── repositories/
│   │   ├── PatientRepository.php (UPDATED - restore, getDeleted)
│   │   ├── AppointmentRepository.php
│   │   └── DoctorRepository.php
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── patients/
│       │   ├── index.php (UPDATED - link recycle bin)
│       │   ├── create.php (UPDATED - CSRF token)
│       │   ├── edit.php (UPDATED - CSRF token)
│       │   └── recycle.php (NEW)
│       └── appointments/
│           ├── index.php (UPDATED - CSRF token)
│           ├── create.php (UPDATED - CSRF token)
│           └── edit.php (UPDATED - CSRF token)
├── public/
│   ├── index.php (UPDATED - require helpers)
│   └── assets/
│       └── styles.css
└── Week7.md (dokumentasi ini)
```

---

**Prepared by:** Tim Asisten Pemrograman Lanjut  
**Last updated:** October 2025  
**Version:** 1.0
