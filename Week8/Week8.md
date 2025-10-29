# Week 8: Authentication & Authorization with OOP

## 📚 Overview

Minggu ini kita akan membangun sistem **Authentication & Authorization** yang robust menggunakan prinsip-prinsip OOP. Kita akan melihat bagaimana design patterns seperti **Singleton**, **Repository**, dan konsep **Role-Based Access Control (RBAC)** diterapkan dalam konteks keamanan aplikasi web.

**Fokus Pembelajaran:**

- **OOP Concepts (80%)**
  - Singleton Pattern untuk Auth management (25%)
  - Repository Pattern untuk User data (20%)
  - Middleware Pattern untuk route protection (15%)
  - Model-based user management (20%)
- **Security Concepts (20%)**
  - Password hashing & verification
  - Session management
  - RBAC implementation
  - Basic security best practices

---

## 🎯 Learning Objectives

Setelah menyelesaikan minggu ini, mahasiswa diharapkan dapat:

1. **Memahami** konsep Authentication vs Authorization
2. **Mengimplementasikan** Singleton Pattern untuk Auth management
3. **Menerapkan** Repository Pattern untuk User operations
4. **Membuat** Middleware untuk route protection
5. **Mengimplementasikan** Role-Based Access Control (RBAC)
6. **Menerapkan** password hashing dan session security
7. **Memahami** separation of concerns dalam auth system

---

## 📖 Materi

### 1. Authentication vs Authorization (10 menit)

**Authentication (Siapa Anda?)**

Authentication adalah proses memverifikasi identitas user. Memastikan bahwa user adalah benar-benar orang yang mereka klaim.

```
User: "Saya adalah dr.john"
System: "Buktikan dengan password"
User: *input password*
System: ✅ "Benar, Anda adalah dr.john"
```

**Authorization (Apa yang boleh Anda lakukan?)**

Authorization adalah proses memverifikasi hak akses user. Memastikan user memiliki permission untuk melakukan suatu aksi.

```
dr.john: "Saya mau lihat data pasien"
System: "Apakah dr.john punya akses ke patients.view?"
System: ✅ "Ya, silakan akses"

receptionist: "Saya mau hapus user lain"
System: "Apakah receptionist punya akses ke users.delete?"
System: ❌ "Tidak, akses ditolak"
```

**Analogy:**

- **Authentication** = Masuk ke hotel dengan KTP (verifikasi identitas)
- **Authorization** = Kartu kamar hotel (hanya bisa buka kamar tertentu)

---

### 2. Design Patterns dalam Authentication System

#### 2.1 Singleton Pattern - Auth Helper (25%)

**Problem Statement:**

Dalam auth system, kita butuh:

- ✅ Satu instance Auth di seluruh aplikasi
- ✅ Akses global ke status login user
- ✅ Konsistensi data user di semua controller

**Solution: Singleton Pattern**

Singleton memastikan class hanya punya **satu instance** dan menyediakan **global access point**.

**Karakteristik Singleton:**

1. Private constructor - tidak bisa `new Auth()`
2. Static instance property
3. Static `getInstance()` method
4. Prevent cloning & serialization

**Implementation:**

```php
class Auth
{
    private static $instance = null;  // ← Static instance
    private $currentUser = null;

    // ❌ Private constructor - prevent direct instantiation
    private function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->initSession();
        $this->loadCurrentUser();
    }

    // ✅ Public static method - global access point
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ❌ Prevent cloning
    private function __clone() {}

    // ❌ Prevent unserialization
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
```

**Usage:**

```php
// ❌ SALAH - Tidak bisa langsung new
$auth = new Auth(); // Error: cannot access private constructor

// ✅ BENAR - Gunakan getInstance()
$auth = Auth::getInstance();

// Di controller lain, tetap instance yang sama
$auth2 = Auth::getInstance();
var_dump($auth === $auth2); // true - same instance!
```

**Benefits:**

- ✅ **Consistent State** - Status login konsisten di semua tempat
- ✅ **Resource Efficient** - Tidak perlu banyak instance
- ✅ **Global Access** - Mudah diakses dari mana saja
- ✅ **Controlled Access** - Hanya satu instance yang dikelola

