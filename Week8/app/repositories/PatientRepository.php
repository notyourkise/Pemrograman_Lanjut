<?php

/**
 * Patient Repository
 * Handles all database operations for patients
 */
class PatientRepository
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find patient by ID
     */
    public function findById(int $id): ?Patient
    {
        $query = "SELECT * FROM patients WHERE id = ? LIMIT 1";
        $result = $this->db->query($query, [$id]);
        
        if ($result && count($result) > 0) {
            return new Patient($result[0]);
        }
        
        return null;
    }
    
    /**
     * Get all patients
     */
    public function findAll(): array
    {
        $query = "SELECT * FROM patients ORDER BY name ASC";
        $results = $this->db->query($query);
        
        $patients = [];
        foreach ($results as $row) {
            $patients[] = new Patient($row);
        }
        
        return $patients;
    }
    
    /**
     * Create a new patient
     */
    public function create(array $data): Patient
    {
        $query = "INSERT INTO patients (name, gender, dob, phone, address, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        
        $params = [
            $data['name'],
            $data['gender'],
            $data['dob'],
            $data['phone'],
            $data['address']
        ];
        
        $patientId = $this->db->insert($query, $params);
        
        if (!$patientId) {
            throw new Exception('Failed to create patient');
        }
        
        return $this->findById($patientId);
    }
    
    /**
     * Update a patient
     */
    public function update(int $id, array $data): bool
    {
        $query = "UPDATE patients 
                  SET name = ?, gender = ?, dob = ?, phone = ?, address = ?, updated_at = NOW()
                  WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['gender'],
            $data['dob'],
            $data['phone'],
            $data['address'],
            $id
        ];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Delete a patient
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM patients WHERE id = ?";
        return $this->db->execute($query, [$id]);
    }
    
    /**
     * Search patients by name or phone
     */
    public function search(string $keyword): array
    {
        $query = "SELECT * FROM patients 
                  WHERE name LIKE ? OR phone LIKE ?
                  ORDER BY name ASC";
        
        $searchTerm = '%' . $keyword . '%';
        $results = $this->db->query($query, [$searchTerm, $searchTerm]);
        
        $patients = [];
        foreach ($results as $row) {
            $patients[] = new Patient($row);
        }
        
        return $patients;
    }
    
    /**
     * Check if patient with phone already exists
     */
    public function existsByPhone(string $phone, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) as count FROM patients WHERE phone = ?";
        $params = [$phone];
        
        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->query($query, $params);
        return $result[0]['count'] > 0;
    }
}
