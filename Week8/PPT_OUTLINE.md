# Week 8: Authentication & Authorization - PPT Outline (20 Slides)

## Slide Distribution

- **Introduction & Concepts:** 4 slides
- **OOP Patterns:** 10 slides (80%)
- **Security:** 3 slides (20%)
- **Demo & Summary:** 3 slides

---

## Slide 1: Title Slide

**Title:** Week 8: Authentication & Authorization with OOP

**Content:**

- Hospital Management System
- Pemrograman Lanjut
- Focus: 80% OOP Patterns, 20% Security

**Visual:**

- Hospital icon
- Lock/key icon
- PHP logo

---

## Slide 2: Today's Agenda

**Title:** What We'll Learn Today

**Content:**

1. Authentication vs Authorization (Concepts)
2. Singleton Pattern (Auth Helper)
3. Repository Pattern (UserRepository)
4. Model Pattern (User Class)
5. Middleware Pattern (Route Protection)
6. RBAC Implementation
7. Security Best Practices
8. Live Demo

**Visual:**

- Numbered list with icons
- Timeline/roadmap

---

## Slide 3: Authentication vs Authorization

**Title:** The Two Pillars of Access Control

**Content:**

**Authentication** (Who are you?)

- Verifying identity
- Login with username/password
- "Prove you are who you claim to be"
- Example: Showing ID card at hotel

**Authorization** (What can you do?)

- Verifying permissions
- Role-based access
- "What are you allowed to access?"
- Example: Hotel room key (only your room)

**Visual:**

- Split screen comparison
- Icons: ID card vs Key
- Real-world analogies

**Code Example:**

```php
// Authentication
$auth->attempt('admin', 'password123'); // ✅ Who?

// Authorization
$auth->hasRole('admin'); // ✅ What can do?
$auth->can('patients.delete'); // ✅ Specific permission
```

---

## Slide 4: Week 8 Architecture Overview

**Title:** System Architecture

**Content:**

```
┌─────────────┐
│    Views    │ Login, Dashboard, etc.
└─────────────┘
      ↓
┌─────────────┐
│ Controllers │ AuthController
└─────────────┘
      ↓
┌─────────────┐
│ Middleware  │ AuthMiddleware
└─────────────┘
      ↓
┌─────────────┐
│  Helpers    │ Auth (Singleton)
└─────────────┘
      ↓
┌─────────────┐
│ Repository  │ UserRepository
└─────────────┘
      ↓
┌─────────────┐
│   Models    │ User
└─────────────┘
      ↓
┌─────────────┐
│  Database   │ MySQL
└─────────────┘
```

**Visual:**

- Layered architecture diagram
- Data flow arrows
- Color-coded layers

---

## Slide 5: Singleton Pattern - Theory

**Title:** Singleton Pattern: One Instance to Rule Them All

**Content:**

**Definition:**
Design pattern yang memastikan class hanya punya **satu instance** dan menyediakan **global access point**.

**When to Use:**

- ✅ Database connection
- ✅ Authentication manager
- ✅ Configuration
- ✅ Logging service

**Characteristics:**

1. Private constructor (prevent `new`)
2. Static instance property
3. Static `getInstance()` method
4. Prevent cloning & serialization

**Visual:**

- UML diagram
- Single instance illustration
- Global access concept

---

## Slide 6: Singleton Pattern - Implementation

**Title:** Auth Helper as Singleton

**Code:**

```php
class Auth
{
    private static $instance = null;
    private $currentUser = null;

    // ❌ Private constructor
    private function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->initSession();
        $this->loadCurrentUser();
    }

    // ✅ Global access point
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
        throw new Exception("Cannot unserialize");
    }
}
```

**Usage:**

```php
// ❌ WRONG
$auth = new Auth(); // Error!

// ✅ CORRECT
$auth = Auth::getInstance();
$auth2 = Auth::getInstance();
var_dump($auth === $auth2); // true - same instance!
```

---

## Slide 7: Repository Pattern - Theory

**Title:** Repository Pattern: Data Access Abstraction

**Content:**

**Definition:**
Abstraction layer antara business logic dan data source.

**Without Repository:**

```php
// ❌ SQL in controller
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $db->prepare($sql);
// ... boilerplate code
```

**With Repository:**

```php
// ✅ Clean business logic
$user = $userRepo->findByUsername('admin');
```

**Benefits:**

- ✅ Separation of concerns
- ✅ Reusability
- ✅ Testability
- ✅ Maintainability
- ✅ Consistency

**Visual:**

- Before/After comparison
- Layer diagram: Controller → Repository → Database

---

## Slide 8: Repository Pattern - Implementation

**Title:** UserRepository Example

**Code:**

```php
class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Find by username or email
    public function findByUsernameOrEmail($identifier)
    {
        $sql = "SELECT * FROM users
                WHERE (username = ? OR email = ?)
                AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$identifier, $identifier]);

        if ($row = $stmt->fetch()) {
            return User::fromArray($row); // Returns object!
        }

        return null;
    }

    // Create user
    public function create($data)
    {
        $sql = "INSERT INTO users (username, email, password, ...)
                VALUES (?, ?, ?, ...)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['username'],
            $data['email'],
            User::hashPassword($data['password']),
            // ...
        ]);

        return $this->findById($this->db->lastInsertId());
    }
}
```

