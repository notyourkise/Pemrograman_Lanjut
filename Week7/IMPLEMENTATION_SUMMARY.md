# Week 7 Implementation Summary

## ✅ Completed Implementation

### 1. PatientFactory.php ✅

**Location:** `app/factories/PatientFactory.php`

**Features:**

- ✅ `create($overrides)` - Generate single patient with optional overrides
- ✅ `createMany($count)` - Batch generate multiple patients
- ✅ `createWithAge($age)` - Generate patient of specific age
- ✅ `createByGender($gender, $count)` - Generate patients by gender
- ✅ Realistic Indonesian names (male/female variants)
- ✅ Realistic addresses, cities, blood types, allergies
- ✅ Private helper method `randomDate()` for date generation

**Factory Pattern Concepts:**

- Static methods (no instantiation needed)
- Default values with override capability
- Encapsulation of data generation logic
- Array merge for combining defaults and overrides

---

### 2. seed.php ✅

**Location:** `public/seed.php`

**Features:**

- ✅ Loads all dependencies (Database, Autoloader, Repository, Factory)
- ✅ Generates 50 dummy patients using `PatientFactory::createMany(50)`
- ✅ Inserts to database via PatientRepository
- ✅ Progress indicators (every 10 records)
- ✅ Success/failure tracking
- ✅ Error handling with try-catch
- ✅ Pretty console output with emojis 🌱📦✅❌

**Usage:**

```bash
php public/seed.php
```

---

### 3. Autoloader.php Updated ✅

**Location:** `app/core/Autoloader.php`

**Changes:**

- ✅ Added `factories/` path to autoload paths
- ✅ Now supports: controllers, repositories, **factories**, core

**Before:**

```php
$paths = [
    'controllers/' . $class . '.php',
    'repositories/' . $class . '.php',
    'core/' . $class . '.php',
];
```

**After:**

```php
$paths = [
    'controllers/' . $class . '.php',
    'repositories/' . $class . '.php',
    'factories/' . $class . '.php',  // ← Added
    'core/' . $class . '.php',
];
```

---

### 4. Week7.md Updated ✅

**Location:** `Week7/Week7.md`

**New Structure - OOP Focused (80% OOP / 20% Security):**

#### 📖 Content Sections:

**1. Introduction to Design Patterns (10 min)**

- What are Design Patterns?
- Why are they important?
- Categories: Creational, Structural, Behavioral
- Bridge from Week 6 to Week 7

**2. Builder Pattern - Validator (35%)**

- Problem: Repetitive validation code
- Solution: Method chaining (fluent interface)
- Full `Validator.php` code with explanations
- Usage examples in controller
- Concepts: Method chaining, fluent interface, encapsulation, SRP
- Practice tasks: Add 5 more validation rules

**3. Factory Pattern - PatientFactory (25%)**

- Problem: Repetitive dummy data creation
- Solution: Centralized data generation
- Full `PatientFactory.php` code with explanations
- Usage examples: seeding, testing
- Concepts: Static methods, encapsulation, defaults, overrides
- Practice task: Create AppointmentFactory

**4. Repository Extension - Recycle Bin (20%)**

- Concept: Soft Delete vs Hard Delete
- Why soft delete? (safety, recoverability, audit trail)
- Database schema changes
- PatientRepository extension methods:
  - `countDeleted()`
  - `getDeleted($limit, $offset)`
  - `restore($id)`
  - `forceDelete($id)`
- Controller implementation: `recycle()`, `restore()`, `forceDelete()`
- View implementation: `recycle.php` with restore/delete UI
- Concepts: Extension, non-breaking changes, Open/Closed Principle

**5. Helper Classes - Sanitizer (10%)**

- Why sanitization?
- Full `Sanitizer.php` code
- Methods: `clean()`, `cleanArray()`, `escape()`, `int()`, `email()`, `url()`
- Usage examples

**6. BONUS: Security - CSRF (10%)**