**Real-World Example:**

```php
// Di AuthController
$auth = Auth::getInstance();
$auth->attempt($username, $password);

// Di PatientsController
$auth = Auth::getInstance();
if (!$auth->check()) {
    redirect('/login');
}

// Di view
$auth = Auth::getInstance();
if ($auth->hasRole('admin')) {
    // Show admin menu
}
```

---

#### 2.2 Repository Pattern - UserRepository (20%)

**Problem Statement:**

Tanpa Repository:

- ❌ SQL queries tersebar di controller
- ❌ Duplikasi code untuk CRUD operations
- ❌ Hard to test
- ❌ Hard to switch database

**Solution: Repository Pattern**

Repository adalah **abstraction layer** antara domain logic dan data source.

```
Controller → Repository → Database
   ↓            ↓           ↓
Business     Data        SQL
Logic        Access      Queries
```

**Implementation:**

```php
class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find user by ID
     * Returns User object, not raw array
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        if ($row = $stmt->fetch()) {
            return User::fromArray($row);  // ← Returns User object
        }

        return null;
    }

    /**
     * Find user by username or email
     * Useful for login
     */
    public function findByUsernameOrEmail($identifier)
    {
        $sql = "SELECT * FROM users
                WHERE (username = ? OR email = ?)
                AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$identifier, $identifier]);

        if ($row = $stmt->fetch()) {
            return User::fromArray($row);
        }

        return null;
    }

    /**
     * Create new user
     */
    public function create($data)
    {
        $sql = "INSERT INTO users (username, email, password, full_name, role)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['username'],
            $data['email'],
            User::hashPassword($data['password']),  // ← Password hashing
            $data['full_name'],
            $data['role'] ?? 'receptionist'
        ]);

        return $this->findById($this->db->lastInsertId());
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin($id)
    {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
```

**Benefits:**

- ✅ **Separation of Concerns** - Business logic terpisah dari data access
- ✅ **Reusability** - Method bisa dipakai di banyak controller
- ✅ **Testability** - Mudah di-mock untuk testing
- ✅ **Maintainability** - SQL queries di satu tempat
- ✅ **Consistency** - Selalu return User object, bukan array

**Usage in Controller:**

```php
class AuthController extends Controller
{
    private $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    public function login()
    {
        // ✅ Clean code - no SQL in controller
        $user = $this->userRepository->findByUsernameOrEmail($_POST['identifier']);

        if ($user && User::verifyPassword($_POST['password'], $user->getPassword())) {
            $this->auth->login($user);
            $this->userRepository->updateLastLogin($user->getId());
        }
    }
}
```

---

#### 2.3 Model Pattern - User Model (20%)

**Problem Statement:**

Tanpa Model class:

- ❌ Data sebagai array - tidak type-safe
- ❌ Logic tersebar (e.g., role checking)
- ❌ Tidak ada encapsulation

**Solution: Model Class**

Model merepresentasikan **entity** dalam domain bisnis dengan **properties** dan **behavior**.

**Implementation:**

```php
class User
{
    // Properties
    private $id;
    private $username;
    private $email;
    private $fullName;
    private $role;
    private $isActive;
    private $lastLogin;

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getFullName() { return $this->fullName; }
    public function getRole() { return $this->role; }
    public function isActive() { return $this->isActive; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUsername($username) { $this->username = $username; }
    // ... other setters

    /**
     * Factory method - create User from database row
     */
    public static function fromArray($row)
    {
        $user = new self();
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setFullName($row['full_name']);
        $user->setRole($row['role']);
        $user->setIsActive($row['is_active']);
        $user->setLastLogin($row['last_login'] ?? null);

        return $user;
    }

    /**
     * Business logic - check if user has role
     */
    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    /**
     * Business logic - role checkers
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isReceptionist()
    {
        return $this->role === 'receptionist';
    }

    /**
     * Get formatted role label
     */
    public function getRoleLabel()
    {
        $labels = [
            'admin' => 'Administrator',
            'doctor' => 'Doctor',
            'receptionist' => 'Receptionist'
        ];

        return $labels[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Static methods for password handling
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
```

