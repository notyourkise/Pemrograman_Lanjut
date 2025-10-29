# Week 8: Quick Start Guide

## 🚀 5-Minute Setup

### 1. Import Database (2 minutes)

```bash
# Start MySQL
# Via MySQL CLI:
mysql -u root -p
source C:/xampp/htdocs/MATERI-ASDOS/Week8/database/hospital.sql
exit

# Or via phpMyAdmin:
# http://localhost/phpmyadmin
# Import: database/hospital.sql
```

### 2. Access Application (30 seconds)

```
http://localhost/MATERI-ASDOS/Week8/public/
```

### 3. Login & Test (2 minutes)

**Try each role:**

✅ **Admin** → `admin` / `password123`

- Full dashboard with user statistics
- Can access /users, /audit-logs
- See delete buttons

✅ **Doctor** → `dr.john` / `password123`

- Doctor dashboard
- Can view/update appointments & patients
- No delete buttons

✅ **Receptionist** → `receptionist` / `password123`

- Receptionist dashboard
- Can create appointments & patients
- Limited access

---

## 📖 What to Read First

### For Students:

1. **README.md** - Setup & overview (5 min)
2. **Week8.md** - Complete theory (30 min)
3. **TUGAS.md** - Assignment (10 min)

### For Instructors:

1. **IMPLEMENTATION_SUMMARY.md** - What's built (10 min)
2. **PPT_OUTLINE.md** - Teaching slides (5 min)
3. **Week8.md** - Source material (20 min)

---

## 🎯 Key Files to Explore

### OOP Patterns:

```
app/helpers/Auth.php           → Singleton Pattern
app/repositories/UserRepository.php → Repository Pattern
app/models/User.php            → Model Pattern
app/middleware/AuthMiddleware.php   → Middleware Pattern
```

### Security:

```
app/helpers/Auth.php           → Password hashing, rate limiting
app/controllers/AuthController.php → Input validation
app/config.php                 → RBAC permissions
```

### Views:

```
app/views/auth/login.php       → Login form
app/views/dashboard/index.php  → Role-based dashboard
app/views/layout/header.php    → Dynamic navbar
```

---

## 🧪 Testing Checklist

Quick tests to verify everything works:

### Authentication:

- [ ] Login with admin → ✅ Success
- [ ] Login with wrong password → ❌ Error message
- [ ] Login 6x wrong → 🔒 Locked out 15 min
- [ ] Register new user → ✅ Auto-login
- [ ] Logout → ✅ Redirect to login

### Authorization:

- [ ] Login as receptionist → Try `/users` → ❌ Access denied
- [ ] Login as doctor → Delete button hidden → ✅
- [ ] Login as admin → All buttons visible → ✅

### Dashboard:

- [ ] Admin sees user statistics → ✅
- [ ] Doctor sees today's schedule → ✅
- [ ] Receptionist sees quick actions → ✅

---

## 💡 Common Issues

### Database error?

```
Solution: Check database name is 'hospital_week8'
```

### 404 error?

```
Solution: Access via /Week8/public/ not just /Week8/
```

### Login not working?

```
Solution: Check credentials are exactly: admin / password123
```

### Session not persisting?

```
Solution: Check browser accepts cookies
```

---

## 📞 Need Help?

1. Check **README.md** troubleshooting section
2. Review **Week8.md** for concepts
3. Look at code comments
4. Contact instructor

---

## 🎓 Assignment Next Steps

After testing the base system:

1. **Read TUGAS.md** - Understand requirements
2. **Copy Week8 folder** - Don't modify original
3. **Implement audit logging** - Main task
4. **Test each requirement** - Use checklist
5. **Document your work** - README.md
6. **Submit to GitHub** - With screenshots

**Estimated Time:** 8-10 hours

**Deadline:** 1 week from praktikum

**Points:** 100 + 20 bonus

---

## ✨ Ready to Go!

Everything is set up and ready for:

- ✅ Teaching (PPT + Week8.md)
- ✅ Practice (Complete codebase)
- ✅ Assignment (TUGAS.md)

**Good luck! 🚀**
