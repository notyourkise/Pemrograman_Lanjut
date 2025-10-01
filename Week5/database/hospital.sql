-- Database: materi_rs
CREATE DATABASE IF NOT EXISTS materi_rs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE materi_rs;

-- Drop tables if exist (dev only)
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS departments;
SET FOREIGN_KEY_CHECKS=1;

-- Departments
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Doctors
CREATE TABLE doctors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  department_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  specialization VARCHAR(100) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_doctor_department FOREIGN KEY (department_id) REFERENCES departments(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Patients
CREATE TABLE patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  gender ENUM('M','F') NOT NULL DEFAULT 'M',
  dob DATE NULL,
  phone VARCHAR(20) NULL,
  address TEXT NULL,
  deleted_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Appointments (n-n between doctors and patients over time)
CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT NOT NULL,
  patient_id INT NOT NULL,
  schedule DATETIME NOT NULL,
  notes VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_appt_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_appt_patient FOREIGN KEY (patient_id) REFERENCES patients(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX idx_schedule (schedule)
) ENGINE=InnoDB;

-- Seed data
INSERT INTO departments (name) VALUES
('Cardiology'), ('Neurology'), ('Pediatrics');

INSERT INTO doctors (department_id, name, specialization) VALUES
(1, 'Dr. Andi', 'Heart Failure'),
(1, 'Dr. Sari', 'Interventional'),
(2, 'Dr. Budi', 'Stroke'),
(3, 'Dr. Rina', 'General Pediatrics');

INSERT INTO patients (name, gender, dob, phone, address) VALUES
('Ahmad', 'M', '1990-05-01', '+62 812-0001', 'Jl. Merdeka 1'),
('Siti', 'F', '1995-10-12', '+62 812-0002', 'Jl. Anggrek 5'),
('Dewi', 'F', NULL, NULL, NULL);

INSERT INTO appointments (doctor_id, patient_id, schedule, notes) VALUES
(1, 1, '2025-10-10 09:00:00', 'Kontrol rutin'),
(3, 2, '2025-10-12 14:30:00', 'Konsultasi awal');
