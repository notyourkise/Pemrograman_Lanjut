-- =====================================================
-- Week 8: Authentication & Authorization System
-- Hospital Management System Database
-- =====================================================

-- Drop existing database if exists
DROP DATABASE IF EXISTS hospital_week8;
CREATE DATABASE hospital_week8;
USE hospital_week8;

-- =====================================================
-- Table: users (Authentication & Authorization)
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'doctor', 'receptionist') NOT NULL DEFAULT 'receptionist',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: departments
-- =====================================================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: doctors (linked to users)
-- =====================================================
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100),
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: patients
-- =====================================================
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Laki-laki', 'Perempuan') NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    email VARCHAR(100),
    blood_type ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_name (name),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: appointments
-- =====================================================
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    reason TEXT,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: audit_logs (Track user actions)
-- =====================================================
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NULL,
    old_values TEXT NULL,
    new_values TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERT DUMMY DATA
-- =====================================================

-- Insert Departments
INSERT INTO departments (name, description) VALUES
('Cardiology', 'Heart and cardiovascular system'),
('Neurology', 'Brain and nervous system'),
('Pediatrics', 'Medical care for children'),
('Orthopedics', 'Bones, joints, and muscles'),
('Dermatology', 'Skin conditions and treatments');

-- Insert Users with different roles
-- Password for all users: "password123" (hashed with password_hash())
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES
-- Admin users
('admin', 'admin@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Utama', 'admin', TRUE),
('admin2', 'admin2@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Dua', 'admin', TRUE),

-- Doctor users (EACH DOCTOR MUST HAVE A USER ACCOUNT)
('dr.john', 'dr.john@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. John Smith', 'doctor', TRUE),
('dr.sarah', 'dr.sarah@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Sarah Johnson', 'doctor', TRUE),
('dr.michael', 'dr.michael@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Michael Brown', 'doctor', TRUE),
('dr.budi', 'dr.budi@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Budi Santoso', 'doctor', TRUE),

-- Receptionist users
('receptionist', 'receptionist@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Receptionist Satu', 'receptionist', TRUE),
('receptionist2', 'receptionist2@hospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Receptionist Dua', 'receptionist', TRUE);

-- Insert Doctors (SYNCHRONIZED with users table via user_id)
-- Each doctor MUST be linked to a user account for login access
INSERT INTO doctors (user_id, name, specialization, phone, email, department_id) VALUES
(3, 'Dr. John Smith', 'Cardiologist', '081234567890', 'dr.john@hospital.com', 1),
(4, 'Dr. Sarah Johnson', 'Neurologist', '081234567891', 'dr.sarah@hospital.com', 2),
(5, 'Dr. Michael Brown', 'Pediatrician', '081234567892', 'dr.michael@hospital.com', 3),
(6, 'Dr. Budi Santoso', 'Orthopedic Surgeon', '081234567893', 'dr.budi@hospital.com', 1);

-- Insert Patients (20 dummy patients)
INSERT INTO patients (name, date_of_birth, gender, phone, address, email, blood_type) VALUES
('Ahmad Fauzi', '1985-03-15', 'Laki-laki', '081234561001', 'Jl. Merdeka No. 10, Jakarta', 'ahmad.fauzi@email.com', 'A+'),
('Siti Nurhaliza', '1990-07-22', 'Perempuan', '081234561002', 'Jl. Sudirman No. 45, Bandung', 'siti.nur@email.com', 'B+'),
('Budi Santoso', '1978-11-30', 'Laki-laki', '081234561003', 'Jl. Gatot Subroto No. 88, Surabaya', 'budi.santoso@email.com', 'O+'),
('Dewi Lestari', '1995-05-18', 'Perempuan', '081234561004', 'Jl. Asia Afrika No. 12, Yogyakarta', 'dewi.lestari@email.com', 'AB+'),
('Eko Prasetyo', '1982-09-25', 'Laki-laki', '081234561005', 'Jl. Diponegoro No. 77, Semarang', 'eko.prasetyo@email.com', 'A-'),
('Fitri Handayani', '1988-12-10', 'Perempuan', '081234561006', 'Jl. Ahmad Yani No. 33, Medan', 'fitri.handayani@email.com', 'B-'),
('Gunawan Wijaya', '1975-04-08', 'Laki-laki', '081234561007', 'Jl. Pahlawan No. 56, Makassar', 'gunawan.wijaya@email.com', 'O-'),
('Hana Pertiwi', '1992-08-14', 'Perempuan', '081234561008', 'Jl. Veteran No. 21, Palembang', 'hana.pertiwi@email.com', 'AB-'),
('Irfan Hakim', '1980-01-27', 'Laki-laki', '081234561009', 'Jl. Imam Bonjol No. 99, Denpasar', 'irfan.hakim@email.com', 'A+'),
('Julia Rahma', '1993-06-19', 'Perempuan', '081234561010', 'Jl. Gajah Mada No. 44, Malang', 'julia.rahma@email.com', 'B+'),
('Kurniawan Adi', '1987-10-03', 'Laki-laki', '081234561011', 'Jl. Hayam Wuruk No. 67, Batam', 'kurniawan.adi@email.com', 'O+'),
('Lina Marlina', '1991-02-11', 'Perempuan', '081234561012', 'Jl. Supratman No. 15, Banjarmasin', 'lina.marlina@email.com', 'A+'),
('Made Wira', '1983-07-29', 'Laki-laki', '081234561013', 'Jl. Kartini No. 82, Manado', 'made.wira@email.com', 'B+'),
('Nina Sari', '1994-11-05', 'Perempuan', '081234561014', 'Jl. Cut Nyak Dien No. 38, Pekanbaru', 'nina.sari@email.com', 'AB+'),
('Oki Setiawan', '1979-03-21', 'Laki-laki', '081234561015', 'Jl. RA Kartini No. 50, Jambi', 'oki.setiawan@email.com', 'O-'),
('Putri Ayu', '1996-09-17', 'Perempuan', '081234561016', 'Jl. Panglima Sudirman No. 23, Pontianak', 'putri.ayu@email.com', 'A-'),
('Rudi Hermawan', '1981-05-13', 'Laki-laki', '081234561017', 'Jl. Jenderal Sudirman No. 71, Samarinda', 'rudi.hermawan@email.com', 'B-'),
('Sari Indah', '1989-12-24', 'Perempuan', '081234561018', 'Jl. Diponegoro No. 29, Balikpapan', 'sari.indah@email.com', 'O+'),
('Toni Kurniawan', '1986-08-06', 'Laki-laki', '081234561019', 'Jl. Pemuda No. 66, Banda Aceh', 'toni.kurniawan@email.com', 'A+'),
('Umi Kalsum', '1997-04-02', 'Perempuan', '081234561020', 'Jl. Moh Hatta No. 41, Padang', 'umi.kalsum@email.com', 'AB+');