- What is CSRF?
- Attack example scenario
- Prevention: CSRF tokens
- Full `Csrf.php` code
- Usage in forms and controllers
- Testing CSRF protection

#### 📝 Praktikum Section:

- Setup database (ADD deleted_at column)
- Update Autoloader
- Load helpers in index.php
- Seed database command

#### 📝 Tugas Section (Detailed):

- **Tugas 1:** Extend Validator (35%)
  - Add 5 rules: numeric(), between(), in(), unique(), confirmed()
- **Tugas 2:** Create AppointmentFactory (25%)
  - Implement 5 methods with specifications
- **Tugas 3:** Recycle Bin for Appointments (20%)
  - Repository extension, controller, view, routing
- **Tugas 4:** Implement Sanitizer (10%)
  - Clean input, escape output, sanitize parameters
- **BONUS:** CSRF Protection (10%)
  - Add to all forms, verify in all actions, test

#### 📊 Rubrik Penilaian:

- Detailed grading criteria for each component
- **Total: 100% + 10% Bonus**

#### 🎓 Key Takeaways:

- Design Patterns summary
- OOP Principles applied
- Best practices learned

#### 📚 Resources:

- Links to external documentation
- Refactoring Guru
- PHP The Right Way
- OWASP Security

---

### 5. Week7_backup.md Created ✅

**Location:** `Week7/Week7_backup.md`

**Purpose:**

- Backup of original security-focused Week 7 materials
- Preserved for reference or future use
- Contains the 60% security / 40% OOP version

---

## 📊 OOP vs Security Balance

### New Distribution (80% OOP / 20% Security):

| Component                          | Weight | Type                 |
| ---------------------------------- | ------ | -------------------- |
| Builder Pattern (Validator)        | 35%    | **OOP** ✅           |
| Factory Pattern (PatientFactory)   | 25%    | **OOP** ✅           |
| Repository Extension (Recycle Bin) | 20%    | **OOP** ✅           |
| Helper Classes (Sanitizer)         | 10%    | **Mixed** (utility)  |
| CSRF Protection                    | 10%    | **Security** (bonus) |

**Total OOP Focus:** 80%  
**Security Bonus:** 20%

---

## 🎯 Learning Objectives Achieved

✅ **Design Patterns Understanding**

- Builder Pattern for validation (method chaining, fluent interface)
- Factory Pattern for data generation (encapsulation, static methods)
- Repository Pattern extension (soft delete, recycle bin)

✅ **OOP Principles Applied**

- **DRY** - Don't Repeat Yourself (Factory, Validator, Sanitizer)
- **Single Responsibility** - Each class has one clear purpose
- **Open/Closed** - Repository open for extension, closed for modification
- **Encapsulation** - Hide implementation details
- **Method Chaining** - Fluent interface for readable code

✅ **Practical Skills**

- Database seeding with realistic data
- Soft delete implementation
- Input validation best practices
- Data sanitization
- Security basics (CSRF as bonus)

---

## 🚀 How to Use

### 1. Test PatientFactory:

```php
// In any PHP file
require_once 'app/factories/PatientFactory.php';

// Generate one patient
$patient = PatientFactory::create();

// Generate with overrides
$testPatient = PatientFactory::create([
    'name' => 'Test Patient',
    'email' => 'test@example.com'
]);

// Generate by age
$senior = PatientFactory::createWithAge(70);

// Generate by gender
$females = PatientFactory::createByGender('female', 10);
```

### 2. Seed Database:

```bash
# From project root
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
   • Total: 50 patients
```

### 3. View Week 7 Documentation:

- Open `Week7/Week7.md` in VS Code or browser
- Read through all sections
- Follow praktikum steps
- Complete tugas assignments

---

## 📁 File Structure

