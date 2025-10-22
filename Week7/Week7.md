# Week 7: Advanced OOP Design Patterns

## 📚 Overview

Minggu ini kita akan mempelajari **Design Patterns** yang paling umum digunakan dalam pengembangan aplikasi berbasis OOP. Kita akan melihat bagaimana pattern-pattern ini diterapkan dalam konteks web development dengan PHP.

**Fokus Pembelajaran:**

- Builder Pattern (35%) - Chainable validation
- Factory Pattern (25%) - Data generation
- Repository Extension (20%) - Soft delete & recycle bin
- Helper Classes (10%) - Sanitization utilities
- Security Bonus (10%) - CSRF protection

---

## 🎯 Learning Objectives

Setelah menyelesaikan minggu ini, mahasiswa diharapkan dapat:

1. **Memahami** konsep Design Patterns dan kegunaannya
2. **Mengimplementasikan** Builder Pattern untuk validation
3. **Mengimplementasikan** Factory Pattern untuk data generation
4. **Meng-extend** Repository Pattern dengan fitur tambahan
5. **Menerapkan** SOLID principles dalam kode
6. **Bonus:** Memahami dasar-dasar web security (CSRF)

---

## 📖 Materi

### 1. Introduction to Design Patterns (10 menit)

**Apa itu Design Pattern?**

Design Pattern adalah solusi umum yang dapat digunakan kembali untuk masalah yang sering muncul dalam software design. Pattern bukan kode yang bisa langsung di-copy-paste, tapi template untuk menyelesaikan masalah dalam berbagai situasi.

**Mengapa Design Patterns Penting?**

- ✅ **Reusability** - Solusi terbukti yang bisa digunakan berulang kali
- ✅ **Maintainability** - Kode lebih mudah dipahami dan di-maintain
- ✅ **Communication** - Vocabulary bersama untuk developer
- ✅ **Best Practices** - Menerapkan cara terbaik yang sudah teruji

**Kategori Design Patterns:**

1. **Creational Patterns** - Cara membuat object (Factory, Builder, Singleton)
2. **Structural Patterns** - Cara menyusun class/object (Adapter, Decorator, Facade)
3. **Behavioral Patterns** - Cara object berinteraksi (Observer, Strategy, Command)

**Bridge dari Week 6 ke Week 7:**

Week 6 kita sudah implement Repository Pattern untuk data access. Week 7 ini kita akan:

- **Extend** Repository dengan fitur baru (Recycle Bin)
- **Add** Builder Pattern untuk validation
- **Add** Factory Pattern untuk data generation
- Semua tetap follow SOLID principles dari Week 6!

---

### 2. Builder Pattern - Validator Class (35%)

**Problem Statement:**

```php
// Tanpa Builder Pattern - kode jadi panjang dan tidak elegant
$errors = [];

if (empty($_POST['name'])) {
    $errors['name'] = 'Name is required';
}

if (strlen($_POST['name']) < 3) {
    $errors['name'] = 'Name must be at least 3 characters';
}

if (strlen($_POST['name']) > 100) {
    $errors['name'] = 'Name must not exceed 100 characters';
}

if (empty($_POST['email'])) {
    $errors['email'] = 'Email is required';
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format';
}

// ... dan seterusnya untuk setiap field
```

**Masalah:**

- 😞 Repetitive - copy-paste yang sama berkali-kali
- 😞 Hard to read - banyak if-else bersarang
- 😞 Hard to maintain - perubahan rules harus update di banyak tempat
- 😞 Not reusable - validation logic tersebar di setiap controller

**Solution: Builder Pattern**

Builder Pattern memungkinkan kita membuat object complex secara bertahap dengan syntax yang clean dan readable.

**Karakteristik Builder Pattern:**

- ✅ **Method chaining** (fluent interface) - methods return `$this`
- ✅ **Step-by-step construction** - build object secara bertahap
- ✅ **Readable dan expressive** - code dibaca seperti bahasa natural
- ✅ **Separation of concerns** - validation logic terpisah dari controller

**Implementation:**

File: `app/helpers/Validator.php`