**Benefits:**

- ✅ **Encapsulation** - Data dan behavior dalam satu class
- ✅ **Type Safety** - IDE autocomplete, type hints
- ✅ **Business Logic** - Method `hasRole()`, `isAdmin()` dll
- ✅ **Reusability** - Method bisa dipakai di banyak tempat
- ✅ **Maintainability** - Perubahan di satu tempat

**Usage:**

```php
// ❌ Dengan array - tidak type-safe
$userData = ['id' => 1, 'username' => 'admin', 'role' => 'admin'];
if ($userData['role'] === 'admin') { } // Bisa typo, tidak ada autocomplete

// ✅ Dengan Model - type-safe dan clean
$user = User::fromArray($row);
if ($user->isAdmin()) { }  // IDE autocomplete, readable
echo $user->getFullName(); // Clear dan type-safe
```

---

#### 2.4 Middleware Pattern - AuthMiddleware (15%)

**Problem Statement:**

Tanpa Middleware:

- ❌ Auth checking di setiap controller method
- ❌ Code duplication
- ❌ Lupa check auth = security hole

**Solution: Middleware Pattern**

Middleware adalah **layer** yang memproses request **sebelum** mencapai controller.

```
Request → Middleware → Controller → Response
            ↓
      Check Auth
      Check Role
      Check Permission
```

**Implementation:**

```php
class AuthMiddleware
{
    private $auth;

    public function __construct()
    {
        $this->auth = Auth::getInstance();
    }

    /**
     * Require authentication
     */
    public function requireAuth($redirectTo = '/login')
    {
        if (!$this->auth->check()) {
            Flash::set('error', 'Please login to continue');
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Require specific role
     */
    public function requireRole($roles, $redirectTo = '/dashboard')
    {
        $this->requireAuth();  // ← Composition

        if (!$this->auth->hasRole($roles)) {
            Flash::set('error', 'Access denied');
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Require specific permission
     */
    public function requirePermission($permission, $redirectTo = '/dashboard')
    {
        $this->requireAuth();

        if (!$this->auth->can($permission)) {
            Flash::set('error', 'You do not have permission');
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Require admin role
     */
    public function requireAdmin($redirectTo = '/dashboard')
    {
        $this->requireRole('admin', $redirectTo);
    }
}
```

**Usage in Controller:**

```php
class PatientsController extends Controller
{
    private $middleware;

    public function __construct()
    {
        parent::__construct();
        $this->middleware = new AuthMiddleware();
    }

    public function index()
    {
        // ✅ Protect route - hanya user yang login bisa akses
        $this->middleware->requireAuth();

        // ✅ Check permission - hanya user dengan patients.view
        $this->middleware->requirePermission('patients.view');

        // Business logic...
    }

    public function delete($id)
    {
        // ✅ Protect route - hanya admin yang bisa delete
        $this->middleware->requireAdmin();

        // Delete logic...
    }
}
```

**Benefits:**

- ✅ **DRY** - Tidak perlu repeat auth checking
- ✅ **Centralized** - Auth logic di satu tempat
- ✅ **Secure by Default** - Lupa middleware = error jelas
- ✅ **Flexible** - Bisa combine multiple checks

---

### 3. Role-Based Access Control (RBAC)

**Konsep RBAC:**

```
User → Role → Permissions
```

**Example:**

```php
// Config - define roles and permissions
'roles' => [
    'admin' => [
        'permissions' => ['*']  // All permissions
    ],
    'doctor' => [
        'permissions' => [
            'appointments.view',
            'appointments.update',
            'patients.view',
            'patients.create',
            'patients.update'
        ]
    ],
    'receptionist' => [
        'permissions' => [
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'patients.view',
            'patients.create',
            'patients.update'
        ]
    ]
]
```

**Permission Naming Convention:**

```
resource.action

Examples:
- patients.view
- patients.create
- patients.update
- patients.delete
- appointments.view
- users.create
```

