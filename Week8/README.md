# Week 8: Authentication & Authorization

## 🎓 Overview

Week 8 membahas implementasi sistem **Authentication & Authorization** dengan pendekatan OOP yang solid. Fokus pada design patterns (Singleton, Repository, Middleware) dan security best practices.

**Learning Focus:**

- 80% OOP (Singleton, Repository, Middleware, Model patterns)
- 20% Security (Password hashing, session management, RBAC)

---

## 📚 Topics Covered

1. **Authentication vs Authorization**

   - Perbedaan konsep
   - Real-world analogies
   - Implementation strategies

2. **Singleton Pattern**

   - Auth helper implementation
   - Benefits dan use cases
   - Preventing multiple instances

3. **Repository Pattern**

   - UserRepository for data access
   - Separation of concerns
   - Query abstraction

4. **Model Pattern**

   - User model with behavior
   - Encapsulation
   - Factory methods

5. **Middleware Pattern**

   - Route protection
   - Role checking
   - Permission validation

6. **Role-Based Access Control (RBAC)**

   - Roles: admin, doctor, receptionist
   - Permission system
   - Dynamic UI based on roles

7. **Security Best Practices**
   - Password hashing with bcrypt
   - Session security
   - Rate limiting
   - Input validation

---

## 🛠️ Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB
- XAMPP/WAMP/LAMP
- Web browser (Chrome/Firefox recommended)

### Setup Steps

1. **Clone/Download project**

   ```bash
   cd C:\xampp\htdocs\MATERI-ASDOS\Week8
   ```

2. **Import database**

   ```bash
   # Via MySQL CLI
   mysql -u root -p
   source database/hospital.sql
   exit

   # Or via phpMyAdmin
   # - Open http://localhost/phpmyadmin
   # - Create database: hospital_week8
   # - Import: database/hospital.sql
   ```

3. **Configure database connection**

   Edit `app/config.php` if needed:

   ```php
   'database' => [
       'host' => 'localhost',
       'dbname' => 'hospital_week8',
       'username' => 'root',
       'password' => '',  // Your MySQL password
   ]
   ```

4. **Start Apache and MySQL**

   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

5. **Access application**
   ```
   http://localhost/MATERI-ASDOS/Week8/public/
   ```

---

## 🔐 Demo Credentials

Test dengan user berikut:

| Role             | Username      | Password    | Capabilities                             |
| ---------------- | ------------- | ----------- | ---------------------------------------- |
| **Admin**        | admin         | password123 | Full access (users, doctors, audit logs) |
| **Admin**        | admin2        | password123 | Full access                              |
| **Doctor**       | dr.john       | password123 | View/update appointments, patients       |
| **Doctor**       | dr.sarah      | password123 | View/update appointments, patients       |
| **Doctor**       | dr.michael    | password123 | View/update appointments, patients       |
| **Receptionist** | receptionist  | password123 | Create/view appointments, patients       |
| **Receptionist** | receptionist2 | password123 | Create/view appointments, patients       |

---

## 📁 Project Structure

```
Week8/
├── app/
│   ├── config.php                    # Configuration (database, security, roles)
│   ├── controllers/
│   │   └── AuthController.php        # Login, register, logout, dashboard
│   ├── core/
│   │   ├── Autoloader.php            # PSR-4 autoloader
│   │   ├── Controller.php            # Base controller
│   │   ├── Database.php              # Singleton database connection
│   │   └── Flash.php                 # Flash message helper
│   ├── helpers/
│   │   └── Auth.php                  # Singleton auth manager
│   ├── middleware/
│   │   └── AuthMiddleware.php        # Route protection middleware
│   ├── models/
│   │   └── User.php                  # User domain model
│   ├── repositories/
│   │   └── UserRepository.php        # User data access layer
│   └── views/
│       ├── auth/
│       │   ├── login.php             # Login page
│       │   └── register.php          # Registration page
│       ├── dashboard/
│       │   └── index.php             # Role-based dashboard
│       └── layout/
│           ├── header.php            # Header with auth navbar
│           └── footer.php            # Footer
├── database/
│   └── hospital.sql                  # Database schema + sample data
├── public/
│   ├── index.php                     # Front controller (routing)
│   └── assets/
│       ├── styles.css                # Custom CSS
│       └── script.js                 # JavaScript utilities
├── Week8.md                          # Complete documentation
├── TUGAS.md                          # Assignment specifications
└── README.md                         # This file
```

---

## 🎯 Features

### 1. Authentication System

- ✅ **Login**

  - Username or email support
  - Password verification with bcrypt
  - Remember me option
  - Rate limiting (5 attempts, 15 min lockout)
  - Session security
  - Flash messages

- ✅ **Registration**

  - Username, email, full name, password
  - Input validation
  - Unique checks
  - Auto-login after registration
  - Default role: receptionist

- ✅ **Logout**
  - Clear session
  - Destroy cookies
  - Redirect to login

### 2. Authorization System

- ✅ **Role-Based Access Control (RBAC)**

  - 3 roles: admin, doctor, receptionist
  - Permission system (e.g., `patients.view`)
  - Dynamic UI based on roles
  - Middleware protection

- ✅ **Permissions by Role**

  **Admin** (`*` = all permissions):

  - Users: create, view, update, delete
  - Doctors: create, view, update, delete
  - Patients: create, view, update, delete
  - Appointments: create, view, update, delete
  - Audit logs: view

  **Doctor**:

  - Appointments: view, update
  - Patients: view, create, update

  **Receptionist**:

  - Appointments: view, create, update
  - Patients: view, create, update

### 3. Role-Based Dashboard

- ✅ **Admin Dashboard**

  - User statistics by role
  - Total patients/appointments/doctors
  - Quick actions: create user, view audit logs
  - User management preview