```php
<?php

class Validator
{
    private array $data = [];
    private array $errors = [];
    private string $currentField = '';

    /**
     * Create new validator instance
     * Static factory method untuk create instance
     */
    public static function make(array $data): self
    {
        $validator = new self();
        $validator->data = $data;
        return $validator;
    }

    /**
     * Set field yang akan divalidasi
     * Method ini memungkinkan chaining
     *
     * @return self - Return $this untuk chaining
     */
    public function field(string $name): self
    {
        $this->currentField = $name;
        return $this; // Key untuk method chaining!
    }

    /**
     * Validasi required
     *
     * @return self - Return $this untuk chaining
     */
    public function required(string $message = null): self
    {
        $value = $this->data[$this->currentField] ?? null;

        if (empty($value) && $value !== '0') {
            $this->errors[$this->currentField] = $message ??
                ucfirst($this->currentField) . ' is required';
        }

        return $this; // Chaining!
    }

    /**
     * Validasi minimum length
     * Skip jika sudah ada error untuk field ini
     *
     * @return self - Return $this untuk chaining
     */
    public function minLength(int $min, string $message = null): self
    {
        // Skip validasi jika sudah ada error
        if (isset($this->errors[$this->currentField])) {
            return $this;
        }

        $value = $this->data[$this->currentField] ?? '';

        if (strlen($value) < $min) {
            $this->errors[$this->currentField] = $message ??
                ucfirst($this->currentField) . " must be at least {$min} characters";
        }

        return $this;
    }

    /**
     * Validasi maximum length
     */
    public function maxLength(int $max, string $message = null): self
    {
        if (isset($this->errors[$this->currentField])) {
            return $this;
        }

        $value = $this->data[$this->currentField] ?? '';

        if (strlen($value) > $max) {
            $this->errors[$this->currentField] = $message ??
                ucfirst($this->currentField) . " must not exceed {$max} characters";
        }

        return $this;
    }

    /**
     * Validasi email format
     */
    public function email(string $message = null): self
    {
        if (isset($this->errors[$this->currentField])) {
            return $this;
        }

        $value = $this->data[$this->currentField] ?? '';

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$this->currentField] = $message ??
                'Invalid email format';
        }

        return $this;
    }

    /**
     * Validasi phone number (Indonesia format)
     */
    public function phone(string $message = null): self
    {
        if (isset($this->errors[$this->currentField])) {
            return $this;
        }

        $value = $this->data[$this->currentField] ?? '';

        // Format: 08xxxxxxxxxx (10-13 digits)
        if (!preg_match('/^08\d{8,11}$/', $value)) {
            $this->errors[$this->currentField] = $message ??
                'Invalid phone number format (must start with 08)';
        }

        return $this;
    }

    /**
     * Validasi date format (YYYY-MM-DD)
     */
    public function date(string $message = null): self
    {
        if (isset($this->errors[$this->currentField])) {
            return $this;
        }

        $value = $this->data[$this->currentField] ?? '';

        $date = \DateTime::createFromFormat('Y-m-d', $value);
        $isValid = $date && $date->format('Y-m-d') === $value;

        if (!$isValid) {
            $this->errors[$this->currentField] = $message ??
                'Invalid date format (must be YYYY-MM-DD)';
        }

        return $this;
    }

    /**
     * Check apakah validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check apakah validation failed
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
```

**Usage Example:**

```php
// File: app/controllers/PatientsController.php

public function store()
{
    // Builder Pattern in action! 🎯
    $validator = Validator::make($_POST)
        ->field('name')
            ->required()
            ->minLength(3)
            ->maxLength(100)
        ->field('email')
            ->required()
            ->email()
        ->field('phone')
            ->required()
            ->phone()
        ->field('date_of_birth')
            ->required()
            ->date();

    if ($validator->fails()) {
        // Validation failed
        Flash::error('Please fix the errors below');
        return $this->create($validator->errors());
    }

    // Validation passed - proceed to save
    $data = Sanitizer::cleanArray($_POST);
    $this->repository->create($data);

    Flash::success('Patient created successfully');
    header('Location: /patients');
}
```

**Perhatikan bagaimana code menjadi:**

- ✅ **Self-documenting** - Jelas field apa yang divalidasi
- ✅ **Easy to read** - Dibaca dari atas ke bawah
- ✅ **Easy to modify** - Tinggal tambah/hapus method
- ✅ **Reusable** - Bisa digunakan di semua controller

**Konsep OOP yang Diterapkan:**

1. **Method Chaining** - Setiap method return `$this`
2. **Fluent Interface** - API yang mudah dibaca
3. **Encapsulation** - Validation logic tersembunyi dalam class
4. **Single Responsibility** - Validator hanya handle validation
5. **Static Factory Method** - `make()` untuk create instance

**Latihan:**

Tambahkan validation rules berikut ke Validator class:

1. `numeric()` - Validasi nilai numerik
2. `between($min, $max)` - Validasi range angka
3. `in($values)` - Validasi value harus dalam array
4. `unique($table, $column)` - Validasi uniqueness di database
5. `confirmed($field)` - Validasi field confirmation (password)

---

### 3. Factory Pattern - PatientFactory Class (25%)

**Problem Statement:**

Untuk testing atau seeding database, kita sering butuh banyak data dummy. Membuat data manual sangat repetitive dan error-prone.

