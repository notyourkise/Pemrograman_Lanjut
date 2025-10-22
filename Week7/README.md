# Week 7 - Advanced OOP Design Patterns

Proyek ini merupakan lanjutan dari Week 6 dengan penerapan Design Patterns:

- **Builder Pattern** - Chainable validation (Validator class)
- **Factory Pattern** - Data generation (PatientFactory class)
- **Repository Extension** - Soft delete & Recycle Bin
- **Helper Classes** - Sanitizer utilities
- **BONUS: CSRF Protection** - Security basics

## 📖 Materi Pembelajaran

Fokus pembelajaran Week 7:

- **80% OOP Design Patterns** (Builder, Factory, Repository Extension)
- **20% Web Security** (CSRF as bonus)

Lihat dokumentasi lengkap di [Week7.md](Week7.md)

---

## 🚀 Quick Start

### 1. Setup Database

```sql
-- Gunakan database yang sama dengan Week 6
USE hospital;

-- Tambah column deleted_at untuk soft delete
ALTER TABLE patients
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Untuk appointments juga (jika ada)
ALTER TABLE appointments
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
```

### 2. Konfigurasi

Edit `app/config.php`:

```php
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'hospital',  // Sesuaikan nama database
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
];
```

### 3. Seed Database (Opsional)

Generate dummy data menggunakan Factory Pattern:

```bash
# Dari root project
php public/seed.php
```

Output expected:

```
🌱 Starting seed...

📦 Generating 50 patients...
   ✓ Inserted 10 patients
   ✓ Inserted 20 patients
   ✓ Inserted 30 patients
   ✓ Inserted 40 patients
   ✓ Inserted 50 patients

✅ Seeding completed!
   • Success: 50 patients
   • Failed: 0 patients
   • Total: 50 patients
```

### 4. Jalankan Aplikasi

- Start XAMPP (Apache + MySQL)
- Akses: `http://localhost/MATERI-ASDOS/Week7/public/`
- Navigate ke Patients → Recycle Bin untuk test soft delete

---

## 🎯 Design Patterns Implemented

### 1. Builder Pattern (35%)

**Class:** `app/helpers/Validator.php`

**Konsep:** Method chaining untuk validasi yang readable dan maintainable

**Example:**

```php
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
        ->phone();

if ($validator->fails()) {
    $errors = $validator->errors();
}
```

**Key Features:**

- ✅ Method chaining (return `$this`)
- ✅ Fluent interface (readable code)
- ✅ Reusable di semua controller
- ✅ Easy to extend dengan rules baru

---

### 2. Factory Pattern (25%)

**Class:** `app/factories/PatientFactory.php`

**Konsep:** Centralized data generation dengan realistic dummy data

**Example:**

```php
// Generate single patient
$patient = PatientFactory::create();

// Generate with overrides
$testPatient = PatientFactory::create([
    'name' => 'Test Patient',
    'email' => 'test@example.com'
]);

// Generate many
$patients = PatientFactory::createMany(50);

// Generate by age
$senior = PatientFactory::createWithAge(70);

// Generate by gender
$females = PatientFactory::createByGender('female', 10);
```

**Key Features:**

- ✅ Static methods (no instantiation)
- ✅ Default values + overrides support
- ✅ Realistic Indonesian names & data
- ✅ Perfect untuk testing & seeding

**Usage in Seeding:**

```php
// public/seed.php
$patients = PatientFactory::createMany(50);

foreach ($patients as $patient) {
    $repo->create($patient);
}
```

---

### 3. Repository Extension (20%)

**Class:** `app/repositories/PatientRepository.php` (extended)

**Konsep:** Soft delete & Recycle Bin functionality

**New Methods:**

```php
// Count deleted records
$count = $repo->countDeleted();

// Get deleted records with pagination
$deletedPatients = $repo->getDeleted($limit, $offset);

// Restore soft-deleted record
$success = $repo->restore($id);

// Permanent delete
$success = $repo->forceDelete($id);
```

**Soft Delete Concept:**

```sql
-- Soft delete (set deleted_at)
UPDATE patients SET deleted_at = NOW() WHERE id = 1;

-- Regular query (hide deleted)
SELECT * FROM patients WHERE deleted_at IS NULL;

-- Recycle bin (show deleted)
SELECT * FROM patients WHERE deleted_at IS NOT NULL;

-- Restore
UPDATE patients SET deleted_at = NULL WHERE id = 1;
```

**Key Features:**

- ✅ Non-breaking extension (Week 6 code masih jalan)
- ✅ Recoverable deletion (safety)
- ✅ Audit trail (track when deleted)
- ✅ User-friendly (undo mistakes)