**Key Points:**

- Always returns User object (not array)
- Prepared statements (security)
- Reusable methods

---

## Slide 9: Model Pattern

**Title:** User Model: Domain Object with Behavior

**Code:**

```php
class User
{
    private $id;
    private $username;
    private $email;
    private $fullName;
    private $role;

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getRole() { return $this->role; }

    // Factory method
    public static function fromArray($row)
    {
        $user = new self();
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        // ...
        return $user;
    }

    // Business logic
    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Password handling
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
```

**Benefits:**

- Type safety
- Encapsulation
- Business logic in one place

---

## Slide 10: Middleware Pattern - Theory

**Title:** Middleware: Request Filtering Layer

**Content:**

**Definition:**
Layer yang memproses request **sebelum** mencapai controller.

**Flow:**

```
Request → Middleware → Controller → Response
            ↓
       - Check auth
       - Check role
       - Check permission
       - Log request
```

**Without Middleware:**

```php
// ❌ Repeat in every method
public function index()
{
    if (!$auth->check()) redirect('/login');
    if (!$auth->hasRole('admin')) redirect('/');
    // Business logic...
}
```

**With Middleware:**

```php
// ✅ Once at the top
public function index()
{
    $this->middleware->requireAdmin();
    // Business logic...
}
```

**Visual:**

- Request flow diagram
- Before/After code comparison

---

## Slide 11: Middleware Pattern - Implementation

**Title:** AuthMiddleware Implementation

**Code:**

```php
class AuthMiddleware
{
    private $auth;

    public function __construct()
    {
        $this->auth = Auth::getInstance();
    }

    // Require authentication
    public function requireAuth($redirectTo = '/login')
    {
        if (!$this->auth->check()) {
            Flash::set('error', 'Please login');
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header("Location: {$redirectTo}");
            exit;
        }
    }

    // Require specific role
    public function requireRole($roles, $redirectTo = '/dashboard')
    {
        $this->requireAuth(); // ← Composition!

        if (!$this->auth->hasRole($roles)) {
            Flash::set('error', 'Access denied');
            header("Location: {$redirectTo}");
            exit;
        }
    }

    // Require admin
    public function requireAdmin($redirectTo = '/dashboard')
    {
        $this->requireRole('admin', $redirectTo);
    }
}
```

**Usage:**

```php
$this->middleware->requireAuth();
$this->middleware->requireRole('admin');
$this->middleware->requirePermission('patients.delete');
```

---

## Slide 12: Role-Based Access Control (RBAC)

**Title:** RBAC: Roles → Permissions

**Content:**

**Concept:**

```
User → Role → Permissions
```

**Example Roles:**

**Admin:**

- `*` (all permissions)

**Doctor:**

- `appointments.view`
- `appointments.update`
- `patients.view`
- `patients.create`
- `patients.update`

**Receptionist:**

- `appointments.view`
- `appointments.create`
- `patients.view`
- `patients.create`

**Permission Format:**

```
resource.action

Examples:
- patients.view
- patients.create
- appointments.delete
- users.create
```

**Visual:**

- Role hierarchy diagram
- Permission matrix table

---

## Slide 13: RBAC Implementation

**Title:** Permission Checking

**Code:**

```php
// In config.php
'roles' => [
    'admin' => [
        'permissions' => ['*']
    ],
    'doctor' => [
        'permissions' => [
            'appointments.view',
            'appointments.update',
            'patients.view',
            'patients.create'
        ]
    ]
]

// In Auth.php
public function can($permission)
{
    if (!$this->check()) return false;

    $role = $this->currentUser->getRole();
    $permissions = $this->config['roles'][$role]['permissions'];

    // Admin has all
    if (in_array('*', $permissions)) {
        return true;
    }

    return in_array($permission, $permissions);
}

// Usage in controller
$this->middleware->requirePermission('patients.delete');

// Usage in view
<?php if ($auth->can('patients.create')): ?>
    <a href="/patients/create">Add Patient</a>
<?php endif; ?>
```

---

## Slide 14: Complete Auth Flow

**Title:** Login Flow Step-by-Step

**Content:**

**1. User submits form** → POST /login

```php
username: admin
password: password123
```

**2. Controller receives** → AuthController::login()

**3. Find user** → UserRepository::findByUsernameOrEmail()

**4. Verify password** → User::verifyPassword()

**5. Create session** → Auth::login()

```php
$_SESSION['user_id'] = $user->getId();
session_regenerate_id(true);
```

**6. Update last login** → UserRepository::updateLastLogin()

**7. Redirect** → /dashboard

**8. Load user** → Auth::loadCurrentUser()

**Visual:**

- Sequence diagram
- Numbered flow with icons

---

## Slide 15: Security - Password Hashing

**Title:** Security Best Practice #1: Password Hashing

**Content:**

**❌ NEVER DO THIS:**