```php
// Tanpa Factory Pattern - repetitive! 😫
$patient1 = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '081234567890',
    'date_of_birth' => '1990-01-01',
    'gender' => 'male',
    'address' => 'Jl. Merdeka No. 1',
    'city' => 'Jakarta',
    'province' => 'DKI Jakarta',
    'postal_code' => '12345',
    'emergency_contact' => '081234567891',
    'blood_type' => 'O',
    'allergies' => 'None',
];

$patient2 = [
    'name' => 'Jane Smith',
    'email' => 'jane@example.com',
    'phone' => '081234567892',
    // ... copy-paste 10 field lagi
];

// Repeat 50 kali? 😱
```

**Masalah:**

- 😞 **Repetitive** - Harus type ulang struktur yang sama
- 😞 **Error-prone** - Typo field name, format salah
- 😞 **Hard to maintain** - Ubah struktur harus update semua
- 😞 **Unrealistic data** - Nama/data tidak realistis

**Solution: Factory Pattern**

Factory Pattern menyediakan interface untuk membuat object tanpa harus specify exact class-nya. Dalam kasus ini, kita gunakan Factory untuk generate data dummy secara otomatis dengan data yang realistis.

**Karakteristik Factory Pattern:**

- ✅ **Encapsulation of object creation** - Logic pembuatan object tersentralisasi
- ✅ **Centralized creation logic** - Satu tempat untuk semua creation logic
- ✅ **Support variations** - Bisa override/customize data
- ✅ **Consistent data structure** - Struktur selalu sama

**Implementation:**

File: `app/factories/PatientFactory.php`

```php
<?php

class PatientFactory
{
    // Static data untuk generate nama realistis
    private static array $firstNamesMale = [
        'Ahmad', 'Budi', 'Dedi', 'Eko', 'Fajar', 'Gilang', 'Hadi', 'Irfan',
        'Joko', 'Kurniawan', 'Lukman', 'Muhamad', 'Nanda', 'Omar', 'Putra'
    ];

    private static array $firstNamesFemale = [
        'Ani', 'Bunga', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hani', 'Indah',
        'Jasmine', 'Kartika', 'Lina', 'Mega', 'Nur', 'Olivia', 'Putri'
    ];

    private static array $lastNames = [
        'Pratama', 'Wijaya', 'Santoso', 'Kusuma', 'Putra', 'Saputra',
        'Setiawan', 'Utama', 'Rahman', 'Hidayat', 'Nugroho', 'Firmansyah'
    ];

    private static array $streets = [
        'Jl. Merdeka', 'Jl. Sudirman', 'Jl. Gatot Subroto', 'Jl. Ahmad Yani',
        'Jl. Diponegoro', 'Jl. Veteran', 'Jl. Pahlawan', 'Jl. Asia Afrika'
    ];

    private static array $cities = [
        'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang',
        'Medan', 'Makassar', 'Palembang', 'Malang', 'Denpasar'
    ];

    private static array $bloodTypes = ['A', 'B', 'AB', 'O'];

    private static array $allergies = [
        'None', 'Penicillin', 'Aspirin', 'Seafood', 'Peanuts',
        'Dust', 'Pollen', 'Pet Dander'
    ];

    /**
     * Create single patient with optional overrides
     *
     * @param array $overrides Data yang ingin di-override
     * @return array Patient data
     *
     * Example:
     *   PatientFactory::create(['name' => 'Test Patient'])
     */
    public static function create(array $overrides = []): array
    {
        // Random gender
        $gender = $overrides['gender'] ?? (rand(0, 1) === 0 ? 'male' : 'female');

        // Generate nama berdasarkan gender
        $firstName = $gender === 'male'
            ? self::$firstNamesMale[array_rand(self::$firstNamesMale)]
            : self::$firstNamesFemale[array_rand(self::$firstNamesFemale)];

        $lastName = self::$lastNames[array_rand(self::$lastNames)];
        $name = $firstName . ' ' . $lastName;

        // Generate email dari nama
        $emailName = strtolower(str_replace(' ', '.', $name));
        $email = $emailName . rand(1, 999) . '@example.com';

        // Generate data lengkap dengan defaults
        $defaults = [
            'name' => $name,
            'email' => $email,
            'phone' => '08' . rand(1000000000, 9999999999),
            'date_of_birth' => self::randomDate('1950-01-01', '2005-12-31'),
            'gender' => $gender,
            'address' => self::$streets[array_rand(self::$streets)] . ' No. ' . rand(1, 200),
            'city' => self::$cities[array_rand(self::$cities)],
            'province' => 'Jawa Barat',
            'postal_code' => (string) rand(10000, 99999),
            'emergency_contact' => '08' . rand(1000000000, 9999999999),
            'blood_type' => self::$bloodTypes[array_rand(self::$bloodTypes)],
            'allergies' => self::$allergies[array_rand(self::$allergies)],
        ];

        // Merge defaults dengan overrides
        // Overrides akan replace defaults
        return array_merge($defaults, $overrides);
    }

    /**
     * Create multiple patients
     *
     * @param int $count Jumlah patient yang ingin dibuat
     * @return array Array of patient data
     *
     * Example:
     *   $patients = PatientFactory::createMany(50);
     */
    public static function createMany(int $count): array
    {
        $patients = [];

        for ($i = 0; $i < $count; $i++) {
            $patients[] = self::create();
        }

        return $patients;
    }

    /**
     * Create patient dengan umur spesifik
     *
     * @param int $age Umur patient
     * @return array Patient data
     *
     * Example:
     *   $senior = PatientFactory::createWithAge(70);
     */
    public static function createWithAge(int $age): array
    {
        $birthYear = date('Y') - $age;
        $birthMonth = str_pad((string) rand(1, 12), 2, '0', STR_PAD_LEFT);
        $birthDay = str_pad((string) rand(1, 28), 2, '0', STR_PAD_LEFT);
        $birthDate = "{$birthYear}-{$birthMonth}-{$birthDay}";

        return self::create([
            'date_of_birth' => $birthDate
        ]);
    }

    /**
     * Create patients berdasarkan gender
     *
     * @param string $gender 'male' atau 'female'
     * @param int $count Jumlah patient
     * @return array Array of patient data
     *
     * Example:
     *   $females = PatientFactory::createByGender('female', 10);
     */
    public static function createByGender(string $gender, int $count = 1): array
    {
        $patients = [];

        for ($i = 0; $i < $count; $i++) {
            $patients[] = self::create(['gender' => $gender]);
        }

        return $patients;
    }

    /**
     * Helper: Generate random date dalam range
     *
     * @param string $start Start date (YYYY-MM-DD)
     * @param string $end End date (YYYY-MM-DD)
     * @return string Random date (YYYY-MM-DD)
     */
    private static function randomDate(string $start, string $end): string
    {
        $startTimestamp = strtotime($start);
        $endTimestamp = strtotime($end);
        $randomTimestamp = rand($startTimestamp, $endTimestamp);

        return date('Y-m-d', $randomTimestamp);
    }
}
```

