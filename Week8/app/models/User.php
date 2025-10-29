<?php

/**
 * Week 8: User Model
 * Represents a user in the system with authentication capabilities
 */

class User
{
    private $id;
    private $username;
    private $email;
    private $password;
    private $fullName;
    private $role;
    private $isActive;
    private $lastLogin;
    private $createdAt;
    private $updatedAt;

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getFullName() { return $this->fullName; }
    public function getRole() { return $this->role; }
    public function isActive() { return $this->isActive; }
    public function getLastLogin() { return $this->lastLogin; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setFullName($fullName) { $this->fullName = $fullName; }
    public function setRole($role) { $this->role = $role; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
    public function setLastLogin($lastLogin) { $this->lastLogin = $lastLogin; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }

    /**
     * Create User from database row
     * 
     * @param array $row
     * @return User
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
        $user->setCreatedAt($row['created_at'] ?? null);
        $user->setUpdatedAt($row['updated_at'] ?? null);
        
        return $user;
    }

    /**
     * Convert User to array
     * 
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->fullName,
            'role' => $this->role,
            'is_active' => $this->isActive,
            'last_login' => $this->lastLogin,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    /**
     * Hash a password
     * 
     * @param string $password
     * @return string
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password against hash
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if user has specific role
     * 
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return $this->role === $roles;
    }

    /**
     * Check if user is admin
     * 
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is doctor
     * 
     * @return bool
     */
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if user is receptionist
     * 
     * @return bool
     */
    public function isReceptionist()
    {
        return $this->role === 'receptionist';
    }

    /**
     * Get role label
     * 
     * @return string
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
}