---

### 4. Helper Classes (10%)

**Class:** `app/helpers/Sanitizer.php`

**Konsep:** Utility functions untuk clean input & escape output

**Example:**

```php
// Clean input
$name = Sanitizer::clean($_POST['name']);

// Clean array
$data = Sanitizer::cleanArray($_POST);

// Escape output (prevent XSS)
echo Sanitizer::escape($patient['name']);

// Type sanitization
$id = Sanitizer::int($_GET['id']);
$email = Sanitizer::email($_POST['email']);
```

**Key Features:**

- ✅ Static utility methods
- ✅ Input cleaning (strip tags, trim)
- ✅ Output escaping (prevent XSS)
- ✅ Type sanitization (int, email, url)

---

### 5. BONUS: CSRF Protection (10%)

**Class:** `app/helpers/Csrf.php`

**Konsep:** Protect forms from Cross-Site Request Forgery attacks

**Example:**

```php
// In view (form)
<form method="POST">
    <?= Csrf::field() ?>
    <!-- form fields -->
</form>

// In controller
public function store()
{
    Csrf::verifyOrFail();  // Auto-check or die with 403

    // Process form...
}
```

**Test CSRF Protection:**

```html
<!-- csrf-test.html (external file) -->
<form action="http://localhost/Week7/public/patients/store" method="POST">
  <input type="text" name="name" value="Hacker" />
  <button>Submit (Should Fail)</button>
</form>
```

Open → Submit → Should get "CSRF token validation failed" (403 error)

---

## 📁 Project Structure

```
Week7/
├── app/
│   ├── controllers/
│   │   ├── AppointmentsController.php
│   │   └── PatientsController.php
│   ├── core/
│   │   ├── Autoloader.php         (updated with factories/)
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Flash.php
│   │   ├── Paginator.php
│   │   └── QueryBuilder.php
│   ├── factories/                  ✅ NEW
│   │   └── PatientFactory.php     ✅ Factory Pattern
│   ├── helpers/                    ✅ NEW
│   │   ├── Csrf.php               ✅ CSRF Protection
│   │   ├── Sanitizer.php          ✅ Input/Output Sanitization
│   │   └── Validator.php          ✅ Builder Pattern
│   ├── repositories/
│   │   ├── AppointmentRepository.php
│   │   ├── DoctorRepository.php
│   │   └── PatientRepository.php  (extended with soft delete)
│   ├── views/
│   │   ├── appointments/
│   │   ├── layout/
│   │   └── patients/
│   │       ├── create.php
│   │       ├── edit.php
│   │       ├── index.php
│   │       └── recycle.php         ✅ NEW (Recycle Bin)
│   └── config.php
├── public/
│   ├── index.php
│   ├── seed.php                    ✅ NEW (Seeding script)
│   └── assets/
│       └── styles.css
├── csrf-test.html                  ✅ NEW (CSRF testing)
├── README.md                       (this file)
├── Week7.md                        (full documentation)
├── TUGAS.md                        (assignment description)
└── IMPLEMENTATION_SUMMARY.md       (implementation summary)
```

---

## 🧪 Testing Guide

### 1. Test Builder Pattern (Validator)

**Location:** Any form (create/edit patient)

**Test Cases:**

1. Submit form dengan field kosong → Should show "required" errors
2. Submit dengan name < 3 chars → Should show "minLength" error
3. Submit dengan invalid email → Should show "email format" error
4. Submit dengan invalid phone → Should show "phone format" error
5. Submit valid data → Should save successfully

**Expected:** Validation errors displayed clearly with red styling

---

### 2. Test Factory Pattern (PatientFactory)

**Command:**

```bash
php public/seed.php
```

**Expected Output:**

```
🌱 Starting seed...

📦 Generating 50 patients...
   ✓ Inserted 10 patients
   ✓ Inserted 20 patients
   ✓ Inserted 30 patients
   ✓ Inserted 40 patients
   ✓ Inserted 50 patients

✅ Seeding completed!
   • Success: 50 patients
   • Failed: 0 patients
```

**Verify:**

- Check phpMyAdmin → patients table should have 50+ records
- Names should be realistic (Indonesian names)
- Emails should be unique (name-based)
- Data should be properly formatted

---

### 3. Test Repository Extension (Soft Delete)

**Location:** Patients list page

**Test Cases:**

1. Delete a patient → Record should disappear from list
2. Check database → Record should have `deleted_at` value (not deleted)
3. Go to Recycle Bin → Deleted patient should appear
4. Click "Restore" → Patient should return to main list
5. Delete again → Go to Recycle Bin → Click "Force Delete" → Record should be GONE from database