- ✅ **Doctor Dashboard**

  - My appointments today
  - Patient statistics
  - Quick schedule view
  - Upcoming appointments

- ✅ **Receptionist Dashboard**
  - Total patients/appointments
  - Latest appointments
  - Quick actions: create appointment, create patient

### 4. Security Features

- ✅ Password hashing (bcrypt)
- ✅ Session security (httponly, regeneration)
- ✅ CSRF protection ready
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS prevention (htmlspecialchars)
- ✅ Rate limiting on login
- ✅ Input validation
- ✅ Soft delete support

---

## 🏗️ Design Patterns Used

### 1. **Singleton Pattern**

**Class:** `Auth`, `Database`

**Purpose:** Ensure only one instance exists globally

```php
$auth = Auth::getInstance();
$db = Database::getInstance();
```

### 2. **Repository Pattern**

**Class:** `UserRepository`

**Purpose:** Abstract data access, separate from business logic

```php
$userRepo = new UserRepository();
$user = $userRepo->findById(1);
```

### 3. **Middleware Pattern**

**Class:** `AuthMiddleware`

**Purpose:** Filter requests before reaching controller

```php
$this->middleware->requireAuth();
$this->middleware->requireRole('admin');
```

### 4. **Factory Method**

**Method:** `User::fromArray()`

**Purpose:** Create objects from data arrays

```php
$user = User::fromArray($row);
```

### 5. **Model Pattern**

**Class:** `User`

**Purpose:** Encapsulate domain logic and data

```php
if ($user->isAdmin()) {
    // Show admin menu
}
```

---

## 🧪 Testing Guide

### 1. Test Authentication

```
✅ Login dengan admin → Success
✅ Login dengan wrong password → Error
✅ Login 6x wrong → Locked out 15 min
✅ Register new user → Auto-login
✅ Logout → Redirect to login
```

### 2. Test Authorization

```
✅ Login as receptionist → Try access /users → Access denied
✅ Login as doctor → See only view/update buttons
✅ Login as admin → See all buttons including delete
✅ Guest → Try access /dashboard → Redirect to login
```

### 3. Test Role-Based Dashboard

```
✅ Admin sees: User statistics, manage users
✅ Doctor sees: My appointments, patient list
✅ Receptionist sees: Create appointment, patient list
```

### 4. Test Session

```
✅ Login → Close browser → Reopen → Still logged in (if remember me)
✅ Login → Wait 30 min → Session timeout
✅ Login → Logout → Cannot access protected pages
```

---

## 🐛 Troubleshooting

### Database Connection Failed

```
Error: Database connection failed
```

**Solution:**

- Check MySQL is running
- Verify database name: `hospital_week8`
- Check credentials in `app/config.php`
- Import `database/hospital.sql`

### 404 Not Found

```
Error: 404 - Page Not Found
```

**Solution:**

- Check URL: `http://localhost/MATERI-ASDOS/Week8/public/`
- Verify Apache is running
- Check `.htaccess` if using mod_rewrite

### Session Not Working

```
Error: User not staying logged in
```

**Solution:**

- Check `session_start()` called
- Verify session cookie settings
- Check browser accepts cookies

### Password Not Matching

```
Error: Invalid credentials
```

**Solution:**

- Use demo credentials exactly
- Password is case-sensitive: `password123`
- Try re-import database

---

## 📖 Learning Resources

**Week8.md** - Complete documentation dengan:

- Penjelasan konsep (Authentication vs Authorization)
- Design patterns dengan examples
- Security best practices
- Code snippets lengkap
- Architecture overview

**TUGAS.md** - Assignment dengan:

- Requirements detail
- Grading rubric
- Deliverables checklist
- Tips & tricks

---

## 🎓 Key Concepts

### OOP Principles Applied:

1. **Encapsulation**

   - Private properties in User model
   - Getters/setters for controlled access

2. **Single Responsibility**

   - Auth → Authentication only
   - UserRepository → Data access only
   - AuthMiddleware → Route protection only

3. **Dependency Injection**

   - Pass dependencies via constructor
   - Easier testing and flexibility

4. **Composition over Inheritance**
   - Middleware uses Auth instance
   - Controller uses Repository instance

### Security Principles Applied:

1. **Defense in Depth**

   - Multiple layers of security
   - Middleware + Controller checks

2. **Principle of Least Privilege**

   - Users only get minimum necessary permissions
   - RBAC enforcement

3. **Never Trust User Input**

   - Validate everything
   - Prepared statements
   - Input sanitization

4. **Secure by Default**
   - Middleware protection
   - Session security settings
   - Password hashing automatic

---

## 📞 Support

Jika ada pertanyaan atau issue:

1. Check `Week8.md` untuk penjelasan lengkap
2. Review code comments
3. Test dengan demo credentials
4. Contact dosen via email/Slack

---

## 🚀 Next Steps

After completing Week 8:

- **Week 9:** Advanced RBAC, Permissions table
- **Week 10:** API Authentication (JWT)
- **Week 11:** Two-Factor Authentication (2FA)
- **Week 12:** OAuth & Social Login

---

## 📝 Notes

- All passwords hashed with bcrypt (PASSWORD_DEFAULT)
- Session lifetime: 2 hours
- Login lockout: 5 attempts / 15 minutes
- CSRF protection ready (implement in Week 9)
- Audit logging ready (implement in assignment)

---

## ✨ Credits

**Course:** Pemrograman Lanjut  
**Week:** 8 - Authentication & Authorization  
**Focus:** 80% OOP, 20% Security  
**Year:** 2025

---

**Happy Learning! 🎉**

Master OOP patterns dan security best practices untuk membangun aplikasi yang robust dan secure!