**Usage Example 1: Seeding Database**

File: `public/seed.php`

```php
<?php

// Load dependencies
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/core/Autoloader.php';
require __DIR__ . '/../app/repositories/PatientRepository.php';
require __DIR__ . '/../app/factories/PatientFactory.php';

$config = require __DIR__ . '/../app/config.php';

echo "🌱 Starting seed...\n\n";

try {
    $repo = new PatientRepository();

    // Generate 50 patients menggunakan Factory! 🎯
    $count = 50;
    echo "📦 Generating {$count} patients...\n";
    $patients = PatientFactory::createMany($count);

    // Insert to database
    $success = 0;
    $failed = 0;

    foreach ($patients as $index => $patient) {
        try {
            $repo->create($patient);
            $success++;

            // Progress indicator
            if (($index + 1) % 10 === 0) {
                echo "   ✓ Inserted " . ($index + 1) . " patients\n";
            }
        } catch (Exception $e) {
            $failed++;
            echo "   ✗ Failed: " . $patient['name'] . "\n";
        }
    }

    echo "\n✅ Seeding completed!\n";
    echo "   • Success: {$success}\n";
    echo "   • Failed: {$failed}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
```

**Menjalankan seed:**

```bash
php public/seed.php
```

**Usage Example 2: Testing**

```php
// Create patient dengan data spesifik
$testPatient = PatientFactory::create([
    'name' => 'Test Patient',
    'email' => 'test@example.com'
]);

// Create patient senior (70 tahun)
$senior = PatientFactory::createWithAge(70);

// Create 10 female patients
$femalePatients = PatientFactory::createByGender('female', 10);

// Create patient dengan blood type spesifik
$oPositive = PatientFactory::create([
    'blood_type' => 'O',
    'allergies' => 'None'
]);
```

**Keuntungan Factory Pattern:**