**Implementation in Auth:**

```php
class Auth
{
    /**
     * Check if user has permission
     */
    public function can($permission)
    {
        if (!$this->check()) {
            return false;
        }

        $role = $this->currentUser->getRole();
        $permissions = $this->config['roles'][$role]['permissions'] ?? [];

        // Admin has all permissions
        if (in_array('*', $permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Check if user cannot perform action
     */
    public function cannot($permission)
    {
        return !$this->can($permission);
    }
}
```

**Usage in Views:**

```php
<!-- Only show button if user has permission -->
<?php if ($auth->can('patients.create')): ?>
    <a href="/patients/create" class="btn btn-primary">
        <i class="bi bi-plus"></i> Add Patient
    </a>
<?php endif; ?>

<!-- Show admin menu -->
<?php if ($auth->hasRole('admin')): ?>
    <li class="nav-item">
        <a href="/users">Manage Users</a>
    </li>
<?php endif; ?>

<!-- Hide delete button for non-admin -->
<?php if ($auth->hasRole('admin')): ?>
    <button class="btn btn-danger">Delete</button>
<?php endif; ?>
```

---

### 4. Security Implementation (20%)

#### 4.1 Password Hashing

**❌ NEVER DO THIS:**

```php
// Plain text password - SANGAT BERBAHAYA!
$sql = "INSERT INTO users (password) VALUES ('{$_POST['password']}')";
```

**✅ CORRECT WAY:**

```php
// PHP's password_hash() - automatically salted
$hash = password_hash($password, PASSWORD_DEFAULT);
// Result: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

// Verify password
if (password_verify($inputPassword, $storedHash)) {
    // Password correct
}
```

**Why password_hash()?**

- ✅ **Bcrypt** algorithm (strong)
- ✅ **Auto-salting** (unique hash setiap kali)
- ✅ **Future-proof** (bisa ganti algorithm)
- ✅ **Industry standard**

#### 4.2 Session Management

**Session Security:**

```php
// 1. Secure session name
session_name('HOSPITAL_SESSION');

// 2. Security settings
ini_set('session.cookie_httponly', 1);  // Prevent XSS access
ini_set('session.use_only_cookies', 1); // Prevent session fixation
ini_set('session.cookie_secure', 1);    // HTTPS only (production)

session_start();

// 3. Regenerate session ID after login
session_regenerate_id(true);

// 4. Session timeout
if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
```

#### 4.3 Login Attempt Limiting

**Prevent Brute Force:**

```php
private function isLockedOut()
{
    $maxAttempts = 5;
    $lockoutTime = 900; // 15 minutes

    if ($_SESSION['login_attempts'] >= $maxAttempts) {
        $timeSince = time() - $_SESSION['last_attempt_time'];

        if ($timeSince < $lockoutTime) {
            return true;  // Still locked out
        } else {
            $this->clearFailedAttempts();
        }
    }

    return false;
}
```

#### 4.4 Input Validation

**Always validate user input:**

```php
// Username validation
if (strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// Password strength
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}
```

---

## 🏗️ Architecture Overview

**Layered Architecture:**

```
┌─────────────────────────────────────────┐
│              VIEWS                      │
│  (login.php, dashboard.php, etc)       │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│           CONTROLLERS                   │
│    (AuthController, PatientsController) │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│           MIDDLEWARE                    │
│         (AuthMiddleware)                │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│       HELPERS & SERVICES                │
│       (Auth, Flash, Session)            │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│          REPOSITORIES                   │
│        (UserRepository)                 │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│            MODELS                       │
│             (User)                      │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│           DATABASE                      │
│        (MySQL/MariaDB)                  │
└─────────────────────────────────────────┘
```

---

## 💻 Complete Flow Example

**Login Flow:**