**Expected:**

- ✅ Soft delete works (data not permanently lost)
- ✅ Recycle Bin shows deleted records
- ✅ Restore brings data back
- ✅ Force delete removes permanently

---

### 4. Test Sanitizer (Helper)

**Location:** Any form

**Test Cases:**

1. Input: `<script>alert('XSS')</script>` in name field

   - Expected: Saved as plain text (tags stripped)
   - Display: Shows as text, not executed

2. Input: `  Test Name  ` (with spaces)

   - Expected: Saved as `Test Name` (trimmed)

3. URL parameter: `?id=abc123`
   - Expected: Sanitized to `0` (invalid int)

**Expected:** All inputs cleaned, all outputs escaped

---

### 5. Test CSRF Protection (BONUS)

**Step 1:** Create `csrf-test.html`:

```html
<!DOCTYPE html>
<html>
  <body>
    <h1>CSRF Attack Test</h1>
    <form
      action="http://localhost/MATERI-ASDOS/Week7/public/index.php?action=patients/store"
      method="POST"
    >
      <input type="text" name="name" value="Hacker" />
      <input type="email" name="email" value="hacker@evil.com" />
      <button type="submit">Submit (Should Fail)</button>
    </form>
  </body>
</html>
```

**Step 2:** Open file in browser (NOT via localhost)

**Step 3:** Click submit

**Expected:**

```
CSRF token validation failed
```

HTTP 403 Forbidden

---

## 📝 OOP Principles Applied

### 1. DRY (Don't Repeat Yourself)

- ✅ Validator reused across all forms
- ✅ Factory generates data without repetition
- ✅ Sanitizer centralizes cleaning logic

### 2. Single Responsibility Principle (SRP)

- ✅ Validator → Only validates
- ✅ Factory → Only generates data
- ✅ Repository → Only handles data access
- ✅ Sanitizer → Only cleans/escapes

### 3. Open/Closed Principle (OCP)

- ✅ Repository **extended** with soft delete (not modified)
- ✅ Easy to add new validation rules without breaking existing
- ✅ Easy to add new factory methods

### 4. Encapsulation

- ✅ Validation logic hidden in Validator class
- ✅ Data generation logic hidden in Factory
- ✅ SQL queries hidden in Repository

### 5. Method Chaining (Fluent Interface)

- ✅ Validator uses method chaining for readable code
- ✅ Each method returns `$this`
- ✅ Allows: `->field('name')->required()->minLength(3)`

---

## 🎓 Learning Outcomes

After completing Week 7, students should understand:

✅ **What are Design Patterns** and why they're useful  
✅ **Builder Pattern** for chainable/fluent interfaces  
✅ **Factory Pattern** for object creation  
✅ **How to extend classes** without breaking existing code  
✅ **Soft delete vs Hard delete** and when to use each  
✅ **Helper classes** for common utilities  
✅ **BONUS: Basic web security** (CSRF protection)

---

## 📚 Resources

- **Full Documentation:** [Week7.md](Week7.md)
- **Assignment:** [TUGAS.md](TUGAS.md)
- **Implementation Summary:** [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)
- **Design Patterns:** [Refactoring Guru](https://refactoring.guru/design-patterns/php)
- **PHP Best Practices:** [PHP The Right Way](https://phptherightway.com/)

---

## 🐛 Troubleshooting

### Issue: "Class not found" error

**Solution:**

```php
// Check Autoloader.php includes factories/ path
$paths = [
    'controllers/' . $class . '.php',
    'repositories/' . $class . '.php',
    'factories/' . $class . '.php',  // ← Must be here
    'helpers/' . $class . '.php',     // ← And here
    'core/' . $class . '.php',
];
```

### Issue: Seed script fails

**Check:**

1. Database connection in `config.php`
2. `patients` table exists
3. MySQL service running in XAMPP

**Run:**

```bash
php public/seed.php
```

### Issue: CSRF token validation fails on legitimate forms

**Check:**

1. Session started in `public/index.php`: `session_start();`
2. CSRF field in form: `<?= Csrf::field() ?>`
3. CSRF verification in controller: `Csrf::verifyOrFail();`

---

## 🚀 Next Week Preview

**Week 8: REST API Development**

- RESTful API design principles
- JSON request/response
- API authentication
- API versioning
- Error handling & status codes

---

**Happy Coding! 🎉**

Focus on understanding **WHY** we use these patterns, not just **HOW** to implement them.