```
Week7/
├── app/
│   ├── factories/
│   │   └── PatientFactory.php          ✅ NEW
│   ├── helpers/
│   │   ├── Csrf.php                     ✅ EXISTS
│   │   ├── Validator.php                ✅ EXISTS
│   │   └── Sanitizer.php                ✅ EXISTS
│   ├── repositories/
│   │   └── PatientRepository.php        ✅ EXTENDED
│   ├── controllers/
│   │   └── PatientsController.php       ✅ UPDATED
│   ├── views/
│   │   └── patients/
│   │       └── recycle.php              ✅ EXISTS
│   └── core/
│       └── Autoloader.php               ✅ UPDATED
├── public/
│   ├── index.php                        ✅ EXISTS
│   └── seed.php                         ✅ NEW
├── Week7.md                             ✅ UPDATED (OOP-focused)
├── Week7_backup.md                      ✅ BACKUP (security-focused)
├── README.md                            ✅ EXISTS
└── TUGAS.md                             ✅ EXISTS
```

---

## ✅ Checklist

### Completed:

- [x] Create `app/factories/` directory
- [x] Create `PatientFactory.php` with full implementation
- [x] Create `public/seed.php` script
- [x] Update `Autoloader.php` to include factories
- [x] Backup original `Week7.md` to `Week7_backup.md`
- [x] Rewrite `Week7.md` with OOP focus (80/20 split)
- [x] Document all changes in summary

### Ready for Students:

- [x] Week 7 materials focus on OOP Design Patterns
- [x] Security positioned as bonus (10%)
- [x] Clear learning objectives
- [x] Practical implementation examples
- [x] Detailed assignments with rubric
- [x] Complete documentation

---

## 🎓 For Teaching

### Presentation Order:

1. **Intro** (5 min) - Why Design Patterns?
2. **Builder Pattern** (25 min) - Validator demo & code review
3. **Factory Pattern** (20 min) - PatientFactory demo & seeding
4. **Repository Extension** (15 min) - Soft delete & Recycle Bin
5. **Helper Classes** (5 min) - Sanitizer utilities
6. **BONUS: CSRF** (5 min) - Quick security overview
7. **Praktikum** (25 min) - Students follow setup steps
8. **Q&A** (10 min)

**Total:** 110 minutes (2 class sessions)

### Key Teaching Points:

- ✅ Design Patterns are templates, not code to copy-paste
- ✅ Method chaining makes code readable ("fluent interface")
- ✅ Factory Pattern excellent for testing & seeding
- ✅ Soft delete > Hard delete (always recoverable)
- ✅ Security is important but not the focus this week

---

## 🎯 Success Criteria

Students should be able to:

- ✅ Explain what Builder and Factory patterns are
- ✅ Implement method chaining in their own classes
- ✅ Create factory classes for data generation
- ✅ Extend existing classes without breaking them
- ✅ Use helper classes appropriately
- ✅ (Bonus) Implement basic CSRF protection

---

## 📝 Notes

**Design Decision:**

- Original Week 7 was 60% security / 40% OOP
- Rebalanced to 80% OOP / 20% security based on course objectives
- "Pemrograman Lanjut" = focus on OOP, not web security
- Security included as bonus to maintain real-world awareness

**Pattern Selection:**

- **Builder** - Most visible in everyday code (validation, query builders)
- **Factory** - Most useful for practical tasks (testing, seeding)
- **Repository Extension** - Builds on Week 6, shows OCP in action

**Student Workload:**

- 4 main tugas + 1 bonus
- Estimated 8-10 hours of work
- Clear rubric with point distribution
- Bonus keeps high-achievers engaged

---

## 🚀 Next Steps

### For Instructor:

1. Review Week7.md content
2. Test seed.php script
3. Prepare PPT slides based on documentation
4. Create example solutions for tugas
5. Set up grading rubric in LMS

### For Students (in TUGAS.md):

1. Read Week7.md thoroughly
2. Follow praktikum setup steps
3. Run seed.php to understand Factory Pattern
4. Complete Tugas 1-4 (+ bonus)
5. Submit before deadline

---

**Last Updated:** 2024
**Version:** 2.0 (OOP-focused)
**Status:** ✅ Ready for Deployment