✅ **DRY (Don't Repeat Yourself)** - Tidak perlu repeat struktur data  
✅ **Consistent** - Semua data punya struktur yang sama  
✅ **Flexible** - Bisa override field tertentu saja  
✅ **Realistic Data** - Data dummy yang realistis  
✅ **Testable** - Mudah generate test data  
✅ **Maintainable** - Satu tempat untuk update struktur

**Konsep OOP yang Diterapkan:**

1. **Static Methods** - Tidak perlu instantiate class
2. **Encapsulation** - Logic generation tersembunyi
3. **Default Values** - Template untuk data standar
4. **Overriding** - Customize data sesuai kebutuhan
5. **Array Merge** - Combine defaults dengan overrides

**Latihan:**

Buat `AppointmentFactory` dengan methods berikut:

1. `create($overrides = [])` - Generate single appointment
2. `createMany($count)` - Generate multiple appointments
3. `createForPatient($patientId, $count)` - Appointments untuk patient tertentu
4. `createForDoctor($doctorId, $count)` - Appointments untuk doctor tertentu
5. `createInDateRange($start, $end, $count)` - Appointments dalam range tanggal

---

### 4. Repository Pattern Extension (20%)

Kita sudah belajar Repository Pattern di Week 6 untuk CRUD operations. Sekarang kita akan **extend** repository dengan fitur tambahan: **Recycle Bin** untuk soft-deleted records.

**Concept: Soft Delete**

Soft delete = menandai record sebagai deleted **tanpa** menghapus dari database secara fisik.

**Mengapa Soft Delete?**

✅ **Safety** - Data tidak langsung hilang permanen  
✅ **Recoverable** - Bisa restore data yang terhapus  
✅ **Audit Trail** - Bisa track kapan data dihapus  
✅ **User Friendly** - User bisa undo mistake  
✅ **Legal Compliance** - Beberapa industri require data retention

**Hard Delete vs Soft Delete:**

```sql
-- Hard Delete (permanent)
DELETE FROM patients WHERE id = 1;
-- Data hilang permanen! ❌

-- Soft Delete (recoverable)
UPDATE patients SET deleted_at = NOW() WHERE id = 1;
-- Data masih ada, cuma di-hide ✅
```

**Database Schema:**

```sql
-- Tambah column deleted_at
ALTER TABLE patients
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Regular query (hide deleted records)
SELECT * FROM patients WHERE deleted_at IS NULL;

-- Recycle bin (show only deleted records)
SELECT * FROM patients WHERE deleted_at IS NOT NULL;

-- Restore (set deleted_at back to NULL)
UPDATE patients SET deleted_at = NULL WHERE id = 1;

-- Force delete (permanent delete)
DELETE FROM patients WHERE id = 1;
```

**Implementation:**

File: `app/repositories/PatientRepository.php`

```php
<?php

class PatientRepository
{
    // ... existing methods (getAll, getById, create, update, delete) ...

    /**
     * Count soft-deleted patients
     * Untuk pagination di recycle bin
     */
    public function countDeleted(): int
    {
        $result = $this->db->query(
            "SELECT COUNT(*) as count
             FROM patients
             WHERE deleted_at IS NOT NULL"
        );

        return (int) $result->fetch_assoc()['count'];
    }

    /**
     * Get soft-deleted patients (for recycle bin)
     *
     * @param int $limit Records per page
     * @param int $offset Starting record
     * @return array Deleted patients
     */
    public function getDeleted(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM patients
             WHERE deleted_at IS NOT NULL
             ORDER BY deleted_at DESC
             LIMIT ? OFFSET ?"
        );

        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Restore soft-deleted patient
     * Set deleted_at back to NULL
     *
     * @param int $id Patient ID
     * @return bool Success status
     */
    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE patients
             SET deleted_at = NULL
             WHERE id = ? AND deleted_at IS NOT NULL"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    /**
     * Permanent delete (force delete)
     * DELETE record dari database
     *
     * @param int $id Patient ID
     * @return bool Success status
     */
    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM patients WHERE id = ?"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }
}
```

**Controller Implementation:**

File: `app/controllers/PatientsController.php`

```php
/**
 * Show recycle bin (soft-deleted patients)
 */
public function recycle()
{
    $page = (int) ($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Get deleted patients dengan pagination
    $patients = $this->repository->getDeleted($limit, $offset);
    $total = $this->repository->countDeleted();

    $this->render('patients/recycle', [
        'patients' => $patients,
        'paginator' => new Paginator($total, $page, $limit)
    ]);
}

/**
 * Restore soft-deleted patient
 */
public function restore()
{
    $id = (int) $_POST['id'];

    if ($this->repository->restore($id)) {
        Flash::success('Patient restored successfully');
    } else {
        Flash::error('Failed to restore patient');
    }

    header('Location: /patients/recycle');
    exit;
}

/**
 * Permanent delete patient
 */
public function forceDelete()
{
    $id = (int) $_POST['id'];

    // Konfirmasi extra untuk permanent delete
    if ($this->repository->forceDelete($id)) {
        Flash::success('Patient permanently deleted');
    } else {
        Flash::error('Failed to delete patient');
    }

    header('Location: /patients/recycle');
    exit;
}
```

**View Implementation:**

File: `app/views/patients/recycle.php`

```php
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>🗑️ Recycle Bin - Deleted Patients</h1>

    <a href="/patients" class="btn btn-secondary">← Back to Patients</a>

    <?php if (empty($patients)): ?>
        <p class="alert alert-info">Recycle bin is empty</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Deleted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= $patient['id'] ?></td>
                    <td><?= Sanitizer::escape($patient['name']) ?></td>
                    <td><?= Sanitizer::escape($patient['email']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($patient['deleted_at'])) ?></td>
                    <td>
                        <!-- Restore -->
                        <form method="POST" action="/patients/restore" style="display:inline">
                            <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">
                                ↻ Restore
                            </button>
                        </form>

                        <!-- Force Delete -->
                        <button class="btn btn-danger btn-sm"
                                onclick="confirmDelete(<?= $patient['id'] ?>)">
                            🗑️ Delete Forever
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?= $paginator->render() ?>
    <?php endif; ?>
</div>

<!-- Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>⚠️ Confirm Permanent Delete</h3>
        <p>This action <strong>cannot be undone</strong>. The patient will be permanently deleted.</p>
        <form id="deleteForm" method="POST" action="/patients/force-delete">
            <input type="hidden" name="id" id="deleteId">
            <button type="submit" class="btn btn-danger">Delete Forever</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
```

**Routing:**

File: `public/index.php`

```php
// Recycle bin routes
case 'GET /patients/recycle':
    $patientsController->recycle();
    break;

case 'POST /patients/restore':
    $patientsController->restore();
    break;

case 'POST /patients/force-delete':
    $patientsController->forceDelete();
    break;
```

**Keuntungan Repository Extension:**

✅ **Non-breaking** - Tidak break existing code  
✅ **Reusable** - Methods bisa digunakan di controller lain  
✅ **Testable** - Mudah di-unit test  
✅ **SOLID** - Follow Open/Closed Principle (open for extension)

**Konsep OOP yang Diterapkan:**

1. **Extension** - Extend existing class dengan method baru
2. **Encapsulation** - SQL logic tersembunyi dalam repository
3. **Single Responsibility** - Repository hanya handle data access
4. **Consistency** - Semua data access melalui repository

---

### 5. Helper Classes - Sanitizer (10%)

Helper class untuk membersihkan input dan escape output.

**Why Sanitization?**

- **Input Sanitization** - Clean user input sebelum process/save
- **Output Escaping** - Prevent XSS attacks saat display data

**Implementation:**

File: `app/helpers/Sanitizer.php`

```php
<?php

class Sanitizer
{
    /**
     * Clean string input (remove HTML tags, trim whitespace)
     *
     * @param string $input Raw input
     * @return string Cleaned input
     */
    public static function clean(string $input): string
    {
        // Remove HTML tags
        $clean = strip_tags($input);

        // Trim whitespace
        $clean = trim($clean);

        return $clean;
    }

    /**
     * Clean array of strings
     *
     * @param array $inputs Array of raw inputs
     * @return array Array of cleaned inputs
     */
    public static function cleanArray(array $inputs): array
    {
        $cleaned = [];

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $cleaned[$key] = self::clean($value);
            } else {
                $cleaned[$key] = $value;
            }
        }

        return $cleaned;
    }

    /**
     * Escape output untuk mencegah XSS
     *
     * @param string $input Raw output
     * @return string Escaped output
     */
    public static function escape(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize integer
     *
     * @param mixed $input Raw input
     * @return int Sanitized integer
     */
    public static function int($input): int
    {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize email
     *
     * @param string $input Raw email
     * @return string Sanitized email
     */
    public static function email(string $input): string
    {
        return filter_var($input, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     *
     * @param string $input Raw URL
     * @return string Sanitized URL
     */
    public static function url(string $input): string
    {
        return filter_var($input, FILTER_SANITIZE_URL);
    }
}
```

**Usage:**

```php
// Clean input sebelum process
$name = Sanitizer::clean($_POST['name']);
$email = Sanitizer::email($_POST['email']);

// Clean seluruh array
$data = Sanitizer::cleanArray($_POST);

// Escape output di view
<h1><?= Sanitizer::escape($patient['name']) ?></h1>

// Sanitize types
$id = Sanitizer::int($_GET['id']);
$website = Sanitizer::url($_POST['website']);
```

---

### 6. BONUS: Security - CSRF Protection (10%)

**CSRF (Cross-Site Request Forgery)** adalah serangan dimana attacker membuat user menjalankan action yang tidak diinginkan di aplikasi yang user sudah login.

**Contoh Serangan:**

1. User login ke aplikasi kita (`hospital.com`)
2. Attacker buat website jahat (`evil.com`) dengan form:

```html
<form action="http://hospital.com/patients/delete" method="POST">
  <input type="hidden" name="id" value="123" />
</form>
<script>
  document.forms[0].submit();
</script>
```

3. User buka `evil.com`
4. Form auto-submit menggunakan session user yang masih active
5. Patient ID 123 terhapus tanpa user sadari! 😱

**Prevention: CSRF Token**

Generate unique token untuk setiap session. Form harus include token ini. Server verify token sebelum process request.

**Implementation:**

File: `app/helpers/Csrf.php`

```php
<?php

class Csrf
{
    /**
     * Generate CSRF token
     * Store di session
     */
    public static function generate(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     * Use hash_equals untuk prevent timing attacks
     */
    public static function verify(string $token): bool
    {
        return isset($_SESSION['csrf_token']) &&
               hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate hidden field untuk form
     */
    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Verify or fail (throw error)
     */
    public static function verifyOrFail(): void
    {
        $token = $_POST['csrf_token'] ?? '';

        if (!self::verify($token)) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
}
```

**Usage di Form:**

```php
<!-- app/views/patients/create.php -->
<form method="POST" action="/patients/store">
    <?= Csrf::field() ?>

    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <button type="submit">Save</button>
</form>
```

**Usage di Controller:**

```php
// app/controllers/PatientsController.php

public function store()
{
    // Verify CSRF token
    Csrf::verifyOrFail();

    // Process form...
}
```

**Testing CSRF Protection:**

Buat file `csrf-test.html` di luar aplikasi:

```html
<!DOCTYPE html>
<html>
  <body>
    <h1>CSRF Attack Test</h1>
    <p>This should fail because no CSRF token!</p>

    <form action="http://localhost/patients/store" method="POST">
      <input type="text" name="name" value="Hacker" />
      <input type="email" name="email" value="hacker@evil.com" />
      <button type="submit">Submit (Should Fail)</button>
    </form>
  </body>
</html>
```

Open file ini di browser dan submit form → Should get "CSRF token validation failed" error.

---

## 🏗️ Praktikum

### Setup Database

```sql
-- Tambah column deleted_at untuk soft delete
ALTER TABLE patients
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
```

### Update Autoloader

File: `app/core/Autoloader.php`

```php
<?php
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';
    $paths = [
        'controllers/' . $class . '.php',
        'repositories/' . $class . '.php',
        'factories/' . $class . '.php',  // ← Tambah ini
        'helpers/' . $class . '.php',     // ← Dan ini
        'core/' . $class . '.php',
    ];
    foreach ($paths as $rel) {
        $file = $baseDir . $rel;
        if (is_file($file)) { require_once $file; return true; }
    }
    return false;
});
```

### Load Helpers di public/index.php

```php
<?php
session_start();

require __DIR__ . '/../app/core/Autoloader.php';
require __DIR__ . '/../app/helpers/Csrf.php';
require __DIR__ . '/../app/helpers/Validator.php';
require __DIR__ . '/../app/helpers/Sanitizer.php';
// ... rest of code
```

### Seed Database

```bash
php public/seed.php
```

---

## 📝 Tugas

### Tugas 1: Extend Validator (35%)

Tambahkan validation rules berikut ke `Validator.php`:

**a) numeric() - Validasi numeric value**