-- Insert Appointments (15 dummy appointments)
INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason, status, notes, created_by) VALUES
(1, 1, '2025-11-01 09:00:00', 'Chest pain and irregular heartbeat', 'scheduled', 'First visit - ECG required', 6),
(2, 2, '2025-11-01 10:30:00', 'Severe headaches and dizziness', 'scheduled', 'MRI scan scheduled', 6),
(3, 3, '2025-11-01 13:00:00', 'Child vaccination check-up', 'scheduled', 'Routine immunization', 7),
(4, 1, '2025-11-02 08:30:00', 'Follow-up cardiac consultation', 'scheduled', 'Review test results', 6),
(5, 2, '2025-11-02 11:00:00', 'Numbness in left arm', 'scheduled', 'Neurological examination needed', 7),
(6, 3, '2025-11-02 14:00:00', 'Fever and cough in toddler', 'scheduled', 'Possible respiratory infection', 6),
(7, 1, '2025-11-03 09:30:00', 'High blood pressure monitoring', 'scheduled', 'Monthly check-up', 7),
(8, 2, '2025-11-03 10:00:00', 'Memory problems', 'scheduled', 'Cognitive assessment', 6),
(9, 3, '2025-11-03 15:00:00', 'Growth development check', 'scheduled', '6-month milestone', 7),
(10, 1, '2025-10-25 09:00:00', 'Heart palpitations', 'completed', 'Prescribed beta-blockers', 6),
(11, 2, '2025-10-25 11:30:00', 'Migraine treatment', 'completed', 'Medication adjusted', 7),
(12, 3, '2025-10-26 08:00:00', 'Vaccination schedule', 'completed', 'All vaccines administered', 6),
(13, 1, '2025-10-27 14:00:00', 'Cardiac consultation', 'cancelled', 'Patient requested reschedule', 7),
(14, 2, '2025-10-28 10:00:00', 'Seizure follow-up', 'completed', 'Condition stable', 6),
(15, 3, '2025-10-28 13:30:00', 'Asthma check-up', 'completed', 'Inhaler prescription renewed', 7);

-- =====================================================
-- SAMPLE AUDIT LOG ENTRIES
-- =====================================================
INSERT INTO audit_logs (user_id, action, table_name, record_id, new_values, ip_address) VALUES
(1, 'CREATE', 'users', 6, '{"username":"receptionist","role":"receptionist"}', '127.0.0.1'),
(1, 'CREATE', 'users', 7, '{"username":"receptionist2","role":"receptionist"}', '127.0.0.1'),
(6, 'CREATE', 'appointments', 1, '{"patient_id":1,"doctor_id":1}', '127.0.0.1'),
(7, 'CREATE', 'appointments', 2, '{"patient_id":2,"doctor_id":2}', '127.0.0.1');

-- =====================================================
-- VIEWS FOR EASIER QUERYING
-- =====================================================

-- Active users only
CREATE VIEW active_users AS
SELECT id, username, email, full_name, role, last_login, created_at
FROM users
WHERE is_active = TRUE AND deleted_at IS NULL;

-- Appointments with full details
CREATE VIEW appointment_details AS
SELECT 
    a.id,
    a.appointment_date,
    a.status,
    p.name AS patient_name,
    p.phone AS patient_phone,
    d.name AS doctor_name,
    d.specialization,
    dept.name AS department,
    u.full_name AS created_by_user,
    a.created_at
FROM appointments a
JOIN patients p ON a.patient_id = p.id
JOIN doctors d ON a.doctor_id = d.id
LEFT JOIN departments dept ON d.department_id = dept.id
LEFT JOIN users u ON a.created_by = u.id
WHERE a.deleted_at IS NULL;

-- =====================================================
-- Display credentials for testing
-- =====================================================
SELECT '=== LOGIN CREDENTIALS (Password: password123) ===' AS '';
SELECT username, email, role FROM users WHERE deleted_at IS NULL ORDER BY role, username;