```
1. User → View (login.php)
   └─ Form POST to /login

2. Router → Controller (AuthController::login)
   └─ Receive POST data

3. Controller → Middleware (optional)
   └─ Check if already logged in

4. Controller → Repository (UserRepository::findByUsernameOrEmail)
   └─ Query database

5. Repository → Model (User::fromArray)
   └─ Convert array to User object

6. Controller → Auth Helper (Auth::attempt)
   └─ Verify password
   └─ Create session
   └─ Update last_login

7. Controller → View (redirect to dashboard)
   └─ Success!
```

---

## 📝 Best Practices

### OOP Principles:

1. **Single Responsibility** - Setiap class punya satu tanggung jawab

   - Auth → Authentication logic
   - UserRepository → Data access
   - User → Domain model
   - AuthMiddleware → Route protection

2. **Dependency Injection** - Pass dependencies through constructor

   ```php
   public function __construct()
   {
       $this->userRepository = new UserRepository();
       $this->auth = Auth::getInstance();
   }
   ```

3. **Encapsulation** - Hide internal state

   ```php
   private $password;  // ← Private!
   public function getPassword() { return $this->password; }
   ```

4. **Composition** - Build complex behavior from simple parts
   ```php
   public function requireAdmin()
   {
       $this->requireAuth();      // ← Reuse
       $this->requireRole('admin');
   }
   ```

### Security Principles:

1. **Never trust user input** - Always validate
2. **Hash passwords** - Never store plain text
3. **Use prepared statements** - Prevent SQL injection
4. **Implement rate limiting** - Prevent brute force
5. **Use HTTPS** - In production
6. **Regenerate session ID** - After login/logout

---

## 🧪 Testing the System

### 1. Import Database

```sql
mysql -u root -p
source Week8/database/hospital.sql
```

### 2. Test Login

**Credentials:**

- Admin: `admin` / `password123`
- Doctor: `dr.john` / `password123`
- Receptionist: `receptionist` / `password123`

### 3. Test Authorization

**Admin dapat:**

- ✅ Manage users
- ✅ View audit logs
- ✅ All CRUD operations

**Doctor dapat:**

- ✅ View/update appointments
- ✅ View/create/update patients
- ❌ Delete users
- ❌ Manage other doctors

**Receptionist dapat:**

- ✅ Create appointments
- ✅ Create/update patients
- ❌ Delete appointments
- ❌ Manage users

---

## 📊 Database Schema

**users table:**

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'doctor', 'receptionist') DEFAULT 'receptionist',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
);
```

**Key Fields:**

- `password` - VARCHAR(255) untuk bcrypt hash
- `role` - ENUM untuk role-based access
- `is_active` - Boolean untuk enable/disable user
- `last_login` - Track user activity
- `deleted_at` - Soft delete support

---

## 🎓 Summary

**OOP Concepts Learned (80%):**

1. ✅ **Singleton Pattern** - Auth helper dengan single instance
2. ✅ **Repository Pattern** - Data access abstraction
3. ✅ **Model Pattern** - Domain object dengan behavior
4. ✅ **Middleware Pattern** - Request filtering
5. ✅ **Factory Method** - User::fromArray()
6. ✅ **Encapsulation** - Private properties, public methods
7. ✅ **Composition** - Build complex from simple

**Security Concepts Learned (20%):**

1. ✅ **Password Hashing** - password_hash() & password_verify()
2. ✅ **Session Security** - Secure settings, regeneration
3. ✅ **RBAC** - Role and permission checking
4. ✅ **Rate Limiting** - Prevent brute force
5. ✅ **Input Validation** - Never trust user input

**Design Principles:**

- ✅ Separation of Concerns
- ✅ Don't Repeat Yourself (DRY)
- ✅ Single Responsibility
- ✅ Dependency Injection
- ✅ Security by Design

---

## 🚀 Next Steps

Week 9 topics bisa include:

- Advanced RBAC dengan permissions table
- JWT authentication untuk API
- OAuth integration
- Two-Factor Authentication (2FA)
- Audit logging dengan Observer pattern

---

## 📚 References

- [PHP password_hash() Documentation](https://www.php.net/manual/en/function.password-hash.php)
- [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)
- [Design Patterns: Singleton](https://refactoring.guru/design-patterns/singleton)
- [Martin Fowler - Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html)