```php
// Plain text - BAHAYA!
$sql = "INSERT INTO users (password) VALUES ('{$_POST['password']}')";
```

**✅ ALWAYS DO THIS:**

```php
// Hashing
$hash = password_hash($password, PASSWORD_DEFAULT);
// Result: $2y$10$92IXUNpkjO0rOQ5byMi.Ye...

// Verifying
if (password_verify($inputPassword, $storedHash)) {
    // ✅ Correct
}
```

**Why password_hash()?**

- ✅ Bcrypt algorithm (strong)
- ✅ Automatic salting (unique every time)
- ✅ Future-proof (can change algorithm)
- ✅ Industry standard

**Visual:**

- Red X vs Green checkmark
- Hash example visual
- Security icon

---

## Slide 16: Security - Session Management

**Title:** Security Best Practice #2: Session Security

**Code:**

```php
// 1. Secure session name
session_name('HOSPITAL_SESSION');

// 2. Security settings
ini_set('session.cookie_httponly', 1); // Prevent XSS
ini_set('session.use_only_cookies', 1); // Prevent fixation
ini_set('session.cookie_secure', 1);   // HTTPS only

session_start();

// 3. Regenerate ID after login
session_regenerate_id(true);

// 4. Session timeout
if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
```

**Best Practices:**

- ✅ Regenerate session ID on login
- ✅ HttpOnly cookies (prevent XSS)
- ✅ Secure flag (HTTPS)
- ✅ Session timeout
- ✅ Destroy on logout

---

## Slide 17: Security - Rate Limiting

**Title:** Security Best Practice #3: Prevent Brute Force

**Code:**

```php
private function isLockedOut()
{
    $maxAttempts = 5;
    $lockoutTime = 900; // 15 minutes

    if ($_SESSION['login_attempts'] >= $maxAttempts) {
        $timeSince = time() - $_SESSION['last_attempt_time'];

        if ($timeSince < $lockoutTime) {
            return true; // Locked out
        } else {
            $this->clearFailedAttempts();
        }
    }

    return false;
}

private function recordFailedAttempt()
{
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
}
```

**Protection:**

- Max 5 failed attempts
- 15 minute lockout
- Track by session
- Clear on success

**Visual:**

- Failed login counter
- Lockout timer visualization

---

## Slide 18: Live Demo

**Title:** Let's See It in Action!

**Demo Steps:**

1. **Login as different roles**

   - Admin → Full dashboard
   - Doctor → Limited dashboard
   - Receptionist → Basic dashboard

2. **Test authorization**

   - Try to access admin page as doctor → Denied
   - Try to delete as receptionist → Button hidden

3. **Test security**

   - Wrong password 6x → Locked out
   - Logout → Cannot access protected pages

4. **Show code**
   - Middleware in action
   - Permission checking in views

**Visual:**

- Screen recordings/screenshots
- Live browser demo
- Code walkthrough

---

## Slide 19: Assignment Overview

**Title:** Your Task: Build Authentication System

**Requirements:**

**1. Authentication (25 pts)**

- Login, logout, register
- Validation & flash messages

**2. Role-Based Dashboard (20 pts)**

- Different for admin/doctor/receptionist
- Statistics & quick actions

**3. Authorization (30 pts)**

- Middleware protection
- Permission checks
- View-based hiding

**4. Audit Logging (25 pts)**

- Track user actions
- AuditLog model & repository
- Admin audit log viewer

**Bonus (20 pts):**

- Password change
- Profile page
- Session timeout
- Email verification

**Deadline:** 1 week

---

## Slide 20: Summary & Q&A

**Title:** Key Takeaways

**OOP Patterns (80%):**

- ✅ **Singleton** - Auth helper single instance
- ✅ **Repository** - Data access abstraction
- ✅ **Model** - Domain objects with behavior
- ✅ **Middleware** - Request filtering

**Security (20%):**

- ✅ **Password hashing** - Never plain text
- ✅ **Session security** - Regeneration, httponly
- ✅ **RBAC** - Role-based permissions
- ✅ **Rate limiting** - Prevent brute force

**SOLID Principles:**

- Single Responsibility
- Separation of Concerns
- Dependency Injection
- Encapsulation

**Questions?** 🙋

**Resources:**

- Week8.md - Complete documentation
- TUGAS.md - Assignment details
- README.md - Setup guide

---

## Notes for Presenter:

**Timing:**

- Slides 1-4: 10 minutes (intro)
- Slides 5-14: 60 minutes (OOP patterns - main content)
- Slides 15-17: 15 minutes (security)
- Slide 18: 15 minutes (demo)
- Slides 19-20: 10 minutes (assignment & Q&A)
- **Total: ~110 minutes (2 sessions)**

**Tips:**

- Live code each pattern (don't just show slides)
- Do interactive demo (let students test)
- Show real errors (wrong password, access denied)
- Emphasize OOP benefits vs procedural
- Connect to real-world examples
- Encourage questions throughout

**Key Messages:**

1. OOP makes code cleaner and more maintainable
2. Design patterns solve common problems elegantly
3. Security is not optional - build it in from start
4. Separation of concerns is critical