```php
public function numeric(string $message = null): self
{
    // Implementasi: check apakah value adalah angka
    // Hint: use is_numeric()
}
```

**b) between($min, $max) - Validasi range angka**

```php
public function between(int $min, int $max, string $message = null): self
{
    // Implementasi: check apakah value antara $min dan $max
}
```

**c) in($values) - Validasi value dalam array**

```php
public function in(array $values, string $message = null): self
{
    // Implementasi: check apakah value ada dalam $values array
    // Hint: use in_array()
}
```

**d) unique($table, $column) - Validasi uniqueness di database**

```php
public function unique(string $table, string $column, string $message = null): self
{
    // Implementasi: check apakah value sudah ada di database
    // Hint: use Database class untuk query
}
```

**e) confirmed($field) - Validasi field confirmation**

```php
public function confirmed(string $message = null): self
{
    // Implementasi: check apakah field_confirmation match dengan field
    // Example: password dan password_confirmation
}
```

**Testing:**

```php
$validator = Validator::make($_POST)
    ->field('age')->numeric()->between(1, 150)
    ->field('gender')->in(['male', 'female'])
    ->field('email')->unique('patients', 'email')
    ->field('password')->required()->confirmed();
```

---

### Tugas 2: Create AppointmentFactory (25%)

Buat `app/factories/AppointmentFactory.php` dengan methods:

**a) create($overrides = [])**

```php
public static function create(array $overrides = []): array
{
    // Generate appointment data:
    // - patient_id (random dari database)
    // - doctor_id (random dari database)
    // - appointment_date (random future date)
    // - appointment_time (random time slot: 08:00, 09:00, ..., 16:00)
    // - reason (random dari array reasons)
    // - status ('scheduled', 'completed', 'cancelled')
}
```

**b) createMany($count)**

```php
public static function createMany(int $count): array
{
    // Generate $count appointments
}
```

**c) createForPatient($patientId, $count)**

```php
public static function createForPatient(int $patientId, int $count = 1): array
{
    // Generate appointments untuk patient spesifik
}
```

**d) createForDoctor($doctorId, $count)**

```php
public static function createForDoctor(int $doctorId, int $count = 1): array
{
    // Generate appointments untuk doctor spesifik
}
```

**e) createInDateRange($start, $end, $count)**

```php
public static function createInDateRange(string $start, string $end, int $count): array
{
    // Generate appointments dalam date range
}
```

**Testing:**

```bash
php public/seed-appointments.php
```

---

### Tugas 3: Implement Recycle Bin untuk Appointments (20%)

**a) Extend AppointmentRepository**

Tambahkan methods:

- `getDeleted($limit, $offset)`
- `countDeleted()`
- `restore($id)`
- `forceDelete($id)`

