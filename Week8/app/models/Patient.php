<?php

/**
 * Patient Model
 * Represents a patient in the hospital system
 */
class Patient
{
    private $id;
    private $name;
    private $gender;
    private $dob;
    private $phone;
    private $address;
    private $created_at;
    private $updated_at;
    
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->gender = $data['gender'] ?? null;
        $this->dob = $data['dob'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
    
    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function getGender(): ?string
    {
        return $this->gender;
    }
    
    public function getDob(): ?string
    {
        return $this->dob;
    }
    
    // Alias for compatibility
    public function getDateOfBirth(): ?string
    {
        return $this->dob;
    }
    
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    
    public function getAddress(): ?string
    {
        return $this->address;
    }
    
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }
    
    /**
     * Get formatted gender
     */
    public function getGenderLabel(): string
    {
        return $this->gender === 'M' ? 'Male' : 'Female';
    }
    
    /**
     * Get formatted age from date of birth
     */
    public function getAge(): ?int
    {
        if (!$this->dob) {
            return null;
        }
        
        $dob = new DateTime($this->dob);
        $now = new DateTime();
        return $now->diff($dob)->y;
    }
    
    /**
     * Get formatted date of birth
     */
    public function getFormattedDob(): ?string
    {
        if (!$this->dob) {
            return null;
        }
        
        return date('d M Y', strtotime($this->dob));
    }
}