**b) Update AppointmentsController**

Tambahkan actions:

- `recycle()` - Show deleted appointments
- `restore()` - Restore deleted appointment
- `forceDelete()` - Permanent delete

**c) Create View**

Buat `app/views/appointments/recycle.php` dengan:

- Table deleted appointments
- Restore button untuk setiap appointment
- Force delete button dengan confirmation
- Pagination

**d) Update Routing**

Tambahkan routes di `public/index.php`:

```php
case 'GET /appointments/recycle':
case 'POST /appointments/restore':
case 'POST /appointments/force-delete':
```

---

### Tugas 4: Implement Sanitizer (10%)

**a) Clean Input di semua Controllers**

Update semua controller actions untuk clean input:

```php
public function store()
{
    $data = Sanitizer::cleanArray($_POST);
    // ... validate & save
}
```

**b) Escape Output di semua Views**

Update semua views untuk escape output:

```php
<td><?= Sanitizer::escape($patient['name']) ?></td>
```

**c) Sanitize URL Parameters**

```php
$id = Sanitizer::int($_GET['id']);
$page = Sanitizer::int($_GET['page'] ?? 1);
```

---

### BONUS: CSRF Protection (10%)

**a) Add CSRF Field ke semua Forms**

```php
<form method="POST">
    <?= Csrf::field() ?>
    <!-- form fields -->
</form>
```

**b) Verify CSRF di semua POST/PUT/DELETE Actions**

```php
public function store()
{
    Csrf::verifyOrFail();
    // ... process
}
```

**c) Test CSRF Protection**

Buat `csrf-test.html` dan test apakah form dari external source di-reject.

---

## 📊 Rubrik Penilaian

| Komponen                | Bobot | Kriteria Penilaian                                                                                                                                                          |
| ----------------------- | ----- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Validator Extension** | 35%   | <ul><li>Semua 5 rules implemented (7% each)</li><li>Method chaining works</li><li>Error messages appropriate</li><li>Code documented</li><li>Tested with examples</li></ul> |
| **AppointmentFactory**  | 25%   | <ul><li>All 5 methods implemented (5% each)</li><li>Realistic data generated</li><li>Overrides work correctly</li><li>Code documented</li><li>Seed script works</li></ul>   |
| **Recycle Bin**         | 20%   | <ul><li>Repository methods work (10%)</li><li>Controller actions complete (5%)</li><li>View UI functional (5%)</li><li>Routing configured</li></ul>                         |
| **Sanitizer**           | 10%   | <ul><li>Input cleaning in controllers (5%)</li><li>Output escaping in views (5%)</li><li>Consistent usage across app</li></ul>                                              |
| **BONUS: CSRF**         | 10%   | <ul><li>All forms protected (5%)</li><li>All actions verified (5%)</li><li>Test passed</li></ul>                                                                            |

**Total: 100% + 10% Bonus**

---

## 🎓 Key Takeaways

**Design Patterns:**

- ✅ **Builder Pattern** untuk chainable validation (fluent interface)
- ✅ **Factory Pattern** untuk data generation (encapsulation)
- ✅ **Repository Extension** untuk add features tanpa break existing code

**OOP Principles:**

- ✅ **DRY** - Don't Repeat Yourself (Factory, Validator, Sanitizer)
- ✅ **Single Responsibility** - Each class has one job
- ✅ **Open/Closed** - Open for extension, closed for modification
- ✅ **Encapsulation** - Hide implementation details
- ✅ **Method Chaining** - Fluent interface untuk readable code

**Best Practices:**

- ✅ **Validation** - Always validate user input
- ✅ **Sanitization** - Clean input, escape output
- ✅ **Soft Delete** - Recoverable data deletion
- ✅ **Security** - CSRF protection untuk form submissions

---

## 📚 Resources

### Design Patterns

- [Refactoring Guru - Design Patterns](https://refactoring.guru/design-patterns/php)
- [Builder Pattern](https://refactoring.guru/design-patterns/builder/php/example)
- [Factory Pattern](https://refactoring.guru/design-patterns/factory-method/php/example)

### PHP Best Practices

- [PHP The Right Way](https://phptherightway.com/)
- [OWASP PHP Security](https://owasp.org/www-project-php-security/)

### SOLID Principles

- [SOLID Principles in PHP](https://www.digitalocean.com/community/conceptual_articles/s-o-l-i-d-the-first-five-principles-of-object-oriented-design)

---

## 🚀 Next Week Preview

**Week 8: API Development & REST**

- RESTful API design principles
- JSON request/response handling
- API authentication & authorization
- API versioning strategies
- Error handling & status codes
- API documentation dengan Postman

Happy coding! 🎉
